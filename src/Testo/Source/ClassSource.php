<?php
namespace Testo\Source;


use Testo\Exception\SourceNotFoundException;

class ClassSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $classTagRegExp = '/^\s*@testo\s+([^\s\.]+)\s*$/m';

    /**
     * {@inheritDoc}
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
                throw new SourceNotFoundException(sprintf("Class not found.\n\nLine is '%s'", $line), 0, $e);
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
