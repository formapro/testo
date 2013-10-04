<?php
namespace Testo\Tests\Sources;

use Testo\Sources\ClassSource;
use Testo\Testo;

class ClassSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenClass()
    {
        $testoMock = $this->createTestoMock();
        $line = '@testo Testo\Tests\files\Example2';
        $expectedContent = array(
            "class Example2\n",
            "{\n",
            "    public function helloWorld()\n",
            "    {\n",
            "        \$helloWorld = new \HelloWorld;\n",
            "\n",
            "        \$helloWorld->say();\n",
            "    }\n",
            "\n",
            "}"
        );

        $source = new ClassSource($testoMock);
        $result = $source->getContent($line);
        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Testo
     */
    protected function createTestoMock()
    {
        return $this->getMock('Testo\Testo');
    }

}