<?php
namespace Testo\Filters;

interface FilterInterface
{
    /**
     * @param array $codeLines
     * @return array
     */
    public function filter(array $codeLines);
}
