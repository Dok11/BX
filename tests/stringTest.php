<?php

namespace Dok\BX;

class StringTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Тест на установку и чтение исходной строки
	 */
	public function testSetSource() {
		$string = new \Dok\BX\String();
		
		$string->setSource('my string');
		$this->assertEquals('my string', $string->getSource());
		
		$string->setSource('');
		$this->assertEquals('', $string->getSource());
		
		$string->setSource('строка на русском');
		$this->assertEquals('строка на русском', $string->getSource());
		
	}
	
	
	/**
	 * Тест на правильное определение окончаний
	 * @dataProvider providerGetStringEnding
	 */
	public function testGetStringEnding($num, $arStringEnds, $res) {
		$string = new \Dok\BX\String();
		
		$this->assertEquals($res, $string->getStringEnding($num, $arStringEnds));
		
	}
	
	public function providerGetStringEnding() {
		$arStringEnds = Array('рубль', 'рубля', 'рублей');
		
		return Array(
			Array(1,	$arStringEnds, 'рубль'),
			Array(21,	$arStringEnds, 'рубль'),
			Array(31,	$arStringEnds, 'рубль'),
			
			Array(2,	$arStringEnds, 'рубля'),
			Array(22,	$arStringEnds, 'рубля'),
			Array(33,	$arStringEnds, 'рубля'),
			
			Array(5,	$arStringEnds, 'рублей'),
			Array(11,	$arStringEnds, 'рублей'),
			Array(12,	$arStringEnds, 'рублей'),
			Array(35,	$arStringEnds, 'рублей'),
		);
	}
	
	
	/**
	 * Тест на перевод даты в человеческий формат
	 * @dataProvider providerGetStringFormatDateHuman
	 */
	public function testGetStringFormatDateHuman($date, $format, $result) {
		$string = new \Dok\BX\String();
		
		$string->setSource($date);
		$this->assertEquals($result, $string->getStringFormatDateHuman($format));
	}
	
	public function providerGetStringFormatDateHuman() {
		return Array(
			Array('18.02.2015', 'DD MMMM YYYY', '18&nbsp;февраля&nbsp;2015'),
			Array('22.12.2011', 'DD MMMM YYYY', '22&nbsp;декабря&nbsp;2011'),
		);
	}
	
	
	/**
	 * Тест на формирование ссылок из текста
	 */
	public function testGetStringLink() {
		$string = new \Dok\BX\String();
		
		$string->setSource('site.ru');
		$this->assertEquals('http://site.ru/', $string->getStringLink());
		
		$string->setSource('http://site.ru/');
		$this->assertEquals('http://site.ru/', $string->getStringLink());
		
		$string->setSource('https://site.ru/');
		$this->assertEquals('https://site.ru/', $string->getStringLink());
		
	}
	
	
	/**
	 * Тест на минификацию html
	 */
	public function testGetStringMinifyHtml() {
		$string = new \Dok\BX\String();
		
		$string->setSource('<div>text div</div>      <div>second div in</div>');
		$this->assertEquals('<div>text div</div> <div>second div in</div>', $string->getStringMinifyHtml());
		
		$string->setSource('      <div>text div</div>      <div>second div in</div>');
		$this->assertEquals(' <div>text div</div> <div>second div in</div>', $string->getStringMinifyHtml());
		
	}

}
