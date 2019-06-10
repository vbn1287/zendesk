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
					
					$type = substr($file, 0, -5);
					
					$className = ucfirst(substr($type, 0, -1)); // removes the "s" suffix: "users" => "User". It may need adjustment if new type is introduced
					
					if (!is_array($data)) {
						throw new DataFileLoadErrorException();
					}
					
					$this->data[$type] = [];
						
					foreach ($data as $item) {
						if (!array_key_exists("_id", $item)) {
							throw new DataFileLoadErrorException();
						}
						
						$id = $item["_id"];
						
						$obj = new $className;
						
						$this->data[$type][$id] = $obj->fillFromArray($item);
					}
					
				}
			}
		}
		
		function search($type, $field, $value) {
			if ($this->data === NULL) {
				$this->readData();
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

				case "3":
					// fall-through
				case "organizations":
					return $this->searchOrganization($field, $value);
				
			}
			
			return [];
		}
		
		protected function getForeignValues($has, $selfValue, $foreignTable, $foreignColumn) {
			$ret = [];
			
			foreach ($this->data[$foreignTable] as $foreignItem) {
				if ($foreignItem->getAttrValue($foreignColumn) == $selfValue) {
					if ($has === "hasOne") {
						return $foreignItem;
					} else {
						$ret[] = $foreignItem;
					}
				}
			}
			
			if ($has === "hasOne") {
				return NULL;
			}
			
			return $ret;
		}
		
		protected function addRelations(Item $item, $relations) {
			foreach ($relations as $relationName => $rules) {
				list($has, $selfField, $foreignField) = $rules;
				list($foreignTable, $foreignColumn) = explode(".", $foreignField);
				
				$relatedItems = $this->getForeignValues($has, $item->getAttrValue($selfField), $foreignTable, $foreignColumn);
				
				$item->addRelatedItems($relationName, $relatedItems);
			}
			
			return $item;
		}
		
		protected function searchUsers($field, $value) {
			$relations = User::getRelations();
			$type = "users";
			return $this->searchItem($type, $relations, $field, $value);
		}
		
		protected function searchTickets($field, $value) {
			$relations = Ticket::getRelations();
			$type = "tickets";
			return $this->searchItem($type, $relations, $field, $value);
		}
		
		protected function searchOrganization($field, $value) {
			$relations = Organization::getRelations();
			$type = "organizations";
			return $this->searchItem($type, $relations, $field, $value);
		}
		
		protected function searchItem($type, $relations, $field, $value) {
			$ret = [];
			
			foreach ($this->data[$type] as $item) {
				if ($item->getAttrValue($field) == $value) {
					$item = $this->addRelations($item, $relations);
					$ret[] = $item;
				}
			}
			
			return $ret;
		}
		
	}