<?php
namespace Testo\Examples;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function whatsInside()
    {
        //@testo:start
        //include autoload.

        $testo = new \Testo\Testo();

        $testo->generate(__DIR__ . '/README.md');
        //@testo:end
        $this->assertFileEquals(__DIR__ . '/README.md.expected', __DIR__ . '/README.md');
    }
}
