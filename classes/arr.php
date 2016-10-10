<?php

namespace Dok\BX;

class Arr {
	
	// =========================================================================
	// === ПАРАМЕТРЫ ОБЪЕКТА ===================================================
	// =========================================================================
	
	/**
	 * Исходный массив
	 * @var array
	 */
	private $source;
	
	
	// =========================================================================
	// === КОНСТРУКТОР, ГЕТТЕРЫ и СЕТТЕРЫ ======================================
	// =========================================================================
	
	/**
	 * Метод возвращает исходный массив
	 * @param string $source
	 */
	public function getSource() {
		return $this->source;
		
	}
	
	
	/**
	 * Метод устанавливает исходный массив
	 * @param string $source
	 */
	public function setSource($source=Array()) {
		if(is_array($source)) {
			$this->source = $source;
		} else {
			$this->source = Array();
		}
		
	}
	
	
	// =========================================================================
	// === МЕТОДЫ ОБЪЕКТА ======================================================
	// =========================================================================
	
	
	/**
	 * Метод разбивает массив на равное количество колонок
	 * @param int $iCount
	 * @return array
	 */
	public function getArrChunkCols($iCount) {
		$iCount		= (intval($iCount)) ? intval($iCount) : 1;
		
		$listlen	= count($this->source);
		$partlen	= max(floor($listlen / $iCount), 1);
		$partrem	= $listlen % $iCount;
		$partition	= Array();
		$mark		= 0;

		for($px = 0; $px < $iCount; $px++) {
			$incr = ($px < $partrem) ? $partlen + 1 : $partlen;
			$arSlice = array_slice($this->source, $mark, $incr);
			
			if($arSlice) {
				$partition[$px] = array_slice($this->source, $mark, $incr);
				$mark += $incr;
			}
			
		}
		
		return $partition;
		
	}
	
	
	/**
	 * Метод производит поиск по двумерному массиву
	 * @param string $sFieldName Имя ключа, по которому ищем
	 * @param string $sSearchVal Искомое значение
	 * @param string $sNeededFieldVal Значение какого поля нужно вернуть, если null - вернется ключ массива
	 */
	public function getArrFindInArr($sFieldName, $sSearchVal, $sNeededFieldVal='ID') {
		$result = array_search(
			$sSearchVal,
			array_column(
				$this->source,
				$sFieldName,
				$sNeededFieldVal
			)
		);

		return $result;
		
	}


	// =========================================================================
	// === СТАТИЧЕСКАЯ ОБВЯЗКА =================================================
	// =========================================================================

	/**
	 * Функция разбивает массив на равное количество колонок
	 * @param array $arIn
	 * @param int $iCount
	 * @return array
	 */
	static function getChunkCols($arIn, $iCount) {
		$arr = new self();
		
		$arr->setSource($arIn);
		$result = $arr->getArrChunkCols($iCount);

		return $result;
		
	}
	
	
	/**
	 * Функция производит поиск по двумерному массиву
	 * @param array $arIn Массив в котором производится поиск
	 * @param string $sFieldName Имя ключа, по которому ищем
	 * @param string $sSearchVal Искомое значение
	 * @param string $sNeededFieldVal Значение какого поля нужно вернуть, если null - вернется ключ массива
	 */
	static function getFindInArr($arIn, $sFieldName, $sSearchVal, $sNeededFieldVal='ID') {
		$arr = new self();
		
		$arr->setSource($arIn);
		$result = $arr->getArrFindInArr($sFieldName, $sSearchVal, $sNeededFieldVal);

		return $result;
		
	}
	
	
}
