<?php
namespace Testo;

class Testo
{
    protected $templateMethodTagRegExp = '/^\s*@testo\s+([^\s]+)\s+([^\s]+)\s*$/m';
    protected $templateFileTagRegExp = '/^\s*@testo\s+([^\s]+)\s*$/m';

    protected $sourceBeginBlockTagRegExp = '|//\s*@testo\s*{\s*$|m';
    protected $sourceEndBlockTagRegExp = '|//\s*@testo\s*}\s*$|m';
    protected $sourceSourceTagRegExp = '|//\s*@testo\s+source|';

    protected $sourceUncommentTagRegExp = '|//\s*@testo\s+uncomment\s*|';
    protected $sourceBeginUncommentTagRegExp = '|(//\s*@testo\s+uncomment\s*{\s*)|';
    protected $sourceEndUncommentTagRegExp = '|(//\s*@testo\s+uncomment\s*}\s*)|';

    protected $templateClassTagRegExp = '/^\s*@testo\s+([^\s]+)\s*$/m';

    public function generate($templateFile, $documentFile)
    {
        $document = $this->replaceMethods(file_get_contents($templateFile));
        $document = $this->replaceFiles($document, dirname($templateFile));
        $document = $this->replaceClasses($document);

        file_put_contents($documentFile, $document);
    }

    protected function replaceMethods($template)
    {
        $document = $template;

        $placeholders = array();
        if (preg_match_all($this->templateMethodTagRegExp, $template, $placeholders)) {
            foreach ($placeholders[1] as $index => $class) {
                $method = $placeholders[2][$index];
                $placeholder = $placeholders[0][$index];

                $methodCode = $this->doGenerateMethodCode($class, $method);

                $document = str_replace($placeholder, $methodCode, $document);
            }
        }

        return $document;
    }

    protected function replaceFiles($template, $rootDir)
    {
        $document = $template;

        $placeholders = array();
        if (preg_match_all($this->templateFileTagRegExp, $template, $placeholders)) {
            foreach ($placeholders[1] as $index => $relativePath) {
                $absolutePath = $rootDir . '/' . $relativePath;
                $placeholder = $placeholders[0][$index];

                if (is_file($absolutePath)) {
                    $document = str_replace($placeholder, file_get_contents($absolutePath), $document);
                }
            }
        }

        return $document;
    }

    protected function replaceClasses($template)
    {
        $document = $template;

        $placeholders = array();
        if (preg_match_all($this->templateClassTagRegExp, $template, $placeholders)) {
            foreach ($placeholders[1] as $index => $className) {

                if (class_exists($className)) {
                    $code = $this->collectClassCode(new \ReflectionClass($className));
                    $placeholder = $placeholders[0][$index];
                    $document = str_replace($placeholder, $code, $document);
                }

//
//                if (is_file($absolutePath)) {
//                    $document = str_replace($placeholder, file_get_contents($absolutePath), $document);
//                }
            }
        }

        return $document;
    }

    protected function doGenerateMethodCode($class, $method)
    {
        $methodCode = $this->collectMethodCode(new \ReflectionMethod($class, $method));
        $methodCode = $this->filterMethodCode($methodCode);

        return $methodCode;
    }

    /**
     * @param \ReflectionMethod $rm
     *
     * @return string
     */
    protected function collectMethodCode(\ReflectionMethod $rm)
    {
        $methodCodeLines = $this->extractMethodLines($rm);
        $methodCodeLines = $this->uncomment($methodCodeLines);
        $methodCodeLines = $this->insertSource($methodCodeLines, $rm);
        $methodCodeLines = $this->leaveBlocksOnly($methodCodeLines);

        return implode("", $methodCodeLines);

    }

    protected function extractMethodLines(\ReflectionMethod $rm)
    {
        $file = file($rm->getFileName());
        $methodCodeLines = array();
        foreach (range($rm->getStartLine(), $rm->getEndLine() - 1) as $line) {
            $methodCodeLines[] = $file[$line];
        }

        return $methodCodeLines;
    }

    protected function filterMethodCode($methodCode)
    {
        $methodCode = preg_replace('/^\s*?\{/', '', $methodCode);
        $methodCode = preg_replace('/\}\s*?$/', '', $methodCode);
        $methodCode = ltrim($methodCode, "\n");
        $methodCode = rtrim($methodCode, "\n ");


        $placeholders = array();
        if (preg_match('/^(\s*?)[^\s]/', $methodCode, $placeholders)) {
            $methodCode = preg_replace("/^$placeholders[1]/", '', $methodCode);
            $methodCode = str_replace("\n$placeholders[1]", "\n", $methodCode);
        }

        return $methodCode;
    }

    protected function uncomment(array $methodCodeLines)
    {
        $commentStarted = false;
        $skipLine = false;

        $codeLinesWithoutMultiLineComments = array();
        foreach ($methodCodeLines as $methodCodeLine) {

            if (preg_match($this->sourceBeginUncommentTagRegExp, $methodCodeLine, $placeholders)) {
                $commentStarted = true;
                $skipLine = true;
            }

            if (preg_match($this->sourceEndUncommentTagRegExp, $methodCodeLine, $placeholders)) {
                $commentStarted = false;
                $skipLine = true;
            }

            if ($commentStarted) {
                $methodCodeLine = str_replace('//', '', $methodCodeLine);
            }

            if ($skipLine) {
                $skipLine = false;
            } else {
                $codeLinesWithoutMultiLineComments[] = $methodCodeLine;
            }

        }

        foreach ($codeLinesWithoutMultiLineComments as &$methodCodeLine) {
            if (preg_match($this->sourceUncommentTagRegExp, $methodCodeLine, $placeholders)) {
                $methodCodeLine = str_replace($placeholders[0], '', $methodCodeLine);
            }

        }

        return $codeLinesWithoutMultiLineComments;
    }

    protected function insertSource(array $methodCodeLines, \ReflectionMethod $reflectionMethod)
    {
        $source = $reflectionMethod->getDeclaringClass()->getName() . '::' . $reflectionMethod->getName() . '()';
        foreach ($methodCodeLines as &$methodCodeLine) {
            if (preg_match($this->sourceSourceTagRegExp, $methodCodeLine, $placeholders)) {
                $methodCodeLine = str_replace(
                    $placeholders[0],
                    '//Source: ' . $source,
                    $methodCodeLine
                );
            }
        }

        return $methodCodeLines;
    }

    protected function leaveBlocksOnly(array $methodCodeLines)
    {
        $blockStarted = false;
        $blockLines = array();
        foreach ($methodCodeLines as $methodCodeLine) {
            if (preg_match($this->sourceEndBlockTagRegExp, $methodCodeLine, $placeholders)) {
                $blockStarted = false;
            }

            if ($blockStarted) {
                $blockLines[] = $methodCodeLine;
            }

            if (preg_match($this->sourceBeginBlockTagRegExp, $methodCodeLine, $placeholders)) {
                $blockStarted = true;
            }
        }
        if (!$blockLines) {
            return $methodCodeLines;
        }

        return $blockLines;
    }

    public function collectClassCode(\ReflectionClass $reflectionClass)
    {
        $fileLines = file($reflectionClass->getFileName());
        $fileStartLine = $reflectionClass->getStartLine();
        $fileEndLine = $reflectionClass->getEndLine();
        $code = '';
        for ($i = $fileStartLine - 1; $i < $fileEndLine; $i++) {
            $code .= $fileLines[$i];
        }

        return $code;
    }
}
