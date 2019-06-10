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
		protected $dataHandler = NULL;
		
		function __construct() {
			$this->dataHandler = new DataHandler();
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
				$this->data = $this->dataHandler->readData();
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
				
				$item->addRelatedItems($relationName, $relatedItems);
			}
			
			return $item;
		}
		
		protected function searchUsers($field, $value) {
			$type  = "users";
			$items = $this->searchItem($type, $field, $value);

			$relations = User::getRelations();
			$items = $this->hydrateRelations($items, $relations);
			return $items;
		}
		
		protected function searchTickets($field, $value) {
			$type  = "tickets";
			$items = $this->searchItem($type, $field, $value);
			
			$relations = Ticket::getRelations();
			$items = $this->hydrateRelations($items, $relations);
			return $items;
		}
		
		protected function searchOrganization($field, $value) {
			$type  = "organizations";
			$items = $this->searchItem($type, $field, $value);
			
			$relations = Organization::getRelations();
			$items = $this->hydrateRelations($items, $relations);
			return $items;
		}
		
		/**
		 * Iterates over the data set and collects the items that have their searched value.
		 *
		 * @param $type
		 * @param $field
		 * @param $value
		 * @return array
		 * @internal param $relations
		 */
		protected function searchItem($type, $field, $value) {
			$ret = [];
			
			foreach ($this->data[$type] as $item) {
				if (self::isEqual($item, $field, $value)) {
					$ret[] = $item;
				}
			}
			
			return $ret;
		}
		
		/**
		 * Adds the relations to each of the item in the list.
		 *
		 * @param array $items
		 * @param array $relations
		 * @return array
		 */
		protected function hydrateRelations(array $items, array $relations):array {
			foreach ($items as $item) {
				$item = $this->addRelations($item, $relations);
				$ret[] = $item;
			}
			
			return $items;
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