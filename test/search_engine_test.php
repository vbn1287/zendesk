<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;
	
	class ZendeskSearcherProxy extends ZendeskSearcher {
		public function help() {
			parent::help();
		}
		
		public function getListSearchableFields() {
			return parent::getListSearchableFields();
		}
	}
	
	class SequenceFeeder {
		protected $array = [];
		protected $cursor = 0;
		
		function __construct($array) {
			$this->array = $array;
		}
		
		function getNext() {
			$ret = $this->array[$this->cursor];
			$this->cursor++;
			return $ret;
		}
	}
	
	final class ZendeskSearcherTest extends TestCase {
		/*
		public function testHelp(): void {
			$paramFeeder = new CliFeeder;
			$zs = new ZendeskSearcherProxy($paramFeeder);
			
			$zs->help();
			
			$this->expectOutputRegex("/Zendesk/");
		}
		
		public function testGetListSearchableFields() {
			$paramFeeder = new CliFeeder;
			$zs = new ZendeskSearcherProxy($paramFeeder);
			
			$list = $zs->getListSearchableFields();
			$this->assertRegexp("/\n_id.*\nexternal_id/s", $list);
		}
		
		public function testRunHelp() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php", "--help"];
			});
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			
			$zs->run();
			$this->expectOutputRegex("/Zendesk/");
		}
		
		public function testRunSearchNotEnoughArguments() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php", "users"];
			});
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			try {
				$zs->run();
			} catch (\Throwable $t) {
				$this->assertEquals($t->getMessage(), "error.not_enough_arguments");
				return;
			}
			
			// if the above catch did not happen then the expected throw did not happen.
			$this->assertEquals(1, 2);
		}
		
		public function testRunSearchFromCommandLine() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php", "users", "_id", 6];
			});
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->run();
			$this->expectOutputRegex("/6/");
		}
		
		public function testRunQuit() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->assertEquals(1, 1); // we just assert that the code has returned.
		}
		
		public function testRunInteractiveSearch() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["1", "2", "_id", "436bf9b0-1147-4c0a-8439-6f79833bff5b", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->expectOutputRegex("/ \[_id\] => 436bf9b0-1147-4c0a-8439-6f79833bff5b/");
		}
		
		public function testRunInteractiveListOfFields() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["2", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->expectOutputRegex("/\n_id.*\nexternal_id/s");
		}
		*/
		
		
		public function testRunInteractiveOrganizations() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["1", "organizations", "_id", "102", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$output = $this->getActualOutput();
			
			$this->assertRegexp("/_id.*external_id/s", $output);
		}
	}
	
		

