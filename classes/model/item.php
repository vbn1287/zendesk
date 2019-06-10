<?php
	
	/**
	 * Abstract class for containing the common parts for the User, Ticket and Organization classes
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 10:54
	 */
	abstract class Item {
		protected static $attributes = [];
		protected static $relations = [];
		protected $values = [];
		protected $relatedItems = [];
		
		/**
		 * Returns the plural name (eg. "users") from the class (eg. "User").
		 *
		 * @return string
		 */
		static function getPluralName():string {
			$class = strtolower(get_called_class());
			return $class. "s";  // this may need improvement if new type names are introduced in the future
		}
		
		/**
		 * Returns the list of the existing field names of the Item.
		 *
		 * @return array
		 */
		static function getFieldNames():array {
			$class = get_called_class();
			return array_keys($class::$attributes);
		}
		
		/**
		 * Returns the list of the relations of the Item.
		 *
		 * @return array
		 */
		static function getRelations():array {
			$class = get_called_class();
			return $class::$relations;
		}
		
		/**
		 * Returns teh value of the given field.
		 *
		 * @param $name
		 * @return mixed
		 */
		function getAttrValue($name) {
			return $this->values[$name];
		}
		
		/**
		 * Returns whether the Item has an attribute with the given name or not.
		 *
		 * @param $name
		 * @return bool
		 */
		function hasAttr($name):bool {
			$class = get_called_class();
			$arr = $class::$attributes;
			return array_key_exists($name, $arr);
		}
		
		/**
		 * Returns the type ("boolean", "string", ...) of he given attribute.
		 *
		 * @param $name
		 * @return mixed
		 */
		function getAttrType($name):string {
			$class = get_called_class();
			$arr = $class::$attributes;
			return $arr[$name];
		}
		
		/**
		 * Initializes the attributes of an Item.
		 *
		 * @param $arr
		 * @return $this
		 */
		function fillFromArray($arr) {
			$this->values = [];
			
			$class = get_called_class();

			foreach ($class::$attributes as $key => $type) {
				if (array_key_exists($key, $arr)) {
					$this->values[$key] = $arr[$key];
				} else {
					$this->values[$key] = NULL;
				}
			}
			
			return $this;
		}
		
		/**
		 * Returns the string representation of an Item, together with its relations.
		 *
		 * @return string
		 */
		function toString() {
			$str = "";
			
			$class = get_called_class();
			
			$maxLength = 0;
			
			foreach ($class::$attributes as $key => $type) {
				if (strlen($key) > $maxLength) {
					$maxLength = strlen($key);
				}
			}
			
			foreach ($this->relatedItems as $key => $type) {
				if (strlen($key) > $maxLength) {
					$maxLength = strlen($key);
				}
			}

			foreach ($class::$attributes as $key => $type) {
				$padding = str_repeat(" ", $maxLength - strlen($key) + 2);

				$value = self::getAttrValue($key);
				
				switch (self::getAttrType($key)) {
					case "array":
						$value = array_map(function($el) {
							return "\"". $el. "\"";
						}, $value);
						$value = "[". join(", ", $value). "]";
						break;
					
					case "boolean":
						$value = ($value === TRUE) ? "true" : (($value === FALSE) ? "false" : "");
						break;
					
					case "string":
					case "email":
					case "url":
					case "uuid":
					case "timestamp":
						$value = ($value === NULL) ? "NULL" : "\"". $value. "\"";
						break;
					
					case "integer":
						$value = ($value === NULL) ? "NULL" : $value;
						break;
						
				}
				
				$str .= $key. $padding. $value. PHP_EOL;
			}
			
			foreach ($this->relatedItems as $key => $item) {
				$gap = 2;
				$padding = str_repeat(" ", $maxLength - strlen($key) + $gap);
				$str .= $key. $padding. $this->relatedItemToString($item, str_repeat(" ", $maxLength + $gap)). PHP_EOL;
			}
			
			return $str;
		}
		
		/**
		 * Returns the string representation of the $item, as relation.
		 * It is used for indented, short representation of an item, for example,
		 * when we build the users for an organization.
		 *
		 * @param $item
		 * @param $padding
		 * @return string
		 */
		protected function relatedItemToString($item, $padding):string {
			if (is_array($item)) {
				$items = array_map(function(Item $el){
					return $el->toStringAsRelated();
				}, $item);
				
				$indent = "    ";
				
				$ret = "[". PHP_EOL. $padding. $indent. join(",". PHP_EOL. $padding. $indent, $items). PHP_EOL. $padding. "]";
				
				return $ret;
			} elseif ($item === NULL) {
				return "null";
			} else {
				/** @var Item $item */
				return $item->toStringAsRelated();
			}
		}
		
		abstract protected function toStringAsRelated();
	}