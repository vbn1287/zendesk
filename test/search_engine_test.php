<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;
	
	class SearchEngineProxy extends SearchEngine {
		public function setDataDir($dir) {
			$this->dataDir = $dir;
		}
	}
	
	final class SearchEngineTest extends TestCase {
		
		public function testBadJson() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/bad_json/");
			$this->expectException(DataFileLoadErrorException::class);
			$items = $searchEngine->search("users", "_id", 1);
		}
		
		public function testNonArrayJson() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/not_array_json/");
			$this->expectException(DataFileLoadErrorException::class);
			$items = $searchEngine->search("users", "_id", 1);
		}
		
		public function testNoIdInArrayElementInJson() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/no_id_in_array/");
			$this->expectException(DataFileLoadErrorException::class);
			$items = $searchEngine->search("users", "_id", 1);
		}
		
		public function testSearchOneUser() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("users", "_id", 3);
			
			$this->assertEquals(count($items), 1);
			$this->assertEquals($items[0]->getAttrValue("_id"), 3);
		}
		
		public function testSearchMoreUsers() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("users", "active", "true");
			
			$this->assertTrue(count($items) > 1);
		}
		
		public function testSearchOneOrganization() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("organizations", "_id", 102);
			
			$this->assertEquals(count($items), 1);
			$this->assertEquals($items[0]->getAttrValue("_id"), 102);
		}
		
		public function testSearchOneTickets() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("tickets", "_id", "1a227508-9f39-427c-8f57-1b72f3fab87c");
			
			$this->assertEquals(count($items), 1);
			$this->assertEquals($items[0]->getAttrValue("_id"), "1a227508-9f39-427c-8f57-1b72f3fab87c");
		}
		
		public function testInvalidItemType() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("pink_unicorns", "age", "9999");
			
			$this->assertSame($items, []);
		}
	}
	
	
	
		

