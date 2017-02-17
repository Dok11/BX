<?php

namespace Dok\BX\InfoBlock;

use Dok\BX\InfoBlock\Main as IB;

class Sect {

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
	 * Параметры выборки разделов
	 * @var array
	 */
	private $filter;


	/**
	 * Возвращать ли поле ELEMENT_CNT - количество элементов в разделе
	 * @var bool Если true, то учитываются параметры фильтра:
	 *	<li> ELEMENT_SUBSECTIONS - подсчитывать элементы вложенных подразделов
	 *		 или нет (Y|N). По умолчанию Y;
	 *	<li> CNT_ALL - подсчитывать еще неопубликованные элементы (Y|N).
	 *	<li> Актуально при установленном модуле документооборота; CNT_ACTIVE -
	 *		 при подсчете учитывать активность элементов (Y|N). По умолчанию N.
	 *		 Учитывается флаг активности элемента ACTIVE и даты начала и окончания
	 *		 активности.
	 */
	private $incCnt;


	/**
	 * Поля и свойства выбираемых разделов
	 * @var array
	 */
	private $select;


	/**
	 * Способ выборки разделов Fetch или GetNext
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
		$this->incCnt		= false;
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
	 * Метод устанавливает выборку количества разделов
	 * @param array $arParams
	 */
	public function setIncCnt($arParams) {
		if(is_array($arParams)
			&& isset($arParams['INC_CNT'])
			&& $arParams['INC_CNT'] === 'Y') {

			$this->incCnt = true;

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


		// Если в SELECT есть SECTION_PAGE_URL, то метод getNext
		if(in_array('SECTION_PAGE_URL', $this->select)) {
			$this->getMethod = 'getNext';

		}

	}


	// =========================================================================
	// === CRUD ================================================================
	// =========================================================================

	/**
	 * Метод возвращает массив разделов инфоблока
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> ORDER
	 *		<li> FILTER
	 *		<li> INC_CNT
	 *		<li> SELECT
	 *		<li> GET_NEXT
	 * @return array
	 */
	public function getSectList($arParams) {
		if(!is_array($arParams)) {return;}

		$result = false;

		$this->setIblockId($arParams);
		$this->setOrder($arParams);
		$this->setFilter($arParams);
		$this->setIncCnt($arParams);
		$this->setSelect($arParams);
		$this->setGetMethod($arParams);

		if($this->isModuleInclude
			&& intval($this->iblockId)) {

			$result = Array();

			$resSections = \CIBlockSection::GetList(
				$this->order,
				$this->filter,
				$this->incCnt,
				$this->select
			);

			if($this->getMethod === 'getNext') {
				while($arSection = $resSections->GetNext()) {
					$result[$arSection['ID']] = $arSection;
				}

			} else {
				while($arSection = $resSections->Fetch()) {
					$result[$arSection['ID']] = $arSection;
				}

			}


		}

		return $result;

	}


	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================

	/**
	 * Метод возвращает список разделов в виде цепочки навигации
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE - Символьный код инфоблока
	 *		<li> FILTER - Массив с SECTION_ID
	 *		<li> SELECT - Массив выбираемых полей
	 *		<li> GET_NEXT - Тип выборки
	 *		<li> REVERSE - Y, если по цепочке нужно будет идти в обратном порядке
	 * @return array
	 */
	public function getSectNavChain($arParams) {
		$result = null;

		$this->setIblockId($arParams);
		$this->setSelect($arParams);
		$this->setGetMethod($arParams);

		if($this->isModuleInclude
			&& intval($this->iblockId)) {

			$resChain = \CIBlockSection::GetNavChain(
				$this->iblockId,
				(int) $arParams['FILTER']['SECTION_ID'],
				$this->select
			);

			$result = Array();

			while($arSection = $resChain->Fetch()) {
				$result[] = $arSection;
			}

			if($arParams['REVERSE'] && $arParams['REVERSE'] === 'Y') {
				$result = array_reverse($result, true);
			}

		}

		return $result;

	}


	/**
	 * Метод возвращает значение поля раздела указанного в фильтре
	 * @param $sIblockCode Символьный код инфоблока
	 * @param $iSectionId ID раздела
	 * @param $sFieldName Код поля
	 * @return mixed
	 */
	public function getSectField($sIblockCode, $iSectionId, $sFieldName) {
		$result = null;

		$arParams = Array(
			'IBLOCK_CODE' => $sIblockCode,
			'FILTER' => Array(
				'ID' => $iSectionId,
			),
			'SELECT' => Array(
				$sFieldName
			),
		);

		$arSectionList = $this->getSectList($arParams);

		if($arSectionList) {
			foreach($arSectionList as $arSection) {break;}

			if(isset($arSection[$sFieldName])) {
				$result = $arSection[$sFieldName];
			}

		}

		return $result;

	}


	/**
	 * Метод возвращает значение поля раздела указанного в фильтре
	 * @param $sIblockCode Символьный код инфоблока
	 * @param $sSectionCode Символьный код раздела
	 * @param $sFieldName Код поля
	 * @return mixed
	 */
	public function getSectFieldByCode($sIblockCode, $sSectionCode, $sFieldName) {
		$result = null;

		$arParams = Array(
			'IBLOCK_CODE' => $sIblockCode,
			'FILTER' => Array(
				'CODE' => $sSectionCode,
			),
			'SELECT' => Array(
				$sFieldName
			),
		);

		$arSectionList = $this->getSectList($arParams);

		if($arSectionList) {
			foreach($arSectionList as $arSection) {break;}

			if(isset($arSection[$sFieldName])) {
				$result = $arSection[$sFieldName];
			}

		}

		return $result;

	}


	/**
	 * Метод возвращает список разделов, отсортированный в порядке «полного развернутого дерева»
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> FILTER
	 *		<li> SELECT
	 *		<li> GET_NEXT
	 * @return array
	 */
	public function getSectTreeList($arParams) {
		if(!is_array($arParams)) {return;}

		$this->setIblockId($arParams);
		$this->setFilter($arParams);
		$this->setSelect($arParams);
		$this->setGetMethod($arParams);

		if($this->isModuleInclude
			&& intval($this->iblockId)) {

			$result = Array();

			$resSections = \CIBlockSection::GetTreeList(
				$this->filter,
				$this->select
			);

			if($this->getMethod === 'getNext') {
				while($arSection = $resSections->GetNext()) {
					$result[$arSection['ID']] = $arSection;
				}

			} else {
				while($arSection = $resSections->Fetch()) {
					$result[$arSection['ID']] = $arSection;
				}

			}


		}

		return $result;

	}


	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================

	/**
	 * Функция возвращает массив разделов инфоблока
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> ORDER
	 *		<li> FILTER
	 *		<li> INC_CNT
	 *		<li> SELECT
	 *		<li> GET_NEXT
	 * @return array
	 */
	static function getList($arParams) {
		$sect = new self;
		$arResult = $sect->getSectList($arParams);

		return $arResult;

	}


	/**
	 * Функция возвращает список разделов в виде цепочки навигации
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE - Символьный код инфоблока
	 *		<li> FILTER - Массив с SECTION_ID
	 *		<li> SELECT - Массив выбираемых полей
	 *		<li> GET_NEXT - Тип выборки
	 *		<li> REVERSE - Y, если по цепочке нужно будет идти в обратном порядке
	 * @return array
	 */
	static function getNavChain($arParams) {
		$sect = new self;
		$arResult = $sect->getSectNavChain($arParams);

		return $arResult;

	}


	/**
	 * Функция возвращает значение поля раздела указанного в фильтре
	 * @param string $sIblockCode Символьный код инфоблока
	 * @param int $iSectionId ID раздела
	 * @param string $sFieldName Код поля
	 * @return mixed
	 */
	static function getField($sIblockCode, $iSectionId, $sFieldName) {
		$sect = new self;
		$arResult = $sect->getSectField($sIblockCode, $iSectionId, $sFieldName);

		return $arResult;

	}


	/**
	 * Функция возвращает значение поля раздела указанного в фильтре
	 * @param string $sIblockCode Символьный код инфоблока
	 * @param string $sSectionCode Символьный код раздела
	 * @param string $sFieldName Код поля
	 * @return mixed
	 */
	static function getFieldByCode($sIblockCode, $sSectionCode, $sFieldName) {
		$sect = new self;
		$arResult = $sect->getSectFieldByCode($sIblockCode, $sSectionCode, $sFieldName);

		return $arResult;

	}


	/**
	 * Функция возвращает список разделов, отсортированный в порядке «полного развернутого дерева»
	 * @param array $arParams Массив с параметрами выборки <br />
	 *		<li> IBLOCK_CODE
	 *		<li> FILTER
	 *		<li> SELECT
	 *		<li> GET_NEXT
	 * @return array
	 */
	static function getTreeList($arParams) {
		$sect = new self;
		$arResult = $sect->getSectTreeList($arParams);

		return $arResult;

	}

}
