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
	}
	
