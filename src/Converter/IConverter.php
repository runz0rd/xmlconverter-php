<?php

/**
 * Created by PhpStorm.
 * User: milos.pejanovic
 * Date: 10/31/2016
 * Time: 2:16 PM
 */
namespace Converter;

interface IConverter {

	/**
	 * @param mixed $data
	 * @return array
	 */
	static function toArray($data);

	/**
	 * @param mixed $data
	 * @return string
	 */
	static function toJson($data);

	/**
	 * @param mixed $data
	 * @return \stdClass
	 */
	static function toObject($data);

	/**
	 * @param mixed $data
	 * @return string
	 */
	static function toXml($data);
}