Testo [![Build Status](https://secure.travis-ci.org/formapro/testo.png?branch=master)](http://travis-ci.org/formapro/testo)
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
{{testo:Testo\Examples\Hello::world}}
?>
```

and there is a class: 

```php
<?php
namespace Testo\Examples;

class Hello
{
    public function world()
    {
        echo 'hello world!';
    }
}
```

So after you run `Testo` there will be clean `README.md`:

```
Some description of the awesome code:

<?php
echo 'hello world!';
?>
```

What's inside?
==============

```php
<?php
//include autoload.

$testo = new \Testo\Testo();

$testo->generate(__DIR__.'/README.md.template', __DIR__.'/README.md');
```