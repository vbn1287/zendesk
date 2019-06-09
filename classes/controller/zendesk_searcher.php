<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 11:52
	 */
	class ZendeskSearcher {
		protected $feeder = NULL;
		
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
					}
				}
			}
		}
		
	}