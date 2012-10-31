<?php
namespace Testo\Examples;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function whatsInside()
    {
        file_put_contents(__DIR__.'/README.md', '');
        //@testo:start
        //include autoload.

        $testo = new \Testo\Testo($rootDir = __DIR__);

        $testo->generate(__DIR__.'/README.md.template', __DIR__.'/README.md');
        //@testo:end
        $this->assertFileEquals(__DIR__.'/README.md.expected', __DIR__.'/README.md');
    }
}
