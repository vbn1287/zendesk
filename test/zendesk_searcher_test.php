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
	
	final class ZendeskSearcherTest extends TestCase {
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
	}
	
