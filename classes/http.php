<?php

namespace Dok\BX;

class Http {
	
	// =========================================================================
	// === ПАРАМЕТРЫ ОБЪЕКТА ===================================================
	// =========================================================================
	
	/**
	 * Исходные данные
	 * @var array|object
	 */
	private $data;
	
	
	/**
	 * Рабочий URL
	 * @var string
	 */
	private $url;


	// =========================================================================
	// === КОНСТРУКТОР, ГЕТТЕРЫ и СЕТТЕРЫ ======================================
	// =========================================================================
	
	/**
	 * Метод устанавливает значение исходных данных
	 * @param array|object $data
	 */
	public function setData($data) {
		$this->data = $data;
		
	}
	
	
	/**
	 * Метод устанавливает рабочий URL
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
		
	}
	
	
	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================
	
	/**
	 * Метод отправляет HTTP POST запрос на адрес $url
	 * @return string
	 */
	public function sendHttpPost() {
		$data_url = http_build_query($this->data);
		$data_len = strlen($data_url);

		$arResult = Array(
			'content' => file_get_contents(
				$this->url,
				false,
				stream_context_create(
					Array(
						'http' => Array(
							'method' => 'POST',
							'header' => "Connection: close\r\nContent-Length: $data_len\r\n",
							'content' => $data_url
						)
					)
				)
			),
			'headers' => $http_response_header,
		);

		return $arResult;
		
	}
	
	
	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================
	
	/**
	 * Функция отправляет HTTP POST запрос на адрес
	 * @param string $url
	 * @param array $data
	 * @return string
	 */
	static function sendPost($url, $data) {
		$http = new self();
		
		$http->setUrl($url);
		$http->setData($data);
		
		$result = $http->sendHttpPost();
		
		return $result;
		
	}
	
	
}
