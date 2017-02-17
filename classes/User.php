<?php

namespace Dok\BX;

class User {
	
	// =========================================================================
	// === ПАРАМЕТРЫ ОБЪЕКТА ===================================================
	// =========================================================================

	/**
	 * @var array Параметры выборки элементов
	 */
	private $filter;


	/**
	 * @var array Поля и свойства выбираемых элементов
	 */
	private $select;


	// =========================================================================
	// === КОНСТРУКТОР, ГЕТТЕРЫ и СЕТТЕРЫ ======================================
	// =========================================================================

	/**
	 * Конструктор объекта.
	 * Устанавливает дефолтные состояния объекта
	 */
	public function __construct() {
		$this->filter		= Array();
		$this->select		= Array();

	}


	/**
	 * Метод устанавливает фильтр выборки
	 * @param array $arParams
	 */
	public function setFilter($arParams) {
		if(is_array($arParams)
			&& isset($arParams['FILTER'])
			&& is_array($arParams['FILTER'])) {

			$this->filter = $arParams['FILTER'];

		}
	}


	/**
	 * Метод устанавливает необходимые поля и свойства выбираемых элементов
	 * @param array $arParams
	 */
	public function setSelect($arParams) {
		if(is_array($arParams)
			&& isset($arParams['SELECT'])
			&& is_array($arParams['SELECT'])) {

			$this->select = $arParams['SELECT'];

		}

	}


	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================

	/**
	 * Метод добавляет пользователя в систему
	 * @param $arParams
	 * @return bool|int
	 */
	public function addUser($arParams) {
		$user = new \CUser;

		$id = $user->Add($arParams);

		return $id;

	}


	/**
	 * Метод возвращает массив пользователей
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> FILTER
	 *		<li> SELECT
	 * @return array
	 */
	public function getUserList($arParams) {

		$this->setFilter($arParams);
		$this->setSelect($arParams);

		$resUserList = \CUser::GetList(
			($by='id'),
			($order='asc'),
			$this->filter,
			$this->select
		);

		$result = Array();
		while($arUser = $resUserList->Fetch()) {
			$result[$arUser['ID']] = $arUser;
		}

		return $result;

	}


	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================

	/**
	 * Функция добавляет пользователя в систему
	 * @param $arParams
	 * @return bool|int
	 */
	static function add($arParams) {
		$user = new self;
		$result = $user->addUser($arParams);

		if(intval($result) > 0) {
			// all ok
		} else {
			$result = $user->LAST_ERROR;
		}

		return $result;

	}


	/**
	 * Функция возвращает массив пользователей
	 * @param array $arParams Массив с параметрами выборки
	 *		<li> FILTER
	 *		<li> SELECT
	 * @return array
	 */
	static function getList($arParams) {
		$user = new self;
		$result = $user->getUserList($arParams);

		return $result;

	}


	/**
	 * Метод возвращает массив групп пользователя
	 * @param int $userId ID пользователя
	 * @return array
	 */
	static function getGroupList($userId) {
		return \CUser::GetUserGroup($userId);

	}


}
