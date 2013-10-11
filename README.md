Testo [![Build Status](https://secure.travis-ci.org/formapro/testo.png?branch=master)](http://travis-ci.org/formapro/testo)
=====

Do you remember those outdated examples in a documentation?.
 
Or times while updating documents you had to test examples manually? 

This days in the past. Testo come into play.

Example 
=======

Let's say you have a `README.md`:

```
@testo examples/Testo/Examples/README.md {
Some description of the awesome code:

<?php
@testo Testo\Examples\Hello world {
echo 'hello world!';
@testo 1abb62086e2cc233ede1e19de3a8e5f6 }
?>
@testo 35a8d2d426c05614551484dcdd450f37 }
```

and there is a class: 

```php
// @testo Testo\Examples\Hello {
// @testo }
```

So after you run `Testo` there will be clean `README.md`:

```
@testo examples/Testo/Examples/README.md.expected {
Some description of the awesome code:

<?php
@testo Testo\Examples\Hello world {
echo 'hello world!';
@testo 1abb62086e2cc233ede1e19de3a8e5f6 }
?>
@testo 35a8d2d426c05614551484dcdd450f37 }
```

What's inside?
==============

```php
<?php
// @testo Testo\Examples\ReadmeTest whatsInside {
// @testo }
```