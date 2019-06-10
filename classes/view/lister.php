<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.11.
	 * Time: 2:30
	 */
	abstract class Lister {
		const SEPARATOR_LENGTH = 46;
		
		protected $languageHandler;
		
		function __construct($languageHandler) {
			$this->languageHandler = $languageHandler;
		}
		
	}