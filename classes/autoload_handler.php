<?php
	class AutoloadHandler {
		/**
		 * @param $className string name of the file to include
		 * @return bool
		 * @throws Exception if include file not found
		 */
		static function autoloader(string $className) {
			$rootDir = __DIR__. "/../";
			
			$fileName = NULL;
			
			$dirs = [
				".",
				"exception",
				"model",
				"view",
				"controller",
			];
			
			$baseFileName = self::classNameToFileName($className);

			foreach ($dirs as $dir) {
				$fileName = $rootDir. "classes/". $dir. "/". $baseFileName;
				if (file_exists($fileName)) {
					break;
				}
			}
			
			if (($fileName === NULL) || !file_exists($fileName)) {
				return FALSE;
			}
			
			include_once($fileName);
		}
		
		/**
		 * Converts a CamelCase class name into a snake_case file name.
		 *
		 * @param $className
		 * @return mixed|string
		 */
		static function classNameToFileName( $className ) {
			$pattern = "/([A-Z])/";
			$ret = preg_replace( $pattern, "_$1", $className );
			$ret = strtolower( $ret );
			$ret = preg_replace( "/^_/", "", $ret );
			$ret = $ret. ".php";
			return $ret;
		}
	}
	
	
