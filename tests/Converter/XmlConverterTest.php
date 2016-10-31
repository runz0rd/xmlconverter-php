<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 10/31/2016
 * Time: 2:44 PM
 */
class XmlConverterTest extends PHPUnit_Framework_TestCase {

    /**
     * @param array $expected
     * @param $xml
     * @dataProvider validValues
     */
	public function testToArray($expected, $xml) {
		$actual = \Converter\XmlConverter::toArray($xml);
        $this->assertEquals($expected, $actual);
	}

    /**
     * @param array $expected
     * @param $xml
     * @dataProvider validValues
     */
    public function testToJson($expected, $xml) {
        $actual = \Converter\XmlConverter::toJson($xml);
        $this->assertEquals($expected, json_decode($actual, true));
    }

    /**
     * @param array $expected
     * @param $xml
     * @dataProvider validValues
     */
    public function testToObject($expected, $xml) {
        $expected = (object) $expected;
        $actual = \Converter\XmlConverter::toObject($xml);
        $this->assertEquals($expected, $actual);
    }

    public function validValues() {
        $model['@attributes']['attribute1'] = 'attribute1';
        $model['boolTrue'] = true;
        $model['boolFalse'] = false;
        $model['string'] = 'a';
        $model['namedString'] = 'named';
        $model['integer'] = 5;
        $model['array'] = [1,'a',3];
        $model['stringArray'] = ['a','b','c'];
        $model['integerArray'] = [1,2,3];
        $model['booleanArray'] = [true,true,false];
        $object['a'] = 1;
        $model['objectArray'] = [$object,$object,$object];
        $model['object'] = $object;
        $model['requiredString'] = 'requiredString';
        $model['alwaysRequiredBoolean'] = false;
        $model['multipleRequiredInteger'] = 5;
        $nestedModel1 = $model;
        $nestedModel1['@attributes']['attribute1'] = 'attribute2';
        $nestedModel2 = $model;
        $nestedModel2['@attributes']['attribute1'] = 'attribute3';
        $model['model'] = $nestedModel1;
        $model['modelArray'] = [$nestedModel1,$nestedModel2];
        $model['xml']['@attributes']['xmlns:test'] = 'testns1';
        $model['xml']['@attributes']['attributeTest'] = 'attribute';
        $model['xml']['@value'] = 'nodeValue';
        $model['xmlWithoutValue']['@attributes']['attributeTest'] = 'attribute';
        $model['xmlWithoutValue']['@attributes']['xmlns:test'] = 'testns2';

        $xml = \Common\Util\Xml::loadFromFile(__DIR__ . '/xml/valid_testModel.xml');

        return [
            [$model, $xml]
        ];
    }
}
