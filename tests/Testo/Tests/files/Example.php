<?php
namespace Testo\Tests\files;

class Example
{
    public function helloWorld()
    {
        $helloWorld = new \HelloWorld;

        $helloWorld->say();
    }

    public function exampleBlocks()
    {
        $helloWorld = new \HelloWorld;
        //@testo {
        $helloWorld->say();
        //@testo }
    }

    public function exampleSource()
    {

        //@testo source
        $helloWorld = new \HelloWorld;

        $helloWorld->say();

    }

    public function exampleUncomment()
    {

        //@testo uncomment use \Foo\Bar;
        $bar = new Bar;

        $bar->baz();

    }
}