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
	 */
	public function testGetStringEnding() {
		$string = new \Dok\BX\String();
	
		$arStringEnds = Array('рубль', 'рубля', 'рублей');
		
		$this->assertEquals('рубль', $string->getStringEnding(1, $arStringEnds));
		$this->assertEquals('рубль', $string->getStringEnding(21, $arStringEnds));
		$this->assertEquals('рубль', $string->getStringEnding(31, $arStringEnds));
		
		$this->assertEquals('рубля', $string->getStringEnding(2, $arStringEnds));
		$this->assertEquals('рубля', $string->getStringEnding(22, $arStringEnds));
		$this->assertEquals('рубля', $string->getStringEnding(33, $arStringEnds));
		
		$this->assertEquals('рублей', $string->getStringEnding(5, $arStringEnds));
		$this->assertEquals('рублей', $string->getStringEnding(11, $arStringEnds));
		$this->assertEquals('рублей', $string->getStringEnding(12, $arStringEnds));
		$this->assertEquals('рублей', $string->getStringEnding(35, $arStringEnds));
		
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
