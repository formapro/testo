<?php
namespace Testo\Exception;

class FileNotFoundException extends TestoLogicException
{
    /**
     * @param string $line
     */
    public function __construct($line)
    {
        parent::__construct('File not found', $line);
    }
}