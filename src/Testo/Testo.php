<?php
namespace Testo;

class Testo
{
    public function generate($templateFile, $documentFile)
    {    
        $template = $document = file_get_contents($templateFile);

        $placeholders =array();
        if (preg_match_all('/\{\{\s*?testo:([^\s]+)\:\:([^\s]+)\s*?\}\}/', $template, $placeholders)) {
            foreach($placeholders[1] as $index => $class) {
                $method = $placeholders[2][$index];
                $placeholder = $placeholders[0][$index];
                
                $methodCode = $this->doGenerateMethodCode($class, $method);
                
                $document = str_replace($placeholder, $methodCode, $document);
            }
        }

        file_put_contents($documentFile, $document);
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