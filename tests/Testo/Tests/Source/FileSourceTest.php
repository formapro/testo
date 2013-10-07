<?php
namespace Testo\Tests\Source;

use Testo\Source\FileSource;
use Testo\Source\RootDirAwareInterface;

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
     * @test
     *
     * @expectedException \Testo\Exception\SourceNotFoundException
     * @expectedExceptionMessage File not found
     */
    public function shouldThrowExceptionIfFileNotFound()
    {
        $line = '@testo nonexistent_file.yml';
        $rootDirAwareStub = $this->createRootDirAwareStub();

        $source = new FileSource($rootDirAwareStub);
        $source->getContent($line);

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RootDirAwareInterface
     */
    protected function createRootDirAwareStub()
    {
        $stub = $this->getMock('Testo\Source\RootDirAwareInterface', array('getRootDir'));
        $stub->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(realpath(__DIR__ . '/../files')));
        return $stub;
    }

}