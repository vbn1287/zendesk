<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 17:35
	 */
	class CliFeeder extends ParamFeeder {
		protected $items = NULL;
		protected $cursor = 0;
		
		function getNext():string {
			if ($this->items === NULL) {
				$this->init();
			}
			
			if ($this->cursor >= count($this->items)) {
				return "";
			}
			
			$this->cursor++;

			return $this->items[$this->cursor - 1];
		}
		
		protected function init() {
			global $argv;

			$this->items  = $argv;
			$this->cursor = 1;
		}
	}