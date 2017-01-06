<?php

namespace Dok\BX;

class IblockElTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Получение инфоблока по коду
	 * @dataProvider providerGetByCode
	 */
	public function testGetByCode($params, $res) {
		$el = new \Dok\BX\Iblock\El();
		$this->assertEquals($res, $el->getElList($params));
		
	}
	
	public function providerGetByCode() {
		return Array(
			
		);
		
	}
	
}
