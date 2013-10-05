<?php
namespace Testo\Sources;

interface RootDirAwareInterface
{
    /**
     * @return string
     */
    public function getRootDir();
}