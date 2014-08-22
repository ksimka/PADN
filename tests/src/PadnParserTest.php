<?php

namespace Padn;

/**
 * PADN parser test
 * 
 * @package Padn
 */
class PadnParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var PadnParser */
    private static $parser;

    public static function setUpBeforeClass()
    {
        self::$parser = new PadnParser();
    }

    /**
     * @return array
     */
    public function providerParse()
    {
        return [
            // 0
            [
                "[int]",
                [
                    ['k' => '', 'kc' => '', 'km' => '', 'c' => 'int', 'm' => '']
                ],
            ],
            // 1
            [
                "[=some_meaning]",
                [
                    ['k' => '', 'kc' => '', 'km' => '', 'c' => '', 'm' => 'some_meaning']
                ],
            ],
            // 2
            [
                "[str:int, str=name:int=id]",
                [
                    ['k' => '', 'kc' => 'str', 'km' => '', 'c' => 'int', 'm' => ''],
                    ['k' => '', 'kc' => 'str', 'km' => 'name', 'c' => 'int', 'm' => 'id'],
                ],
            ],
            // 3
            [
                "['k':str=key, 'kc':str=key_class]",
                [
                    ['k' => 'k', 'kc' => '', 'km' => '', 'c' => 'str', 'm' => 'key'],
                    ['k' => 'kc', 'kc' => '', 'km' => '', 'c' => 'str', 'm' => 'key_class'],
                ],
            ],
            // 4
            [
                "['names':[str], 'ids':[int]]",
                [
                    ['k' => 'names', 'kc' => '', 'km' => '', 'c' => '[]', 'm' => '',
                        'a' => [
                            ['k' => '', 'kc' => '', 'km' => '', 'c' => 'str', 'm' => '']
                        ]
                    ],
                    ['k' => 'ids', 'kc' => '', 'km' => '', 'c' => '[]', 'm' => '',
                        'a' => [
                            ['k' => '', 'kc' => '', 'km' => '', 'c' => 'int', 'm' => '']
                        ]
                    ],
                ],
            ],

            // 5
            [
                "[int, ...]",
                [
                    ['k' => '', 'kc' => '', 'km' => '', 'c' => 'int', 'm' => '', '...' => true]
                ],
            ],
            // 6
            [
                "[=some_meaning, ...]",
                [
                    ['k' => '', 'kc' => '', 'km' => '', 'c' => '', 'm' => 'some_meaning', '...' => true]
                ],
            ],
            // 7
            [
                "['names':[str, ...], 'ids':[int, ...]]",
                [
                    [
                        'k' => 'names', 'kc' => '', 'km' => '', 'c' => '[]', 'm' => '',
                        'a' => [
                            ['k' => '', 'kc' => '', 'km' => '', 'c' => 'str', 'm' => '', '...' => true]
                        ]
                    ],
                    [
                        'k' => 'ids', 'kc' => '', 'km' => '', 'c' => '[]', 'm' => '',
                        'a' => [
                            ['k' => '', 'kc' => '', 'km' => '', 'c' => 'int', 'm' => '', '...' => true]
                        ]
                    ],
                ],
            ],
            // 8
            [
                "[str:[int, ...], ...]",
                [
                    [
                        'k' => '', 'kc' => 'str', 'km' => '', 'c' => '[]', 'm' => '',
                        'a' => [
                            ['k' => '', 'kc' => '', 'km' => '', 'c' => 'int', 'm' => '', '...' => true]
                        ],
                        '...' => true,
                    ]
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerParse
     *
     * @param $padnString
     * @param $expectedResult
     */
    public function testParse($padnString, $expectedResult)
    {
        $result = self::$parser->parseArray($padnString);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerParseError()
    {
        return [
            // Square brackets
            ['', '\UnexpectedValueException', 1],
            ['[', '\UnexpectedValueException', 1],
            [']', '\UnexpectedValueException', 1],

            // Invalid element
            ['[in t]', '\UnexpectedValueException', 2],
            ['[int, s;r, int]', '\UnexpectedValueException', 2],
            ['[int, str:], int]', '\UnexpectedValueException', 2],
            ['[[int]:int]', '\UnexpectedValueException', 2],
            ["['k'=meaning:int]", '\UnexpectedValueException', 2],
            ["['k':int=abc=def]", '\UnexpectedValueException', 2],
            ["['k':int:int]", '\UnexpectedValueException', 2],
            ["[str:'k']", '\UnexpectedValueException', 2],
            ["[ , ]", '\UnexpectedValueException', 2],
            ['[str, int:[]]', '\UnexpectedValueException', 2],

            // Empty array
            ['[]', '\UnexpectedValueException', 3],

            // Wrong repetition usage
            ['[...]', '\UnexpectedValueException', 4],
            ['[int, str:[...]]', '\UnexpectedValueException', 4],
        ];
    }

    /**
     * @dataProvider providerParseError
     *
     * @param $padnString
     * @param $expectedException
     * @param $expectedExceptionCode
     */
    public function testParseError($padnString, $expectedException, $expectedExceptionCode)
    {
        $this->setExpectedException($expectedException, '', $expectedExceptionCode);

        self::$parser->parseArray($padnString);
    }
}
