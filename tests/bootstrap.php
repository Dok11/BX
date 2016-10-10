<?php

/**
 * A recursive directory crawler that looks everywhere for the Bitrix
 * core file, known as "prolog_before.php", and includes it if the search
 * is successful.
 *
 * @author Ivan Tsirulev <ivan.tsirulev@gmail.com>
 * @link http://pastebin.com/RQv119GX
 */
final class BitrixCoreSeeker {

	const relativeCorePath = "/bitrix/modules/main/include/prolog_before.php";

	public function includeBitrixCore() {
		if ($corePath = $this->lookForCore(__DIR__)) {
			$this->prepareEnvironment($corePath);
			require_once($corePath);
		}

		return $corePath ? true : false;
	}

	private function lookForCore($dir) {
		$dirObject = dir($dir);

		while ($subDir = $dirObject->read()) {
			if (file_exists("$dir/$subDir" . self::relativeCorePath)) {
				return "$dir/$subDir" . self::relativeCorePath;
			}
		}

		$dirObject->close();

		return dirname($dir) == $dir ? null : $this->lookForCore(dirname($dir));
	}

	private function prepareEnvironment($corePath) {
		$docRoot = str_replace(self::relativeCorePath, "", $corePath);

		$_SERVER['DOCUMENT_ROOT'] = $docRoot;
		$_SERVER['SCRIPT_NAME'] = realpath($_SERVER['SCRIPT_NAME']);
	}

}

$bitrixSeeker = new BitrixCoreSeeker();
$bitrixSeeker->includeBitrixCore();
