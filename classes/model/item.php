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
			return $this->attributes[$name];
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
	}