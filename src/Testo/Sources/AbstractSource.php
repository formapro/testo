<?php
namespace Testo\Sources;

use Testo\Testo;

abstract class AbstractSource
{
    /**
     * @var \Testo\Testo
     */
    protected $testo;

    /**
     * @param Testo $testo
     */
    public function __construct(Testo $testo)
    {
        $this->testo = $testo;
    }

    /**
     * @param string $line
     * @return array
     */
    public abstract function getContent($line);

}
