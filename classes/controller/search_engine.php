<?php
	
	/**
	 * This class contains the main search functionality.
	 *
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
						
						/** @var Item $obj */
						$this->data[$type][$id] = $obj->fillFromArray($item);
					}
					
				}
			}
		}
		
		/**
		 * Converts an integer type code (1, 2, 3) into its string representation ("users", "tickets", "organizations").
		 *
		 * @param $type
		 * @return mixed
		 */
		static function stringifyType($type) {
			$lookUp = [
				1 => "users",
				2 => "tickets",
				3 => "organizations",
			];
			
			if (array_key_exists($type, $lookUp)) {
				$type = $lookUp[$type];
			}
			
			return $type;
		}
		
		/**
		 * Executes the search functionality, based on the type, field name and search value.
		 *
		 * @param $type
		 * @param $field
		 * @param $value
		 * @return array
		 */
		function search($type, $field, $value) {
			if ($this->data === NULL) {
				$this->readData();
			}
			
			switch (self::stringifyType($type)) {
				case "users":
					return $this->searchUsers($field, $value);
				
				case "tickets":
					return $this->searchTickets($field, $value);

				case "organizations":
					return $this->searchOrganization($field, $value);
				
			}
			
			return [];
		}
		
		protected function getForeignValues($has, $selfValue, $foreignTable, $foreignColumn) {
			$ret = [];
			
			foreach ($this->data[$foreignTable] as $foreignItem) {
				/** @var Item $foreignItem */
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
		
		/**
		 * Adds the relations to the pure Item.
		 *
		 * @param Item $item
		 * @param      $relations
		 * @return Item
		 */
		protected function addRelations(Item $item, $relations) {
			foreach ($relations as $relationName => $rules) {
				list($has, $selfField, $foreignField) = $rules;
				list($foreignTable, $foreignColumn) = explode(".", $foreignField);
				
				$relatedItems = $this->getForeignValues($has, $item->getAttrValue($selfField), $foreignTable, $foreignColumn);
				
				$this->relatedItems[$relationName] = $relatedItems;
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
		
		/**
		 * Iterates over the data set and collects the items that have their searched value.
		 *
		 * @param $type
		 * @param $relations
		 * @param $field
		 * @param $value
		 * @return array
		 */
		protected function searchItem($type, $relations, $field, $value) {
			$ret = [];
			
			foreach ($this->data[$type] as $item) {
				if (self::isEqual($item, $field, $value)) {
					$item = $this->addRelations($item, $relations);
					$ret[] = $item;
				}
			}
			
			return $ret;
		}
		
		/**
		 * Implements the comparison of an Item based on its given field and a search value.
		 *
		 * @param Item   $item
		 * @param string $field
		 * @param string $value
		 * @return bool
		 */
		protected static function isEqual(Item $item, string $field, string $value) {
			if (!$item->hasAttr($field)) {
				return FALSE;
			}
			
			switch ($item->getAttrType($field)) {
				case "integer":
					return ((string)$item->getAttrValue($field) === (string)$value);
				
				case "boolean":
					if ((strtolower($value) === "true") && ($item->getAttrValue($field) === TRUE)) {
						return TRUE;
					}
					
					if ((strtolower($value) === "false") && ($item->getAttrValue($field) === FALSE)) {
						return TRUE;
					}

					return FALSE;

				case "array":
					$arr = $item->getAttrValue($field);

					if (is_array($arr)) {
						foreach ($arr as $el) {
							if ($el === $value) {
								return TRUE;
							}
						}
					}
					
					return FALSE;
				
				default:  // string-like fields, like UUID, email, URL, timestamp, string
					return ($item->getAttrValue($field) === $value);
			}
		}
		
	}