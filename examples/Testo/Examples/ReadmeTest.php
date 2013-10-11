<?php
namespace Testo\Examples;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function whatsInside()
    {
        //@testo {
        $testo = new \Testo\Testo();

        $testo->generate(__DIR__ . '/README.md');
        //@testo }
        $this->assertFileEquals(__DIR__ . '/README.md.expected', __DIR__ . '/README.md');
    }
}
