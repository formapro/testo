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

    public function generate($templateFile, $documentFile)
    {
        $document = $this->replaceMethods(file_get_contents($templateFile));
        $document = $this->replaceFiles($document, dirname($templateFile));

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

                $document = str_replace($placeholder, file_get_contents($absolutePath), $document);
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

        foreach ($methodCodeLines as &$methodCodeLine) {
            if (preg_match($this->sourceSourceTagRegExp, $methodCodeLine, $placeholders)) {
                $methodCodeLine = str_replace(
                    $placeholders[0],
                    '//Source: ' . $rm->getDeclaringClass()->getName() . '::' . $rm->getName() . '()',
                    $methodCodeLine
                );
            }
            if (preg_match($this->sourceUncommentTagRegExp, $methodCodeLine, $placeholders)) {
                $methodCodeLine = str_replace($placeholders[0], '', $methodCodeLine);
            }
        }

        $hasInstruction = false !== strpos(implode("", $methodCodeLines), '//@testo');

        $methodCode = '';
        if ($hasInstruction) {
            $blockStarted = false;
            foreach ($methodCodeLines as $methodCodeLine) {
                if (preg_match($this->sourceEndBlockTagRegExp, $methodCodeLine, $placeholders)) {
                    $blockStarted = false;
                }

                if ($blockStarted) {
                    $methodCode .= $methodCodeLine;
                }

                if (preg_match($this->sourceBeginBlockTagRegExp, $methodCodeLine, $placeholders)) {
                    $blockStarted = true;
                }
            }
        } else {
            $methodCode = implode("", $methodCodeLines);
        }

        return $methodCode;
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
}
