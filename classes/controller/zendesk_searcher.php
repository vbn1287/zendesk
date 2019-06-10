<?php
	
	/**
	 * This class contains the main logic of the program.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 11:52
	 */
	class ZendeskSearcher {
		protected $feeder = NULL;
		protected $reader = "readline"; // the function that reads from the stdio, or its test mock.
		protected $searchEngine = NULL;
		protected $languageHandler = NULL;
		protected $itemLister = NULL;
		protected $searchableFieldLister = NULL;
		
		function __construct(ParamFeeder $paramFeeder) {
			$this->feeder = $paramFeeder;
			$this->languageHandler = new LanguageHandler("en");
		}
		
		/**
		 * Overwrites the default stdio reader for the unit tests.
		 *
		 * @param callable $reader
		 */
		function setReader(callable $reader) {
			$this->reader = $reader;
		}
		
		/**
		 * Returns the help text from the file.
		 *
		 * @return string
		 * @throws HelpNotFoundException
		 */
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
		
		/**
		 * Implements the main loop of the program, reads the operations util "quit" is entered.
		 *
		 * @throws NotEnoughArgumentsException
		 */
		public function run() {
			$el = $this->feeder->getNext();
			
			if (($el === "--help") || ($el === "-h")) {
				$this->help();
			} elseif ($el === "") {  // interactive mode
				
				print $this->languageHandler->get("welcome");
				
				while (TRUE) {
					print $this->languageHandler->get("type_quit");
					$line = ($this->reader)();
					
					switch ($line) {
						case "quit":
							return;
						
						case "1":
							do {
								print $this->languageHandler->get("select_type");
								
								$type = ($this->reader)();
							} while (!in_array($type, ["1", "2", "3"]));
							
							do {
								print $this->languageHandler->get("select_search_term");
								
								$field = ($this->reader)();
								
								if ($this->isFieldSelectable($type, $field)) {
									break;
								}
								print $this->languageHandler->get("wrong_search_term");
							} while(TRUE);
							
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
			} else { // command line mode
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
		
		/**
		 * Determines whether $fieldName can be searched for in the given $typeCode or not.
		 *
		 * @param $typeCode
		 * @param $fieldName
		 * @return bool
		 */
		protected function isFieldSelectable($typeCode, $fieldName):bool {
			$fields = $this->getSearchableFields();
			
			$typeName = array_keys($fields)[$typeCode - 1];
			
			$searchableFields = $fields[$typeName];
			
			return in_array($fieldName, $searchableFields);
		}
		
		/**
		 * Executes the search functionality with the initialized SearchEngine.
		 *
		 * @param $type
		 * @param $field
		 * @param $value
		 * @return array
		 */
		protected function search($type, $field, $value) {
			if ($this->searchEngine === NULL) {
				$this->searchEngine = new SearchEngine();
			}
			
			return $this->searchEngine->search($type, $field, $value);
		}
		
		/**
		 * Returns the array of the valid field names for each Item type.
		 *
		 * @return array
		 */
		protected function getSearchableFields():array {
			$types = ["User", "Ticket", "Organization"];
			
			$ret = [];
			
			foreach ($types as $type) {
				$ret[$type::getPluralName()] = $type::getFieldNames();
			}
			
			return $ret;
		}
		
		/**
		 * Returns the list of the possible fields for each Item type.
		 *
		 * @return string
		 */
		protected function getListSearchableFields():string {
			if ($this->searchableFieldLister === NULL) {
				$this->searchableFieldLister = new SearchableFieldLister($this->languageHandler);
			}
			
			$fields = $this->getSearchableFields();
			
			return $this->searchableFieldLister->list($fields);
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
		protected function itemsToString($items, $type, $field, $value):string {
			if ($this->itemLister === NULL) {
				$this->itemLister = new ItemLister($this->languageHandler);
			}
			
			return $this->itemLister->itemsToString($items, $type, $field, $value);
		}

	}