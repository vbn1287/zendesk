<?php
	
	/**
	 * This class is responsible for feeding values to the main program that normally come from the Command Line Interface.
	 * The purpose of this class is to make the CLI usage testable.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 17:35
	 */
	class CliFeeder extends ParamFeeder {
		protected $items = NULL;
		protected $cursor = 0;
		
		protected $argGetter = NULL; // argument getter is needed to test getting arguments from a mocked command line
		
		function __construct(callable $argGetter = NULL) {
			$this->argGetter = $argGetter;
		}
		
		/**
		 * Returns the next item from the Command Line.
		 *
		 * @return string
		 */
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

			if ($this->argGetter === NULL) {
				$this->items = $argv;
			} else {
				$this->items = ($this->argGetter)();
			}
			
			$this->cursor = 1;
		}
	}