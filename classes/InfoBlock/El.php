<?php

namespace Dok\BX\InfoBlock;

use Dok\BX\InfoBlock\Main as IB;

class El {

	// =========================================================================
	// === ПАРАМЕТРЫ ОБЪЕКТА ===================================================
	// =========================================================================

	/**
	 * Подключен ли модуль для работы с инфоблоками
	 * @var bool
	 */
	private $isModuleInclude;


	/**
	 * ID инфоблока выборки
	 * @var int
	 */
	private $iblockId;


	/**
	 * Порядок сортировки для выборки
	 * @var array
	 */
	private $order;


	/**
	 * Параметры выборки элементов
	 * @var array
	 */
	private $filter;


	/**
	 * Навигация выборки
	 * @var array
	 */
	private $nav;


	/**
	 * Группировка выборки
	 * @var array
	 */
	private $group;


	/**
	 * Поля и свойства выбираемых элементов
	 * @var array
	 */
	private $select;


	/**
	 * Способ выборки элементов Fetch или GetNext
	 * @var string
	 */
	private $getMethod;


	// =========================================================================
	// === КОНСТРУКТОР, ГЕТТЕРЫ и СЕТТЕРЫ ======================================
	// =========================================================================

	/**
	 * Конструктор объекта.
	 * Устанавливает дефолтные состояния объекта
	 */
	public function __construct() {
		$this->isModuleInclude = \Bitrix\Main\Loader::includeModule('iblock');

		$this->order		= Array('ID' => 'ASC');
		$this->filter		= Array();
		$this->nav			= false;
		$this->group		= false;
		$this->select		= Array('IBLOCK_ID', 'ID');
		$this->getMethod	= 'fetch';

	}


	/**
	 * Метод устанавливает ID инфоблока выборки
	 * @param array $arParams
	 */
	public function setIblockId($arParams) {
		if($arParams['IBLOCK_CODE']
			&& is_string($arParams['IBLOCK_CODE'])) {

			$iIblockId = IB::getByCode($arParams['IBLOCK_CODE']);

			$this->iblockId = $iIblockId;

		}

	}


	/**
	 * Метод устанавливает порядок сортировки
	 * @param array $arParams
	 */
	public function setOrder($arParams) {
		if(is_array($arParams)
			&& isset($arParams['ORDER'])
			&& is_array($arParams['ORDER'])) {

			$this->order = $arParams['ORDER'];

		}

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

		if($this->iblockId) {
			$this->filter['IBLOCK_ID'] = $this->iblockId;
		}

	}


	/**
	 * Метод устанавливает параметры навигации
	 * @param array $arParams
	 */
	public function setNav($arParams) {
		if(is_array($arParams)
			&& isset($arParams['NAV'])
			&& is_array($arParams['NAV'])) {

			$this->nav = $arParams['NAV'];

		}

	}


	/**
	 * Метод устанавливает параметры группировки в выборке
	 * @param array $arParams
	 */
	public function setGroup($arParams) {
		if(is_array($arParams)
			&& isset($arParams['GROUP'])
			&& is_array($arParams['GROUP'])) {

			$this->group = $arParams['GROUP'];

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


	/**
	 * Метод устанавливает тип выборки элементов Fetch или GetNext
	 * @param array $arParams
	 */
	public function setGetMethod($arParams) {
		if(is_array($arParams)
			&& isset($arParams['GET_NEXT'])
			&& $arParams['GET_NEXT'] === 'Y') {

			$this->getMethod = 'getNext';

		}


		// Если в SELECT есть DETAIL_PAGE_URL, то метод getNext
		if(in_array('DETAIL_PAGE_URL', $this->select)) {
			$this->getMethod = 'getNext';

		}

	}


	// =========================================================================
	// === CRUD ================================================================
	// =========================================================================

	/**
	 * Метод возвращает массив элементов инфоблока
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> ORDER
	 *		<li> FILTER
	 *		<li> NAV
	 *		<li> GROUP
	 *		<li> SELECT
	 * @return array
	 */
	public function getElList($arParams) {
		if(!is_array($arParams)) {return;}

		$result = false;

		$this->setIblockId($arParams);
		$this->setOrder($arParams);
		$this->setFilter($arParams);
		$this->setNav($arParams);
		$this->setGroup($arParams);
		$this->setSelect($arParams);
		$this->setGetMethod($arParams);

		if($this->isModuleInclude
			&& intval($this->iblockId)) {

			$result = Array();

			$resElements = \CIBlockElement::GetList(
				$this->order,
				$this->filter,
				$this->group,
				$this->nav,
				$this->select
			);

			if($this->getMethod === 'getNext') {
				while($arElement = $resElements->GetNext()) {
					$result[] = $arElement;
				}

			} else {
				while($arElement = $resElements->Fetch()) {
					$result[] = $arElement;
				}

			}


		}

		return $result;

	}


	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================

	/**
	 * Метод возвращает значение поля элемента указанного в фильтре
	 * @param string $sIblockCode Символьный код инфоблока
	 * @param int $iElementId ID элемента
	 * @param string $sFieldName Код поля
	 * @return mixed
	 */
	public function getElField($sIblockCode, $iElementId, $sFieldName) {
		$result = null;

		$arParams = Array(
			'IBLOCK_CODE' => $sIblockCode,
			'FILTER' => Array(
				'ID' => intval($iElementId),
			),
			'SELECT' => Array(
				$sFieldName
			),
		);

		$arElementList = $this->getElList($arParams);

		if($arElementList) {
			foreach($arElementList as $arElement) {break;}

			if(isset($arElement[$sFieldName])) {
				$result = $arElement[$sFieldName];
			}

		}

		return $result;

	}


	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================

	/**
	 * Функция возвращает массив элементов инфоблока
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> ORDER
	 *		<li> FILTER
	 *		<li> NAV
	 *		<li> GROUP
	 *		<li> SELECT
	 * @return array
	 */
	static function getList($arParams) {
		$el = new self;
		$arResult = $el->getElList($arParams);

		return $arResult;

	}


	/**
	 * Функция возвращает значение поля элемента указанного в фильтре
	 * @param string $sIblockCode Символьный код инфоблока
	 * @param int $iElementId ID элемента
	 * @param string $sFieldName Код поля
	 * @return mixed
	 */
	static function getField($sIblockCode, $iElementId, $sFieldName) {
		$el = new self;
		$arResult = $el->getElField($sIblockCode, $iElementId, $sFieldName);

		return $arResult;

	}

}
