<?php
	
	/**
	 * This class is responsible to compile the list of the items into a textual representation.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.11.
	 * Time: 1:47
	 */
	class ItemLister {
		protected $languageHandler;
		
		function __construct($languageHandler) {
			$this->languageHandler = $languageHandler;
		}
		
		/**
		 * Returns the formatted list of the items.
		 *
		 * @param $items
		 * @param $type
		 * @param $field
		 * @param $value
		 * @return string
		 */
		public function itemsToString($items, $type, $field, $value):string {
			if (count($items) === 0) {
				$msg = sprintf($this->languageHandler->get("no_hits"), SearchEngine::stringifyType($type), $field, $value);
				return $msg;
			}
			
			$str = "";
			
			$separator = str_repeat("-", 50). PHP_EOL;
			
			foreach ($items as $item) {
				if ($str !== "") {
					$str .= $separator;
				}
				
				/** @var Item $item */
				$str .= $item->toString();
			}
			
			return $str;
		}
		
	}