<?php
	
	/**
	 * This class is responsible for reading the JSON data files.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.11.
	 * Time: 1:17
	 */
	class DataHandler {
		protected $dataDir = NULL;
		
		function __construct() {
			$this->dataDir = __DIR__. "/../../data/";
		}
		
		function readData() {
			$files = scandir($this->dataDir);
			
			$ret = [];
			
			foreach ($files as $file) {
				if (substr($file, -5) === ".json") {
					$content = file_get_contents($this->dataDir. $file);
					
					if ($content === FALSE) {
						throw new DataFileLoadErrorException();
					}
					
					$data = json_decode($content, TRUE);
					
					if (json_last_error() !== JSON_ERROR_NONE) {
						throw new DataFileLoadErrorException();
					}
					
					$type = substr($file, 0, -5);
					
					$className = ucfirst(substr($type, 0, -1)); // removes the "s" suffix: "users" => "User". It may need adjustment if new type is introduced
					
					if (!is_array($data)) {
						throw new DataFileLoadErrorException();
					}
					
					$ret[$type] = [];
					
					foreach ($data as $item) {
						if (!array_key_exists("_id", $item)) {
							throw new DataFileLoadErrorException();
						}
						
						$id = $item["_id"];
						
						$obj = new $className;
						
						/** @var Item $obj */
						$ret[$type][$id] = $obj->fillFromArray($item);
					}
					
				}
			}

			return $ret;
		}
		
	}