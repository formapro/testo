<?php
namespace Testo\Sources;

class ClassSource implements SourceInterface
{
    protected $classTagRegExp = '/^\s*@testo\s+([^\s]+)\s*$/m';

    /**
     * @param string $line
     * @return array
     */
    public function getContent($line)
    {
        $placeholders = array();
        if (preg_match($this->classTagRegExp, $line, $placeholders)) {
            $className = $placeholders[1];
            if (class_exists($className)) {
                $rc = new \ReflectionClass($className);
                return $this->getClassCode($rc);
            }
        }

        return false;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    protected function getClassCode(\ReflectionClass $reflectionClass)
    {
        $fileLines = file($reflectionClass->getFileName());
        $fileStartLine = $reflectionClass->getStartLine();
        $fileEndLine = $reflectionClass->getEndLine();
        $codeLines = array();
        for ($i = $fileStartLine - 1; $i < $fileEndLine; $i++) {
            $codeLines[] = $fileLines[$i];
        }

        return $codeLines;
    }

}
