<?php

namespace Dok\BX;

class Help {

	/**
	 * Функция конвертирует
	 * @param type $str
	 * @return string
	 */
	static function convertStringToLink($str='') {
		$sPatternProtocol = '/[a-z]{2,6}:\/\//';

		if(preg_match($sPatternProtocol, $str)) {
			$result = $str;

		} else {
			$result = 'http://'.$str.'/';

		}


		return $result;
	}


	/**
	 * Функция возвращает нужную словоформу чистилительного по колчиеству
	 * @param int $n Количество
	 * @param array $arVars Массив словоформ (1, 2, 5)
	 * @return string Результат
	 */
	static function getEnding($n, $arVars) {
		if(!intval($n)) {return false;}

		$n = intval($n);

		$plural	= $n%10==1&&$n%100!=11?$arVars[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$arVars[1]:$arVars[2]);

		return $plural;
	}


	/**
	 * Функция отправляет HTTP POST запрос на адрес
	 * @param string $url
	 * @param array $data
	 * @return string
	 */
	static function httpPost($url, $data) {
		$data_url = http_build_query($data);
		$data_len = strlen($data_url);

		$arResult = Array(
			'content'	=> file_get_contents(
				$url,
				false,
				stream_context_create(
					Array(
						'http'	=> Array(
							'method'	=> 'POST',
							'header'	=> "Connection: close\r\nContent-Length: $data_len\r\n",
							'content'	=> $data_url
						)
					)
				)
			),
			'headers'	=> $http_response_header,
		);

		return $arResult;
	}


	/**
	 * Функция переводит дату из формата сайта в человеческий вид
	 * @param string $date Дата в формате текущего сайта
	 * @param string $format Формат в который необходимо её пробразовать
	 * @return string Дата в отформатированном виде
	 */
	static function formatDateHuman($date, $format) {
		$result	= FormatDateFromDB($date, $format);								// Перевод даты в человеческий формат
		$result = preg_replace('/^0/', '', $result);							// Убираем первый ноль из даты
		$result = str_replace(' ', '&nbsp;', $result);							// Ставим неразрывные пробелы

		if(strstr($format, 'MMMM') && LANGUAGE_ID == 'ru') {					// Проверка, нужно ли приводить дату к нижнему регистру
			$result = mb_strtolower($result, 'UTF-8');							// Приведение даты к нижнему регистру
		}

		return $result;															// Возвращаем результат
	}


	/**
	 * Функция превращает дату в формате сайта в человеческий вид
	 * Возвращает время, если дата соответствует сегодняшней
	 * Возвращает только время, дату, месяц, если дата текущего года
	 * @param string $date Дата в формате сайта
	 * @return string Дата в отформатированном виде
	 */
	static function formatDateHumanSmart($date) {
		// Исходные параметры: парсинг даты и таймштампы
		$arDate		= ParseDateTime($date);
		$iTimeStamp	= MakeTimeStamp($date);

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
		$sDateFormatted = \ALS\Helper\Help::formatDateHuman($date, $sFormat);

		return $sDateFormatted;
	}


	/**
	 * Функция возвращает число в формате суммы
	 * @param type $number Число для вывода суммы
	 * @param string $opt_decimal Число знаков после запятой, например, не более двух
	 * @param string $opt_groupSeparator Разделитель разрядов
	 * @param string $opt_fractionSeparator Разделитель дробных значений
	 */
	static function formatPrice($number, $opt_decimal=2, $opt_groupSeparator='&#8201;', $opt_fractionSeparator=',') {
		if(LANGUAGE_ID === 'en') {
			$opt_fractionSeparator = '.';
			$opt_groupSeparator = ',';
		}

		$iRoundNumber	= round($number, $opt_decimal);
		$arNumberParts	= explode('.', $iRoundNumber);
		$iDecimal		= ($arNumberParts[1]) ? strlen($arNumberParts[1]) : 0;
		$iSeparatorTmp	= ($number>=10000) ? "#": false;

		$sNumber		= number_format($number, $iDecimal, $opt_fractionSeparator, $iSeparatorTmp);
		$sNumber		= str_replace("#", $opt_groupSeparator, $sNumber);

		if(!$sNumber) {
			$sNumber = 0;
		}

		return $sNumber;
	}


	/**
	 * Функция минифицирует html-код
	 * @param string $content html-код
	 * @return string
	 *
	 * @link http://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
	 */
	static function minifyHtml($content) {
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

		$result = preg_replace($search, $replace, $content);

		return $result;

	}

}
