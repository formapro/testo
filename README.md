Testo
=====

Do you remember those outdated examples in a documentation?. 
Or times while updating documents you had to test examples manually? 
This days in the past. 
Testo can replace a placeholder with a method code.

Example 
======

Let's say you have a README.md.template:

```
Some description of the awesome code:

```php
<?php
{{testo:ReadmeExamples::awesomeExample}}
```
```

and there is a class: 

```php
<?php 
namespace \Foo;

class ReadmeExamples
{
    public function awesomeExample()
    {
        echo 'hello world!';
    }
}

So after testo did his job you will get:

```
Some description of the awesome code:

```php
<?php
//Source: ReadmeExamples::awesomeExample

echo 'hello world!';
```
```

Running
=======

```php
<?php
//include autoload.

$testo = new \Testo\Testo();

$testo->generate('path_to_template', 'path_to_document');
```