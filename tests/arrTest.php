<?php

namespace Dok\BX;

class ArrTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Тест разбивки массовов на колонки
	 * @dataProvider providerGetArrChunkCols
	 */
	public function testGetArrChunkCols($src, $i, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->getArrChunkCols($i));
		
	}
	
	public function providerGetArrChunkCols() {
		return Array(
			Array(
				Array(1, 2, 3, 4, 5, 6, 7, 8), 2,
				Array(Array(1,2,3,4), Array(5,6,7,8))
			),
			Array(
				Array(1, 2, 3, 4, 5, 6, 7, 8, 9), 2,
				Array(Array(1,2,3,4,5), Array(6,7,8,9))
			),
			Array(
				Array(1, 2, 3, 4, 5), 3,
				Array(Array(1,2), Array(3,4), Array(5))
			),
			Array(
				Array(1), 3,
				Array(Array(1))
			),
			Array(
				Array(1, 2, 3, 4, 5), 1,
				Array(Array(1, 2, 3, 4, 5))
			),
			Array(
				'some string', 1,
				Array()
			),
			Array(
				Array(1, 2, 3, 4, 5), 'string',
				Array(Array(1, 2, 3, 4, 5))
			),
		);
	}
	
	
	/**
	 * Тест поиска по массиву
	 * @dataProvider providerGetArrFindInArr
	 */
	public function testGetArrFindInArr($src, $field, $val, $key, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->getArrFindInArr($field, $val, $key));
		
	}
	
	public function providerGetArrFindInArr() {
		$arSourceArray = Array(
			Array(
				'ID'		=> 1,
				'NAME'		=> 'Oleg',
				'COUNTRY'	=> 'Russia',
			),
			Array(
				'ID'		=> 2,
				'NAME'		=> 'Li',
				'COUNTRY'	=> 'China',
			),
			Array(
				'ID'		=> 3,
				'NAME'		=> 'Arnold',
				'COUNTRY'	=> 'Austria',
			),
		);
		
		return Array(
			Array($arSourceArray, 'ID',		2,			'ID',		2),
			Array($arSourceArray, 'ID',		2,			'NAME',		'Li'),
			Array($arSourceArray, 'NAME',	'Oleg',		'ID',		1),
			Array($arSourceArray, 'NAME',	'Oleg',		'COUNTRY',	'Russia'),
			Array($arSourceArray, 'NAME',	'Zena',		'COUNTRY',	false),
			Array($arSourceArray, 'AGE',	'Zena',		'COUNTRY',	false),
			Array($arSourceArray, 'NAME',	'Arnold',	null,		2),
		);
		
	}

}
