<?php
namespace Testo\Tests\Sources;

use Testo\Sources\ClassSource;

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
}
