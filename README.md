PHP array doc notation (PADN)
===================================

Purpose
-------

Array is one of the most used types in PHP. When function returns an array as a result, it's quite difficult to trace the whole array structure: all possible keys, types of values and nested arrays structures.

PADN is intended to facilitate the process of description and reading array's structure. PADN is supposed to be used in `@return`, `@param` and `@var` PHPDOC tags.

Limitations
-----------

PADN is designed for strict typing inside array structure. Loose typing is not supported.

Description
-----------

> The general rule for using recommendations and confusing stuff — brevity: if > you can write something shorter without compromising readability, do so.

Array bounds are denoted with square brackets. Values are separated with commas. It's recommended to use one space after each comma.

`[[], [], []]`

Structure consists of the following elements:

+ class
+ key
+ meaning
+ repetition

### Class ###

Class describes a type of corresponding value. 

Classes for internal types and pseudo-types:

+ array: `[]`
+ integer, integer-string: `int`
+ float, float-string: `flt`
+ boolean: `bool`
+ string: `str`
+ resource: `res`
+ null: `null`
+ callback: `clb`

> Numeric-strings (integer-string, float-string) are equal to int and flt
> accordingly

Simple example of PADN and corresponding result:

```
[int, int, flt, str, bool, res]
[2, '4', 3.14, "Hello!", false, STDOUT]
```

FQCN can be used when describing objects. But if this leads to cumbersome notation it's recommended to use short aliases. The main criteria here is understandability and readability for all your team members.

Simple example of array that contains user (actual class Auth_User) and collection of photo albums (\Media\Photo\PhotoAlbum\AlbumCollection):

```
[Auth_User, \Media\Photo\PhotoAlbum\AlbumCollection] // full
[user, album_coll] // short
```

### Key ###

Key is denoted with string enclosed in single quotes. Key must be exactly the key in array.

Example of notation and result:

```
['country_id', 'country_name']
['country_id' => 42, 'country_name' => 'Oz']
```

Type of value must be written after the key separated with colon.

`['country_id':int, 'country_name':str]`

Type should be omitted always when possible.

```
// id is often int but can be str, so we leave type here, but name is always string, omit it
['country_id':int, 'country_name']
```

In case you don't use suffixes like 'id' or 'name' or else, you shoul always denote the type. In this example the first notation describes array of ids, the second — array of names.

```
['country':int, 'region':int, 'city':int]
['country':str, 'region':str, 'city':str]
```

### Meaning ###

Meaning should be used to clarify the purpose of the value. Meaning can be used both with class and instead of it. Meaning must be denoted after class with equal sign as separator.

`['country':str=name]`

If you always (or in current context) use integers for ids, then the following PADN can be used for array containing three ids:

`[id, id, id]`

Otherwise you should write it with classes:

`[int=id, int=id, str=id]`

Previous example with geo ids and names can be rewritten to one of the following forms:

```
['country'=id, 'region'=id, 'city'=id]
['country':int=id, 'region':int=id, 'city':int=id]
['country'=name, 'region'=name, 'city'=name]
['country':str=name, 'region':str=name, 'city':str=name]
```

### Repetition ###

Ellipsis is used as repetition operator. It means a repetition of the preceding structure.

Array with any number of integers:

`[int, ...]`

Two-level array, the key ids contains array of integers, names — array of strings.

`['ids':[int, ...], 'names':[str, ...]]`

The first element is timestamp, the second — array of user objects:

`[int=timestamp, [user, ...]]`

Array contains info for list of hyperlinks.

`[['href', 'name'], ...]`

Multiline notation
------------------

@return supports multiline notation, so you can write PADN in several lines, breaking it after commas.

```
@return array ['id':int, 'weight':flt, 'name', 'privilegy':str, 
    'items':[['id':int, 'weight':flt, 'name'], ...]]
```
