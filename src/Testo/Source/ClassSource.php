<?php
namespace Testo\Source;

use Testo\Exception\ClassNotFoundException;

class ClassSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $classTagRegExp = '/^\s*@testo\s+([^\s\.]+)\s*$/m';

    /**
     * {@inheritDoc}
     *
     * @throws ClassNotFoundException
     */
    public function getContent($line)
    {
        $placeholders = array();
        if (preg_match($this->classTagRegExp, $line, $placeholders)) {
            $className = $placeholders[1];
            try {
                $rc = new \ReflectionClass($className);

                return $this->getClassCode($rc);
            } catch (\ReflectionException $e) {
                throw new ClassNotFoundException($line);
            }

        }

        return false;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    protected function getClassCode(\ReflectionClass $reflectionClass)
    {
        $fileLines = file($reflectionClass->getFileName());
        $fileStartLine = $reflectionClass->getStartLine();
        $fileEndLine = $reflectionClass->getEndLine();
        $codeLines = array_slice($fileLines, $fileStartLine - 1, $fileEndLine - $fileStartLine + 1);
        return $codeLines;
    }

}
