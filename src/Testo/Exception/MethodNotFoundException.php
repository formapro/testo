<?php
namespace Testo\Exception;

class MethodNotFoundException extends TestoLogicException
{

    /**
     * @param string $line
     */
    public function __construct($line)
    {
        parent::__construct('Method not found', $line);
    }
}