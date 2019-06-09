<?php
	class AutoloadHandler {
		/**
		 * @param $className string name of the file to include
		 * @throws Exception if include file not found
		 */
		static function autoloader(string $className) {
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
				$fileName = "classes/". $dir. "/". $baseFileName;
				if (file_exists($fileName)) {
					break;
				}
			}
			
			if (($fileName === NULL) || !file_exists($fileName)) {
				$msg = sprintf("Autoload not found: classname = \"%s\", filename = \"%s\"", $className, $fileName);
				throw new Exception($msg);
			}
			
			include_once($fileName);
		}
		
		static function classNameToFileName( $className ) {
			$pattern = "/([A-Z])/";
			$ret = preg_replace( $pattern, "_$1", $className );
			$ret = strtolower( $ret );
			$ret = preg_replace( "/^_/", "", $ret );
			$ret = $ret. ".php";
			return $ret;
		}
	}
	
	
