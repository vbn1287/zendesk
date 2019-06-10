<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;
	
	final class ItemListerTest extends TestCase {
		
		public function testNoRecordFound() {
			$languageHandler = new LanguageHandler();
			$itemLister = new ItemLister($languageHandler);
			
			$items = [];
			$type  = "user";
			$field = "name";
			$value = "NoSuch Person";
			$res = $itemLister->itemsToString($items, $type, $field, $value);
			$this->assertTrue(strpos($res, "No results found") > 0);
		}
		
		public function testMoreRecordsHaveSeparator() {
			$languageHandler = new LanguageHandler();
			$itemLister = new ItemLister($languageHandler);
			
			$user = new User;
			$user->fillFromArray([]);
			$items = [$user, $user];
			
			$type  = NULL;
			$field = NULL;
			$value = NULL;
			$res = $itemLister->itemsToString($items, $type, $field, $value);
			$this->assertTrue(strpos($res, "-------") > 0);
		}
		
	}
	
	
	
		

