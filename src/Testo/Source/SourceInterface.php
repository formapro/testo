<?php
namespace Testo\Source;

use Testo\Exception\SourceNotFoundException;

interface SourceInterface
{
    /**
     * @param string $line
     *
     * @return array
     * @throws SourceNotFoundException
     */
    public function getContent($line);
}
