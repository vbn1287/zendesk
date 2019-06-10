<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 10:54
	 */
	abstract class Item {
		protected static $attributes = [];
		
		static function getPluralName() {
			$class = strtolower(get_called_class());
			return $class. "s";  // this may need improvement if new type names are introduced in the future
		}
		
		static function getFieldNames() {
			$class = get_called_class();
			return array_keys($class::$attributes);
		}
	}