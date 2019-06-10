<?php
	
	/**
	 * This class is responsible to compile the list of the fields that can be searched for.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.11.
	 * Time: 1:47
	 */
	class SearchableFieldLister extends Lister {
		protected $languageHandler;
		
		function __construct($languageHandler) {
			$this->languageHandler = $languageHandler;
		}
		
		/**
		 * Returns the formatted list of the fields.
		 *
		 * @param $fields
		 * @return string
		 */
		public function list($fields):string {
			$typeDescriptions = [];
			
			foreach ($fields as $type => $fieldList) {
				$head = sprintf($this->languageHandler->get("search_with"), ucfirst($type));
				$body = join(PHP_EOL, $fieldList). PHP_EOL. PHP_EOL;
				$typeDescriptions[] = $head. $body;
			}
			
			$separator = str_repeat("-", self::SEPARATOR_LENGTH). PHP_EOL;
			
			$ret = join($separator, $typeDescriptions);
			
			return $ret;
		}
		
	}