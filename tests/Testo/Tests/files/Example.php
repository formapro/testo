<?php
namespace Testo\Tests\files;

class Example
{
    public function helloWorld() 
    {
        $helloWorld = new \HelloWorld;
        
        $helloWorld->say();
    }

    public function exampleBlocks() {
        $helloWorld = new \HelloWorld;
        //@testo:start
        $helloWorld->say();
        //@testo:end
    }
}