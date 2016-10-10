<?php

namespace Dok\bx;

class String {
	
	// =========================================================================
	// === ПАРАМЕТРЫ ОБЪЕКТА ===================================================
	// =========================================================================
	
	/**
	 * Исходная строка
	 * @var string 
	 */
	private $source;
	
	
	/**
	 * Формат текущего языка
	 * @var string
	 */
	private $lang;


	// =========================================================================
	// === КОНСТРУКТОР, ГЕТТЕРЫ и СЕТТЕРЫ ======================================
	// =========================================================================
	
	/**
	 * Метод устанавливает исходное значение строки
	 * @param string $source
	 */
	public function setSource($source='') {
		$this->source = $source;
		
	}
	
	
	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================
	
	/**
	 * Метод возвращает нужную словоформу чистилительного по колчиеству
	 * @param int $n Количество
	 * @param array $arVars Массив словоформ (1, 2, 5)
	 * @return string
	 */
	public function getStringEnding($n, $arVars) {
		if(!intval($n)) {return false;}

		$i = intval($n);

		$plural	= $i%10==1&&$i%100!=11
				? $arVars[0]
				: ($i%10>=2&&$i%10<=4&&($i%100<10||$i%100>=20)
					? $arVars[1]
					: $arVars[2]);

		return $plural;

	}
	
	
	/**
	 * Метод переводит дату из формата сайта в человеческий вид
	 * @param string $format Формат в который необходимо её пробразовать
	 * @return string Дата в отформатированном виде
	 */
	public function getStringFormatDateHuman($format) {
		
		// Перевод даты в человеческий формат
		$result	= FormatDateFromDB($this->source, $format);
		
		// Убираем первый ноль из даты
		$result = preg_replace('/^0/', '', $result);
		
		// Ставим неразрывные пробелы
		$result = str_replace(' ', '&nbsp;', $result);

		if(strstr($format, 'MMMM') && LANGUAGE_ID === 'ru') {
			// Приведение даты к нижнему регистру, если нужно
			$result = mb_strtolower($result, 'UTF-8');
			
		}

		return $result;
		
	}
	
	
	/**
	 * Метод превращает прошедшую дату в формате сайта в человеческий вид
	 * Возвращает время, если дата соответствует сегодняшней
	 * Возвращает только время, дату, месяц, если дата текущего года
	 * @param string $date Дата в формате сайта
	 * @return string Дата в отформатированном виде
	 */
	public function getStringFormatDateHumanBack() {
		$date			= $this->source;
		
		
		// Исходные параметры: парсинг даты и таймштампы
		$arDate			= ParseDateTime($date);
		$iTimeStamp		= MakeTimeStamp($date);

		$iDateDayNum	= floor($iTimeStamp / 86400);
		$iNowDayNum		= floor(time() / 86400);
		// ---------------------------------------------------------------------


		// Определение формата даты
		$sFormat = 'DD MMMM YYYY в HH:MI';

		if($iDateDayNum === $iNowDayNum) {
			$sFormat = 'Сегодня в HH:MI';

		} elseif($iDateDayNum === $iNowDayNum-1) {
			$sFormat = 'Вчера в HH:MI';

		} elseif($arDate['YYYY'] == date('Y')) {
			$sFormat = 'DD MMMM в HH:MI';

		}
		// ---------------------------------------------------------------------


		// Формирование результата
		$sDateFormatted = self::getFormatDateHuman($date, $sFormat);

		return $sDateFormatted;
		
	}
	
	
	/**
	 * Метод возвращает число в формате суммы
	 * @param string $decimal Число знаков после запятой, например, не более двух
	 * @param string $groupDiv Разделитель разрядов
	 * @param string $fractionDiv Разделитель дробных значений
	 * @return string
	 */
	public function getStringFormatPrice($decimal=2, $groupDiv='&#8201;', $fractionDiv=',') {
		if(LANGUAGE_ID === 'en') {
			$fractionDiv = '.';
			$groupDiv = ',';
		}

		$iRoundNumber	= round($this->source, $decimal);
		$arNumberParts	= explode('.', $iRoundNumber);
		$iDecimal		= ($arNumberParts[1]) ? strlen($arNumberParts[1]) : 0;
		$iSeparatorTmp	= ($this->source >= 10000) ? '#': false;

		$sNumber		= number_format($this->source, $iDecimal, $fractionDiv, $iSeparatorTmp);
		$sNumber		= str_replace('#', $groupDiv, $sNumber);

		if(!$sNumber) {
			$sNumber = 0;
		}

		return $sNumber;
		
	}
	
	
	/**
	 * Метод формирует ссылку из текстовой строки
	 * Например site.ru превращается в http://site.ru/
	 * @return string
	 */
	public function getStringLink() {
		$sSourceString = $this->source;
		$sPatternProtocol = '/[a-z]{2,6}:\/\//';

		if(preg_match($sPatternProtocol, $sSourceString)) {
			$result = $sSourceString;

		} else {
			$result = 'http://'.$sSourceString.'/';

		}


		return $result;
		
	}
	
	
	/**
	 * Метод минифицирует html-код
	 * @return string
	 *
	 * @link http://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
	 */
	public function getStringMinifyHtml() {
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);

		$replace = array(
			'>',
			'<',
			'\\1'
		);

		$result = preg_replace($search, $replace, $this->source);

		return $result;
		
	}
	
	
	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================
	
	/**
	 * Функция возвращает нужную словоформу чистилительного по колчиеству
	 * @param int $n Количество
	 * @param array $arVars Массив словоформ (1, 2, 5)
	 * @return string
	 */
	static function getEnding($n, $arVars) {
		$string = new self();
		$result = $string->getStringEnding($n, $arVars);
		
		return $result;
		
	}
	
	
	/**
	 * Функция переводит дату из формата сайта в человеческий вид
	 * @param string $date Дата в формате текущего сайта
	 * @param string $format Формат в который необходимо её пробразовать
	 * @return string Дата в отформатированном виде
	 */
	static function getFormatDateHuman($date, $format) {
		$string = new self();
		$string->setSource($date);
		$result = $string->getStringFormatDateHuman($format);
		
		return $result;
		
	}
	
	
	/**
	 * Функция превращает прошедшую дату в формате сайта в человеческий вид
	 * Возвращает время, если дата соответствует сегодняшней
	 * Возвращает только время, дату, месяц, если дата текущего года
	 * @param string $date Дата в формате сайта
	 * @return string Дата в отформатированном виде
	 */
	static function getFormatDateHumanBack($date) {
		$string = new self();
		$string->setSource($date);
		$result = $string->getStringFormatDateHumanBack();
		
		return $result;
		
	}
	

	/**
	 * Метод возвращает число в формате суммы
	 * @param type $number Число для вывода суммы
	 * @param string $decimal Число знаков после запятой, например, не более двух
	 * @param string $groupDiv Разделитель разрядов
	 * @param string $fractionDiv Разделитель дробных значений
	 * @return string
	 */
	static function getFormatPrice($number, $decimal=2, $groupDiv='&#8201;', $fractionDiv=',') {
		$string = new self();
		$string->setSource($number);
		$result = $string->getStringFormatPrice($decimal, $groupDiv, $fractionDiv);
		
		return $result;
		
	}


	/**
	 * Функция формирует ссылку из текстовой строки
	 * Например site.ru превращается в http://site.ru/
	 * @param string $str
	 * @return string
	 */
	static function getLink($str) {
		$string = new self();
		$string->setSource($str);
		$result = $string->getStringLink();
		
		return $result;
		
	}
	
	
	/**
	 * Функция минифицирует html-код
	 * @param string $str html-код
	 * @return string
	 *
	 * @link http://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
	 */
	static function getMinifyHtml($str) {
		$string = new self();
		$string->setSource($str);
		$result = $string->getStringMinifyHtml();
		
		return $result;
		
	}
	
}
