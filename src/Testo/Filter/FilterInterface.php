<?php
namespace Testo\Filter;

interface FilterInterface
{
    /**
     * @param array $codeLines
     *
     * @return array
     */
    public function filter(array $codeLines);
}
