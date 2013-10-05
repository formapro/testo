<?php
namespace Testo\Sources;

interface SourceInterface
{
    /**
     * @param string $line
     * @return array
     */
    public function getContent($line);
}
