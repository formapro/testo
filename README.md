Testo
=====

Do you remember those outdated examples in a documentation?.
 
Or times while updating documents you had to test examples manually? 

This days in the past. Testo come into play.

Example 
=======

Let's say you have a `README.md.template`:

```
Some description of the awesome code:

<?php
{{testo:ReadmeExamples::awesomeExample}}
?>
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
```

So after you run `Testo` there will be clean ``README.md`:

```
Some description of the awesome code:

<?php
//Source: ReadmeExamples::awesomeExample

echo 'hello world!';
?>
```

What's inside?
==============

```php
<?php
//generate-doc.php

//include autoload.

$testo = new \Testo\Testo();

$testo->generate('path_to_template', 'path_to_document');
```