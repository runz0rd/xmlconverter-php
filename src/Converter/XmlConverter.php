<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 10/31/2016
 * Time: 2:15 PM
 */
namespace Converter;

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

	public static function toJson($data) {
		// TODO: Implement toJson() method.
	}

	public static function toObject($data) {
		// TODO: Implement toObject() method.
	}

	public static function toXml($data) {
		// TODO: Implement toXml() method.
	}

	private function readToArray(\XMLReader $cursor) {
		$output = [];

		if($cursor->hasAttributes) {
			while($cursor->moveToNextAttribute()) {
				$output[self::ATTRIBUTES_KEY][$cursor->name] = $cursor->value;
			}
			$cursor->moveToElement();
		}
		if(!$cursor->isEmptyElement) {
			while ($cursor->read()) {
				if ($cursor->nodeType === \XMLReader::END_ELEMENT) {
					break;
				}

				if ($cursor->nodeType === \XMLReader::ELEMENT) {
					$output[$cursor->name] = self::readToArray($cursor);
				}

				if ($cursor->nodeType === \XMLReader::TEXT) {
					if (isset($output[self::ATTRIBUTES_KEY])) {
						$output[self::VALUE_KEY] = $cursor->value;
					} else {
						$output = $cursor->value;
					}
				}
			}
		}

		return $output;
	}

	private function readAttributes() {
		//TODO extract from readToArray
	}
}