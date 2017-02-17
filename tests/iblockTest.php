<?php

namespace Dok\BX;

class IbTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Получение инфоблока по коду
	 * @dataProvider providerGetByCode
	 */
	public function testGetByCode($code, $res) {
		$ib = new \Dok\BX\InfoBlock\Main();
		$this->assertEquals($res, $ib->getIblockByCode($code));
		
	}
	
	public function providerGetByCode() {
		return Array(
			Array('clothes',	2),
			Array('false',		false),
			Array(false,		false),
			Array(0,			false),
		);
		
	}
	
}
