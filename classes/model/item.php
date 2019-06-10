<?php
	
	/**
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
		
		static function getPluralName() {
			$class = strtolower(get_called_class());
			return $class. "s";  // this may need improvement if new type names are introduced in the future
		}
		
		static function getFieldNames() {
			$class = get_called_class();
			return array_keys($class::$attributes);
		}
		
		static function getRelations() {
			$class = get_called_class();
			return $class::$relations;
		}
		
		function getAttrValue($name) {
			return $this->values[$name];
		}
		
		function hasAttr($name) {
			$class = get_called_class();
			$arr = $class::$attributes;
			return array_key_exists($name, $arr);
		}
		
		function getAttrType($name) {
			$class = get_called_class();
			$arr = $class::$attributes;
			return $arr[$name];
		}
		
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
		
		function addRelatedItems($relationName, $items) {
			$this->relatedItems[$relationName] = $items;
		}
		
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