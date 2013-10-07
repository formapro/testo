<?php
namespace Testo\Exception;

class TestoLogicException extends \LogicException
{
    /**
     * @param string $message
     * @param string $line
     */
    public function __construct($message, $line)
    {
        $message = sprintf("%s.\n\nLine is '%s'", $message, $line);
        parent::__construct($message);
    }
}