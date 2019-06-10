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
		protected $searchEngine = NULL;
		
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
							
							print $this->itemsToString($items, $type, $field, $value);
							
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
				
				print $this->itemsToString($items, $type, $field, $value);
				
			}
		}
		
		protected function search($type, $field, $value) {
			if ($this->searchEngine === NULL) {
				$this->searchEngine = new SearchEngine();
			}
			
			return $this->searchEngine->search($type, $field, $value);
		}
		
		protected function getSearchableFields() {
			$types = ["User", "Ticket", "Organization"];
			
			$ret = [];
			
			foreach ($types as $type) {
				$ret[$type::getPluralName()] = $type::getFieldNames();
			}
			
			return $ret;
		}

		protected function getListSearchableFields() {
			$fields = $this->getSearchableFields();
			
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
		
		protected function itemsToString($items, $type, $field, $value) {
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