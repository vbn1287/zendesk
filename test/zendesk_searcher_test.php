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
		
		public function getListSearchableFields():string {
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
		public function testHelp(): void {
			$paramFeeder = new CliFeeder;
			$zs = new ZendeskSearcherProxy($paramFeeder);
			
			$zs->help();
			
			$this->expectOutputRegex("/Zendesk/");
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
			$this->expectException(NotEnoughArgumentsException::class);
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->run();
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

		public function testRunSearchFromCommandLine() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php", "users", "_id", 6];
			});
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->run();
			$this->expectOutputRegex("/6/");
		}
		
		public function testRunInteractiveSearchForTickets() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["1", "2", "_id", "436bf9b0-1147-4c0a-8439-6f79833bff5b", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->expectOutputRegex("/_id[\s]+\"436bf9b0-1147-4c0a-8439-6f79833bff5b\"/");
		}
		
		public function testRunInteractiveSearchWrongSearchTerm() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["1", "non-existing-search-option", "2", "non-existing-search-term", "_id", "436bf9b0-1147-4c0a-8439-6f79833bff5b", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->expectOutputRegex("/_id[\s]+\"436bf9b0-1147-4c0a-8439-6f79833bff5b\"/");
		}
		
		public function testRunListsRelations() {
			$paramFeeder = new CliFeeder(function() {
				return ["program.php"];
			});
			
			$sf = new SequenceFeeder(["1", "3", "_id", "101", "quit"]);
			
			$zs = new ZendeskSearcherProxy($paramFeeder);
			$zs->setReader([$sf, "getNext"]);
			
			$zs->run();
			$this->expectOutputRegex("/submitter/");
			$this->expectOutputRegex("/assignee/");
			$this->expectOutputRegex("/organization/");
		}
	}
	
		

