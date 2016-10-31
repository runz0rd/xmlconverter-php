<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 10/31/2016
 * Time: 2:15 PM
 */
namespace Converter;

use Common\Util\Iteration;
use Common\Util\Xml;

class XmlConverter implements IConverter {

	const ATTRIBUTES_KEY = '@attributes';
	const VALUE_KEY = '@value';

	/**
	 * @param string $data
	 * @return array
	 * @throws \Exception
	 */
	public static function toArray($data) {
		$xml = Xml::removeWhitespace($data);
		$reader = new \XMLReader();
		if(!$reader->XML($xml)) {
			throw new \Exception('invalid xml');
		}

		// Start at first element, skip root
		while($reader->read()) {
			if($reader->nodeType === \XMLReader::ELEMENT) {
				break;
			}
		}
		$output = self::readToArray($reader);
		$reader->close();

		return $output;
	}

    /**
     * @param string $data
     * @return string
     */
	public static function toJson($data) {
		return json_encode(self::toArray($data));
	}

    /**
     * @param string $data
     * @return object
     */
	public static function toObject($data) {
        return (object) self::toArray($data);
	}

    /**
     * @param string $data
     * @return string
     */
	public static function toXml($data) {
        return $data;
	}

    /**
     * @param \XMLReader $cursor
     * @return array|mixed
     */
	private function readToArray(\XMLReader $cursor) {
		$output = [];
		if($cursor->hasAttributes) {
            $output = self::pushAttributes($cursor, $output);
            $cursor->moveToElement();
		}
		if(!$cursor->isEmptyElement) {
            while ($cursor->read()) {
                switch ($cursor->nodeType) {
                    case \XMLReader::END_ELEMENT:
                        break 2;
                    case \XMLReader::ELEMENT:
                        $output = self::pushElement($cursor, $output);
                        break;
                    case \XMLReader::TEXT:
                        $output = self::pushText($cursor, $output);
                        break;
                }
            }
        }
        return $output;
	}

    /**
     * @param \XMLReader $cursor
     * @param array $output
     * @return array
     */
	private function pushAttributes(\XMLReader $cursor, array $output) {
        while($cursor->moveToNextAttribute()) {
            $output[self::ATTRIBUTES_KEY][$cursor->name] = $cursor->value;
        }
        return $output;
	}

    /**
     * @param \XMLReader $cursor
     * @param array $output
     * @return array
     */
    private function pushElement(\XMLReader $cursor, array $output) {
        if(isset($output[$cursor->name])) {
            $output = self::pushCollection($cursor, $output);
        }
        else {
            $output[$cursor->name] = self::readToArray($cursor);
        }
        return $output;
    }

    /**
     * @param \XMLReader $cursor
     * @param array $output
     * @return array|mixed
     */
    private function pushText(\XMLReader $cursor, array $output) {
        $value = Iteration::typeFilter($cursor->value);
        if(empty($output)) {
            $output = $value;
        }
        else {
            $output[self::VALUE_KEY] = $value;
        }
        return $output;
    }

    /**
     * @param \XMLReader $cursor
     * @param array $output
     * @return array
     */
    private function pushCollection(\XMLReader $cursor, array $output) {
        if(!is_array($output[$cursor->name]) || !is_numeric(key($output[$cursor->name]))) {
            $output[$cursor->name] = [$output[$cursor->name]];
        }
        $output[$cursor->name][] = self::readToArray($cursor);
        return $output;
    }
}