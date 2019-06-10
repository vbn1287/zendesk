<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;
	
	final class SearchableFieldListerTest extends TestCase {
		
		public function testGetListSearchableFields() {
			$languageHandler = new LanguageHandlerStub();
			$searchableFieldLister = new SearchableFieldLister($languageHandler);
			
			$fields = [
				"foo" => ["foo_field_1", "foo_field_2"],
				"bar" => ["bar_field_1", "bar_field_2", "bar_field_3"],
			];
			
			$list = $searchableFieldLister->list($fields);
			
			$this->assertRegexp("/\nfoo_field_2.*\nbar_field_3/s", $list);
		}
		
		
	}
	
	
	
		

