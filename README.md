PHP array doc notation (PADN)
===================================

> This is work-in-progress!

PADN is a notation for documenting array structures in your PHP code. With phpdoc you can tell that var is array. With PADN you can tell that array
- has certain keys
- has keys of certain types
- has values of certain types
- has keys that mean something special and has values that mean something special

Here are some examples

```
[int, str]
[str:int, ...]
['id':str, ...]
['country', 'region', 'city']
['id':int, 'weight':flt, 'name', 'pr':str=privilegy, 'items':[['id':int, 'weight':flt, 'name'], ...]]
```

And here is how PADN is supposed to be used

```php
/**
 * Returns geo ids
 *
 * @return array    ['country', 'region', 'city']
 */
public function getGeoIds() {
    return ['country' => $this->countryId, 'region' => $this->regionId, 'city' => $this->cityId];
}

/**
 * Finds something using geo ids
 *
 * @param array $ids    ['country', 'region', 'city'] Geo identifiers
 * @return SomeThing[]
 */
public function findByGeoIds(array $ids) {
    // ...
}
```

[PADN v0.8 — see full specification in wiki](https://github.com/ksimka/PADN/wiki)
---------------------------------------------------------------------------------

PADN parser
-----------

[![Build Status](https://travis-ci.org/ksimka/PADN.svg?branch=master)](https://travis-ci.org/ksimka/PADN) 

Source code contains a parser for PADN which parses a PADN-formatted string to an array. See examples in [tests](https://github.com/ksimka/PADN/blob/master/tests/src/PadnParserTest.php).

PADN validator (TODO)
--------------

PADN validator validates an array against corresponding PADN string.
