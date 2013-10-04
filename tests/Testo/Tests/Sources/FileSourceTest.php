<?php
namespace Testo\Tests\Sources;

use Testo\Sources\FileSource;
use Testo\Testo;

class FileSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenFile()
    {
        $this->assertTrue(true);
        $testoStub = $this->createTestoStub();
        $line = '@testo config.yml';
        $expectedContent = array(
            "foo:\n",
            "    - fooKey: fooValue\n",
            "    - barKey: barValue\n",
            "\n",
            "baz: baz"
        );
        $source = new FileSource($testoStub);
        $result = $source->getContent($line);
        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Testo
     */
    protected function createTestoStub()
    {
        $stub = $this->getMock('Testo\Testo', array('getRootDir'));
        $stub->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(realpath(__DIR__ . '/../files')));
        return $stub;
    }

}