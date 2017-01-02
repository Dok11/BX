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
	
	
	/**
	 * Тест вычисления сходства массивов
	 * @dataProvider providerGetArrIntersectPercent
	 */
	public function testGetArrIntersectPercent($src, $target, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->getArrIntersectPercent($target));
		
	}
	
	public function providerGetArrIntersectPercent() {
		return Array(
			Array(Array(1), Array(1), 100),
			Array(Array(0), Array(1), 0),
			Array(Array(0, 1), Array(1), 50),
			Array(Array(0, 1), Array(1, 2), 50),
			Array(Array(0, 1), Array(1, 2, 3), 50),
			Array(Array(0, 1, 2), Array(0), 33),
			Array(Array(0), Array(0, 1, 2), 100),
		);
		
	}
	
	
	/**
	 * Тест вычисления сходства массивов по ключам
	 * @dataProvider providerGetArrIntersectKeyPercent
	 */
	public function testGetArrIntersectKeyPercent($src, $target, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->getArrIntersectKeyPercent($target));
		
	}
	
	public function providerGetArrIntersectKeyPercent() {
		return Array(
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				100
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				Array('KEY_1' => 1, 'KEY_2' => 2),
				67
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				Array('KEY_1' => 1),
				33
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2),
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				100
			),
			Array(
				Array('KEY_1' => 1),
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				100
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2),
				Array('KEY_2' => 2, 'KEY_3' => 3),
				50
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2),
				Array('KEY_2' => 2, 'KEY_3' => 3, 'KEY_4' => 3),
				50
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2),
				Array('KEY_3' => 3, 'KEY_4' => 3),
				0
			),
		);
		
	}
	
	
	/**
	 * Тест объединения массивов
	 * @dataProvider providerGetArrMergeExt
	 */
	public function testGetArrMergeExt($src, $target, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->getArrMergeExt($target));
		
	}
	
	public function providerGetArrMergeExt() {
		return Array(
			Array(
				false,
				false,
				Array(),
			),
			Array(
				Array('K1' => 1, 'K2'=>2),
				false,
				Array('K1' => 1, 'K2'=>2),
			),
			Array(
				Array('KEY_1' => 1, 'KEY_2' => 2, 'KEY_3' => 3),
				Array('KEY_1' => 2, 'KEY_2' => 3, 'KEY_3' => 4),
				Array('KEY_1' => Array(1, 2), 'KEY_2' => Array(2, 3), 'KEY_3' => Array(3, 4)),
			),
			
		);
		
	}
	
	
	/**
	 * Тест сортировки массива
	 * @dataProvider providerSortArrByField
	 */
	public function testSortArrByField($src, $field, $order, $res) {
		$arr = new \Dok\BX\Arr();
		
		$arr->setSource($src);
		$this->assertEquals($res, $arr->sortArrByField($field, $order));
		
	}
	
	public function providerSortArrByField() {
		return Array(
			// Прямая сортировка
			Array(
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
				'A', SORT_ASC,
				Array(
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
			),
			
			// Обратная сортировка
			Array(
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
				'A', SORT_DESC,
				Array(
					Array('A'=>31, 'B'=>11, 'C' => 22),
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
				),
			),
			
			// Неявная сортировка
			Array(
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
				'A', false,
				Array(
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
			),
			
			// Без сортировки
			Array(
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
				'A', true,
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
			),
			
			// Сортировка по несуществующему ключу
			Array(
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
				'Z', false,
				Array(
					Array('A'=>11, 'B'=>21, 'C' => 12),
					Array('A'=>10, 'B'=>41, 'C' => 16),
					Array('A'=>31, 'B'=>11, 'C' => 22),
				),
			),
			
			// Сортировка немассива
			Array(
				false, 'Z', false, null
			),
			
		);
		
	}


}
