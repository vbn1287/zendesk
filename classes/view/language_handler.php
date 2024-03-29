<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 0:10
	 */
	class LanguageHandler {
		protected $lang;
		protected $dataDir;
		protected $dictionary;
		
		function __construct($lang = "en") {
			$this->lang = $lang;
			$this->dataDir = __DIR__. "/../../lang/";
			$this->dictionary = $this->readDictionary();
		}
	
		protected function getDictionaryFileName($lang) {
			return $this->dataDir. $lang. ".json";
		}
		
		protected function readDictionary() {
			$fileName = $this->getDictionaryFileName($this->lang);
			
			if (!file_exists($fileName)) {
				$fileName = $this->getDictionaryFileName("en");
			}
			
			if (!file_exists($fileName)) {
				throw new DictionaryReadingException();
			}

			$content = file_get_contents($fileName);
			
			if ($content === FALSE) {
				throw new DictionaryReadingException();
			}
			
			$dictionary = json_decode($content, TRUE);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new DictionaryReadingException();
			}
			
			return $dictionary;
		}
		
		function get($key) {
			
			if (array_key_exists($key, $this->dictionary)) {
				return $this->dictionary[$key];
			}
			
			return $key;
		}
	}