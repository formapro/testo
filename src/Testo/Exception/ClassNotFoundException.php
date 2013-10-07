<?php
namespace Testo\Exception;

class ClassNotFoundException extends TestoLogicException
{

    /**
     * @param string $line
     */
    public function __construct($line)
    {
        parent::__construct('Class not found', $line);
    }

}