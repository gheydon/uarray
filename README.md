# uArray

[![Build Status](https://travis-ci.org/gheydon/uarray.png)](https://travis-ci.org/gheydon/uarray)

Allow manipulation of PICK Dynamic Arrays in PHP

PICK Dynamic Arrays are how the PICK operating system stores arrays in the database. The data itself is a single text string with delimiters inserted with the string to break the string up into different fields.

* Attribute Mark *AM* (\xfe) ]
* Value Make *VM* (\xfd) ^
* Sub Value Mark *SVM* (\xdc) \

These marks allow the system to break up the string into the fields which can be used by the system to indicate the fields and recurring values.
##Example Variable ##
`1^2^3^4^5]Value 1^Value 2^Value 3^Value 4^Value 5`
## Example Code ##
`$array = new uArray(“1\xfd2\xfd3\xfd4\xfd5\xfeValue 1\xfdValue 2\xfdValue 3\xfdValue 4\xfdValue 5”);

$a = $array[1] // VAR<1> = 1^2^3^4^5
$a = $array[1][2] // VAR<1,2> = 2
`