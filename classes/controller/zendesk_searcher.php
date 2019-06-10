<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 11:52
	 */
	class ZendeskSearcher {
		protected $feeder = NULL;
		protected $reader = "readline";
		
		const SEPARATOR_LENGTH = 46;
		
		function __construct(ParamFeeder $paramFeeder) {
			$this->feeder = $paramFeeder;
			$this->languageHandler = new LanguageHandler("en");
		}
		
		function setReader(callable $reader) {
			$this->reader = $reader;
		}
		
		static function getHelp(): string {
			@$help = file_get_contents("docs/help.txt");
			
			if ($help === FALSE) {
				throw new HelpNotFoundException;
			}
			
			return $help;
		}
		
		protected function help() {
			print $this->getHelp();
		}
		
		public function run() {
			$el = $this->feeder->getNext();
			
			if (($el === "--help") || ($el === "-h")) {
				$this->help();
			} elseif ($el === "") {
				
				print $this->languageHandler->get("welcome");
				
				while (TRUE) {
					print $this->languageHandler->get("type_quit");
					$line = ($this->reader)();
					
					switch ($line) {
						case "quit":
							return;
						
						case "1":
							print $this->languageHandler->get("select_type");
							$type = ($this->reader)();
							
							print $this->languageHandler->get("select_search_term");
							$field = ($this->reader)();
							
							print $this->languageHandler->get("select_search_value");
							$value = ($this->reader)();
							
							$items = $this->search($type, $field, $value);
							
							print_r($items);
							
							break;
						
						case "2":
							print $this->getListSearchableFields();
							break;
					}
				}
			} else {
				$type  = $el;
				$field = $this->feeder->getNext();
				$value = $this->feeder->getNext();
				
				if ($value === "") {
					throw new NotEnoughArgumentsException();
				}
				
				$items = $this->search($type, $field, $value);
				
				print_r($items);
				
			}
		}
		
		protected function search($type, $field, $value) {
			$item = [
				"_id" => $value,
			];
			$ret = [$item];
			return $ret;
		}
		
		protected function getSearchableFields() {
			return [
				"users"         => ["_id", "url", "external_id", "name", "alias"],
				"tickets"       => ["_id", "url", "external_id", "created_at"],
				"organizations" => ["_id", "url", "external_id", "details"],
			];
		}

		protected function getListSearchableFields() {
			$fields = $this->getSearchableFields();
			
			$typeDescriptions = [];

			foreach ($fields as $type => $fieldList) {
				$head = "Search ". $type. "s with". PHP_EOL;
				$body = join(PHP_EOL, $fieldList). PHP_EOL. PHP_EOL;
				$typeDescriptions[] = $head. $body;
			}
			
			$separator = str_repeat("-", self::SEPARATOR_LENGTH). PHP_EOL;

			$ret = join($separator, $typeDescriptions);

			return $ret;
		}
	}