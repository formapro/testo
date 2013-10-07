<?php
namespace Testo\Source;

use Testo\Exception\SourceNotFoundException;

class MethodSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $methodTagRegExp = '/^\s*@testo\s+([^\s]+)\s+([^\s]+)\s*$/m';

    /**
     * {@inheritDoc}
     */
    public function getContent($line)
    {
        $placeholders = array();
        if (preg_match($this->methodTagRegExp, $line, $placeholders)) {

            $className = $placeholders[1];
            $methodName = $placeholders[2];

            try {
                $rm = new \ReflectionMethod($className, $methodName);

                return $this->getMethodCode($rm);
            } catch (\ReflectionException $e) {
                throw new SourceNotFoundException(sprintf("Method not found.\n\nLine is '%s'", $line), 0, $e);
            }

        }

        return false;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array
     */
    protected function getMethodCode(\ReflectionMethod $reflectionMethod)
    {
        $file = file($reflectionMethod->getFileName());
        $methodCodeLines = array();
        foreach (range($reflectionMethod->getStartLine(), $reflectionMethod->getEndLine() - 1) as $line) {
            $methodCodeLines[] = $file[$line];
        }

        if (trim($methodCodeLines[count($methodCodeLines) - 1]) == '}') {
            unset($methodCodeLines[count($methodCodeLines) - 1]);
        }

        if (trim($methodCodeLines[0]) == '{') {
            unset($methodCodeLines[0]);
        }

        return array_values($methodCodeLines);

    }
}
