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
	 * @return string $source
	 */
	public function getSource() {
		return $this->source;
		
	}
	
	
	/**
	 * Метод устанавливает исходный массив
	 * @param array|string $source
	 */
	public function setSource($source=Array()) {
		if(is_array($source)) {
			$this->source = $source;
		} else {
			$this->source = Array($source);
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
	 * @return mixed
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
	 * Метод переводит ассоциативный массив в строку с атрибутами html-элемента
	 * @return string
	 */
	public function getArrHtmlAttr() {
		$result = '';

		foreach($this->source as $field=>$value) {
			if($value === true) {
				$result .= $field.' ';

			} else {
				$result .= $field.'="'. \htmlspecialchars($value) .'" ';

			}

		}

		return \trim($result);

	}
	
	
	/**
	 * Метод вычисляет на сколько процентов первый массив похож на второй по значениям
	 * @param array $arTarget Массив с которым идет сравнение
	 * @return int Число в диапазоне 0-100
	 */
	public function getArrIntersectPercent($arTarget) {
		if(!is_array($arTarget)) {return null;}
		
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
		if(!is_array($arTarget)) {return null;}
		
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
	 * @return array
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
	
	
	/**
	 * Метод сортирует двумерый массив по нужному полю
	 * @param string $sField Ключ массива для сортировки
	 * @param int|bool $sortOrder Порядок для сортировки SORT_ASC|SORT_DESC|false
	 * @return array Отсортированный масиив
	 */
	public function sortArrByField($sField, $sortOrder=false) {
		if(!$this->source) {return null;}
		
		$arResult = $this->source;

		$arSortData = Array();

		foreach($this->source as $arItem) {
			foreach($arItem as $keyField=>$valField) {
				if($keyField == $sField) {
					$arSortData[] = $valField;
				}
			}
		}

		if($arSortData) {
			if($sortOrder) {
				\array_multisort($arSortData, $sortOrder, $arResult);

			} else {
				\array_multisort($arSortData, $arResult);

			}

		}

		return $arResult;
		
	}
	
	
	/**
	 * Метод сортирует двумерый массив по наборю ключей
	 * @param array $arFields Поля и направления сортировки
	 * @return array Отсортированный масиив
	 */
	public function sortArrByFields($arFields) {
		$arResult = $this->source;


		// Определение массива определяющего сортировку
		$arSortData = Array();
		foreach($this->source as $arItem) {
			foreach($arItem as $keyField=>$valField) {
				if($arFields[$keyField]) {
					$arSortData[$keyField][] = $valField;
				}
			}
		}
		// ---------------------------------------------------------------------


		// Сортировка
		if($arSortData) {
			$arMultisort = Array();
			foreach($arFields as $k=>$v) {
				$arMultisort[] = '$arSortData["'.$k.'"], '.$v;
			}
			
			eval('array_multisort('.implode(', ', $arMultisort).', $arResult);');

		}
		// ---------------------------------------------------------------------


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
	 * @return mixed
	 */
	static function getFindInArr($arIn, $sFieldName, $sSearchVal, $sNeededFieldVal='ID') {
		$arr = new self();
		
		$arr->setSource($arIn);
		$result = $arr->getArrFindInArr($sFieldName, $sSearchVal, $sNeededFieldVal);

		return $result;
		
	}


	/**
	 * Функция переводит ассоциативный массив в строку с атрибутами html-элемента
	 * @param array $arIn Ассоциативный массив
	 * @return string
	 */
	static function getHtmlAttr($arIn) {
		$arr = new self();

		$arr->setSource($arIn);
		$result = $arr->getArrHtmlAttr();

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
	 * @return array
	 */
	static function getMergeExt($arSource, $arTarget) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->getArrMergeExt($arTarget);

		return $result;
		
	}
	
	
	/**
	 * Функция сортирует двумерый массив по нужному полю
	 * @param array $arSource Исходный массив
	 * @param string $sField Ключ массива для сортировки
	 * @param int|bool $sortOrder Порядок для сортировки SORT_ASC|SORT_DESC|false
	 * @return array Отсортированный масиив
	 */
	static function sortByField($arSource, $sField, $sortOrder) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->sortArrByField($sField, $sortOrder);

		return $result;
		
	}
	
	
	/**
	 * Функция сортирует двумерый массив по наборю ключей
	 * @param array $arSource Исходный массив
	 * @param array $arFields Поля и направления сортировки
	 * @return array Отсортированный масиив
	 */
	static function sortByFields($arSource, $arFields) {
		$arr = new self();
		
		$arr->setSource($arSource);
		$result = $arr->sortArrByFields($arFields);

		return $result;
		
	}
	
	
}
