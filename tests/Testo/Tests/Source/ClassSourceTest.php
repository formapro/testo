<?php
namespace Testo\Tests\Source;

use Testo\Source\ClassSource;

class ClassSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenClass()
    {
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

        $source = new ClassSource();
        $result = $source->getContent($line);

        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @test
     *
     * @expectedException \Testo\Exception\ClassNotFoundException
     */
    public function shouldThrowExceptionIfClassNotFound()
    {
        $line = '@testo NonexistentClassName';

        $source = new ClassSource();
        $source->getContent($line);

    }
}
