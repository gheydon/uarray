# uArray [![Build Status](https://travis-ci.org/gheydon/uarray.png)](https://travis-ci.org/gheydon/uarray)

Allow manipulation of PICK Dynamic Arrays in PHP

PICK Dynamic Arrays are how the PICK operating system stores arrays in the database. The data itself is a single text string with delimiters inserted with the string to break the string up into different fields.

* Attribute Mark *AM* (\xfe) ^
* Value Make *VM* (\xfd) ]
* Sub Value Mark *SVM* (\xdc) \

These marks allow the system to break up the string into the fields which can be used by the system to indicate the fields and recurring values.
## Installation
Installation via [composer](http://getcomposer.com) is the only method supported at this stage. Edit your composer.json to add the following.
``` js
{
    "require": {
        // ...
        "heydon/uarray": "1.0.x"
    }
}
```

##Example Variable
```
1]2]3]4]5^Value 1]Value 2]Value 3]Value 4]Value 5
```
## Example Code
``` php
$array = new uArray("1\xfd2\xfd3\xfd4\xfd5\xfeValue 1\xfdValue 2\xfdValue 3\xfdValue 4\xfdValue 5");

$a = $array[1] // VAR<1> = 1]2]3]4]5
$a = $array[1][2] // VAR<1,2> = 2
```
### Setting a value
In PICK BASIC you would do the following to insert a value
``` basic
A<1,2> = 'example'
```
in PHP the following will be done.
``` php
$a = new uArray();
$a[1][2] = 'example'
```
the result of this would be the same as the PICK equivilent.
```
]example
```
### unsetting a value
PICK/u2 doesn't really have a delete value, which will delete the value and not having "sliding delimiters". so in PICK/u2 BASIC you would do something like
``` basic
A = 1:VM:2:VM:3
A<1,2> = ''
PRINT A ;* would result in 1]]3
```
In PHP this can be done 2 ways.
``` php
$a = new uArray("1\xfd2\xfd3");
unset($a[2]); // or
$a[2] = '';
```
### Inserting values
To insert values into an array which in PICK/u2 you would do the following.
``` basic
A = 100:VM:200:VM:300:VM:400:VM:500
INS 350 BEFORE A<1,4>
```
and in PHP the following will be done.
``` php
$array = new uArray("100\xfd200\xfd300\xfd400\xfd500");

$array->ins(350, 4);
```
### Deleting values
When deleting a value in PICK the following is done.
``` basic
A = 100:VM:200:VM:300:VM:400:VM:500
DEL A<1,3>
```
to do the equivilent in PHP
``` php
$array = new uArray("100\xfd200\xfd300\xfd400\xfd500");

$array->del(3);
```
### Associated Arrays
Dynamic arrays in PICK/u2 and PHP are fundermentally different. The following is an example of how a basic invoice would be reprsented in PICK/u2
```
01: 10]20]30
02: Item 1]Item 2]Item 3
03: 100]200]300
```
Where in this example the line 1 is the item number, 2 is the item name, and 3 is the cost. But in PHP this same array would be represented this in the following way.
``` php
$order = array(
  10 => array(
    'title' => 'Item 1',
    'price' => 1.00,
  ),
  20 => array(
    'title' => 'Item 2',
    'price' => 2.00,
  ),
  30 => array(
    'title' => 'Item 3',
    'price' => 3.00,
  ),
);
```
Keeping the attributes in alignment is not very easy or very natural in PHP, so the ::fetchAssoc() has been created to allow easier manipulation of the PICK/u2 associated array.

``` php
$v = new uArray('10\xfd20\xfd30\xfeItem 1\xfdItem 2\xfdItem 3\xfe100\xfd200\xfd300');
$assoc = $v->fetchAssoc(array(2,3), 1); // tell fetchAssoc() to user attribute 2 and 3 as the values and attribute 1 as the key

echo "Item: $assoc[20][2]\n"; // to access the values

$assoc[] = array(2 => 'Item 4', 3 => 400); // Which will add an new associated value and keep all the values inline.
```
Any object which implements the uAssocArraySource can implement the fetchAssoc() method.
### Checking for changes.
Since the uArray was primarily built to work with RedBack, there needed to be the ability to capture changes so they can be sent to the backend server. The Taint flag was created and can be reset when needed to allow to detect changes during a specific period.

``` php
$value = new uArray('example');
$value->resetTaint(); // Resets the taint flag.
$value->isTainted(); // checks to see if the valiable has changed.
```