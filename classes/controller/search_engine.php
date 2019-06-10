<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 11:33
	 */
	class SearchEngine {
		protected $data = NULL;
		protected $dataDir = NULL;

		function __construct() {
			$this->dataDir = __DIR__. "/../../data/";
		}
		
		protected function readData() {
			$files = scandir($this->dataDir);
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
					
					$this->data[substr($file, 0, -5)] = $data;
				}
			}
		}
		
		protected function reindexData() {
			
			foreach ($this->data as $type => $records) {
			
				$reindexed = [];
				
				foreach ($records as $rec) {
					$reindexed[$rec["_id"]] = $rec;
				}
				
				$this->data[$type] = $reindexed;
			}
		}
		
		function search($type, $field, $value) {
			if ($this->data === NULL) {
				$this->readData();
				$this->reindexData();
			}
			
			switch ($type) {
				case "1":
					// fall-through
				case "users":
					return $this->searchUsers($field, $value);
				
				case "2":
					// fall-through
				case "tickets":
					return $this->searchTickets($field, $value);

				case "1":
					// fall-through
				case "user":
					return $this->searchUser($field, $value);
				
			}
		}
		
		protected function getForeignValues($has, $selfValue, $foreignTable, $foreignColumn) {
			$ret = [];
			
			foreach ($this->data[$foreignTable] as $foreignRecord) {
				if (array_key_exists($foreignColumn, $foreignRecord) && $foreignRecord[$foreignColumn] == $selfValue) {
					if ($has === "hasOne") {
						return $foreignRecord;
					} else {
						$ret[] = $foreignRecord;
					}
				}
			}
			
			if ($has === "hasOne") {
				return NULL;
			}
			
			return $ret;
		}
		
		protected function addRelations($item, $relations) {
			foreach ($relations as $relationName => $rules) {
				list($has, $selfField, $foreignField) = $rules;
				list($foreignTable, $foreignColumn) = explode(".", $foreignField);
				
				$item[$relationName] = $this->getForeignValues($has, $item[$selfField], $foreignTable, $foreignColumn);
			}
			
			return $item;
		}
		
		protected function searchUsers($field, $value) {
			$ret = [];
			
			foreach ($this->data["users"] as $user) {
				if ($user[$field] == $value) {
					$relations = User::getRelations();
					$user = $this->addRelations($user, $relations);
					$ret[] = $user;
				}
			}
				
			return $ret;
		}
		
	}