<?php
namespace Testo\Source;

interface SourceInterface
{
    /**
     * @param string $line
     *
     * @return array
     */
    public function getContent($line);
}
