<?php
namespace Testo;

class Testo
{
    protected $rootDir;
    
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }
    
    public function generate($templateFile, $documentFile)
    {    
        $document = $this->replaceMethods(file_get_contents($templateFile));
        $document = $this->replaceFiles($document);

        file_put_contents($documentFile, $document);
    }
    
    protected function replaceMethods($template)
    {
        $document = $template;
        
        $placeholders =array();
        if (preg_match_all('/\{\{\s*?testo:([^\s]+)\:\:([^\s]+)\s*?\}\}/', $template, $placeholders)) {
            foreach($placeholders[1] as $index => $class) {
                $method = $placeholders[2][$index];
                $placeholder = $placeholders[0][$index];

                $methodCode = $this->doGenerateMethodCode($class, $method);

                $document = str_replace($placeholder, $methodCode, $document);
            }
        }

        return $document;
    }
    
    protected function replaceFiles($template)
    {
        $document = $template;

        $placeholders =array();
        if (preg_match_all('/\{\{\s*?testo:(.+\/.+)\}\}/', $template, $placeholders)) {
            foreach($placeholders[1] as $index => $relativePath) {
                $absolutePath = $this->rootDir.'/'.$relativePath;
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
        $methodCode = $this->addSourceComment($class, $method, $methodCode);
        
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
        
        $hasInstruction = false !== strpos(implode("", $methodCodeLines), '//@testo');
        $methodCode = '';
        if ($hasInstruction) {
            $blockStarted = false;
            foreach($methodCodeLines as $methodCodeLine) {
                if (false !== strpos($methodCodeLine, '//@testo:end')) {
                    $blockStarted = false;
                }
                
                if ($blockStarted) {
                    $methodCode .= $methodCodeLine;
                }
                
                if (false !== strpos($methodCodeLine, '//@testo:start')) {
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
        foreach(range($rm->getStartLine(), $rm->getEndLine() - 1) as $line) {
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
    
    protected function addSourceComment($class, $method, $methodCode)
    {
        $methodCode = "//Source: $class::$method()\n\n".$methodCode;

        return $methodCode;
    }
}