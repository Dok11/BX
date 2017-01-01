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
	
	
	/**
	 * Метод вычисляет на сколько процентов первый массив похож на второй по значениям
	 * @param array $arTarget Массив с которым идет сравнение
	 * @return int Число в диапазоне 0-100
	 */
	public function getArrIntersectPercent($arTarget) {
		if(!is_array($arTarget)) {return;}
		
		$iSourceCount		= count($this->source);
		$arIntersect		= array_intersect($this->source, $arTarget);
		$arIntersectCount	= count($arIntersect);
		
		if($arIntersectCount > 0) {
			$iPercentage = round($arIntersectCount / $iSourceCount * 100);
			
		} else {
			$iPercentage = 0;
			
		}
		
		return (int) $iPercentage;
		
	}
	
	
	/**
	 * Метод вычисляет на сколько процентов первый массив похож на второй
	 * @param array $arTarget Массив с которым идет сравнение
	 * @return int Число в диапазоне 0-100
	 */
	public function getArrIntersectKeyPercent($arTarget) {
		if(!is_array($arTarget)) {return;}
		
		$iSourceCount		= count($this->source);
		$arIntersect		= array_intersect_key($this->source, $arTarget);
		$arIntersectCount	= count($arIntersect);
		
		if($arIntersectCount > 0) {
			$iPercentage = round($arIntersectCount / $iSourceCount * 100);
			
		} else {
			$iPercentage = 0;
			
		}
		
		return (int) $iPercentage;
		
	}
	
	
	/**
	 * Метод сводит два одномерных или двумерных массива к одному,
	 * а дублирующие ключи превращаются в массивы
	 * @param array $arNew Массив назначения
	 */
	public function getArrMergeExt($arNew) {
		$arResult = $this->source;

		foreach($this->source as $fieldKey=>$fieldValue) {
			if(is_array($fieldValue) && is_array($arNew[$fieldKey])) {
				$arResult[$fieldKey] = array_merge($fieldValue, $arNew[$fieldKey]);
				
			} elseif(is_array($fieldValue) && !is_array($arNew[$fieldKey])) {
				$arResult[$fieldKey][] = $arNew[$fieldKey];
				
			} elseif(is_array($arNew[$fieldKey]) && !is_array($fieldValue)) {
				$arResult[$fieldKey][] = $fieldValue;
				$arResult[$fieldKey] = $arNew[$fieldKey];
				
			} elseif(is_array($arNew) && $fieldValue !== $arNew[$fieldKey]) {
				$arResult[$fieldKey] = Array($fieldValue, $arNew[$fieldKey]);
				
			}

		}
		
		return $arResult;
		
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
	
	
	/**
	 * Функция вычисляет на сколько процентов первый массив похож на второй по значениям
	 * @param array $arSource Исходный массив
	 * @param array $arTarget Массив с которым идет сравнение
	 * @return int Число в диапазоне 0-100
	 */
	static function getIntersectPercent($arSource, $arTarget) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->getArrIntersectPercent($arTarget);

		return $result;
		
	}
	
	
	/**
	 * Функция вычисляет на сколько процентов первый массив похож на второй
	 * @param array $arSource Исходный массив
	 * @param array $arTarget Массив с которым идет сравнение
	 * @return int Число в диапазоне 0-100
	 */
	static function getIntersectKeyPercent($arSource, $arTarget) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->getArrIntersectKeyPercent($arTarget);

		return $result;
		
	}
	
	
	/**
	 * Функция сводит два одномерных или двумерных массива к одному,
	 * а дублирующие ключи превращаются в массивы
	 * @param array $arSource Массив источник
	 * @param array $arTarget Массив назначения (не ссылка)
	 */
	static function getMergeExt($arSource, $arTarget) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->getArrMergeExt($arTarget);

		return $result;
		
	}
	
	
}
