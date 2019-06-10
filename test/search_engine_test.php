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
		
		public static function isEqual(Item $item, string $field, string $value) {
			return parent::isEqual($item, $field, $value);
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
		
		public function testInvalidItemTypeReturnsEmptyArray() {
			$searchEngine = new SearchEngineProxy();
			$searchEngine->setDataDir(__DIR__. "/data/good_jsons/");
			$items = $searchEngine->search("pink_unicorns", "age", "9999");
			
			$this->assertSame($items, []);
		}

		public function testIsEqualIdAttribute() {
			$item = new User;
			$item->fillFromArray([
				"_id"       => 1,
				"suspended" => FALSE,
				"shared"    => TRUE,
			]);
			$ret = SearchEngineProxy::isEqual($item, "_id", 1);
			$this->assertSame($ret, TRUE);
		}

		public function testIsEqualWrongAttribute() {
			$item = new User;
			
			$ret = SearchEngineProxy::isEqual($item, "no-such-field", 1);
			$this->assertSame($ret, FALSE);
		}
		
		public function testIsEqualBooleanAttribute() {
			$item = new User;
			$item->fillFromArray([
				"_id"       => 1,
				"suspended" => FALSE,
				"shared"    => TRUE,
			]);
			
			$ret = SearchEngineProxy::isEqual($item, "suspended", "false");
			$this->assertSame($ret, TRUE);
			
			$ret = SearchEngineProxy::isEqual($item, "suspended", "true");
			$this->assertSame($ret, FALSE);
			
			$ret = SearchEngineProxy::isEqual($item, "shared", "true");
			$this->assertSame($ret, TRUE);
			
			$ret = SearchEngineProxy::isEqual($item, "shared", "false");
			$this->assertSame($ret, FALSE);
		}
		
		public function testIsEqualArrayAttribute() {
			$item = new User;
			$item->fillFromArray([
				"tags"      => ["alpha", "beta"],
			]);
			
			$ret = SearchEngineProxy::isEqual($item, "tags", "alpha");
			$this->assertSame($ret, TRUE);
			
			$ret = SearchEngineProxy::isEqual($item, "tags", "beta");
			$this->assertSame($ret, TRUE);
			
			$ret = SearchEngineProxy::isEqual($item, "tags", "gamma");
			$this->assertSame($ret, FALSE);
			
		}
	}
	
	
	
		

