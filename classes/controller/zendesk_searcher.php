<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 11:52
	 */
	class ZendeskSearcher {
		protected $feeder = NULL;
		
		const SEPARATOR_LENGTH = 46;
		
		function __construct(ParamFeeder $paramFeeder) {
			$this->feeder = $paramFeeder;
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
				print "Welcome...";
				while (TRUE) {
					$prompt = "Type 'quit' ". PHP_EOL. "Select...". PHP_EOL;
					print $prompt;
					$line = readline();
					switch ($line) {
						case "quit":
							return;

						case "2":
							print $this->listSearchableFields();
							break;
					}
				}
			}
		}
		
		protected function getSearchableFields() {
			return [
				"users"         => ["_id", "url", "external_id", "name", "alias"],
				"tickets"       => ["_id", "url", "external_id", "created_at"],
				"organizations" => ["_id", "url", "external_id", "details"],
			];
		}
		protected function listSearchableFields() {
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