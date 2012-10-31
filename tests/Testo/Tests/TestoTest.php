<?php
namespace Testo\Tests;

use Testo\Testo;

class TestoTest extends \PHPUnit_Framework_TestCase
{
    public function provideTestData()
    {
        return array(
            array(__DIR__.'/files/with_spaces.tpl', __DIR__.'/files/with_spaces.txt'),
            array(__DIR__.'/files/without_spaces.tpl', __DIR__.'/files/without_spaces.txt'),
            array(__DIR__.'/files/with_blocks.tpl', __DIR__.'/files/with_blocks.txt'),
            array(__DIR__.'/files/all_file.tpl', __DIR__.'/files/all_file.txt'),
        );
    }

    /**
     * @test
     * 
     * @dataProvider provideTestData
     */
    public function shouldGenerateExpectedDocumentFromTemplate($templateFile, $expectedFile)
    {
        $actualFile = tempnam(sys_get_temp_dir(), 'testo');
        
        $testo = new Testo($rootDir = __DIR__);
        $testo->generate($templateFile, $actualFile);
        
        $this->assertFileEquals($expectedFile, $actualFile);
    }
}