<?php
namespace Testo\Tests\Sources;

use Testo\Sources\FileSource;
use Testo\Sources\RootDirAwareInterface;

class FileSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenFile()
    {
        $this->assertTrue(true);
        $rootDirAwareStub = $this->createRootDirAwareStub();
        $line = '@testo config.yml';
        $expectedContent = array(
            "foo:\n",
            "    - fooKey: fooValue\n",
            "    - barKey: barValue\n",
            "\n",
            "baz: baz"
        );
        $source = new FileSource($rootDirAwareStub);
        $result = $source->getContent($line);
        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RootDirAwareInterface
     */
    protected function createRootDirAwareStub()
    {
        $stub = $this->getMock('Testo\Sources\RootDirAwareInterface', array('getRootDir'));
        $stub->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(realpath(__DIR__ . '/../files')));
        return $stub;
    }

}