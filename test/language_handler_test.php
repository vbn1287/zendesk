<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;

	class LanguageHandlerStub extends LanguageHandler {
		function setDataDir($dir) {
			$this->dataDir = $dir;
			$this->readDictionary();
		}
	}
	
	final class LanguageHandlerTest extends TestCase {
		
		public function testHappyPath() {
			$languageHandler = new LanguageHandler();
			$res = $languageHandler->get("welcome");
			$this->assertTrue($res !== "welcome"); // a real translation took effect
			$this->assertTrue(strlen($res) > 0); // it has a non-empty content
		}
		
		public function testMissingLanguage() {
			$languageHandler = new LanguageHandler("klingon");
			$res = $languageHandler->get("welcome");
			$this->assertTrue($res !== "welcome");
			$this->assertTrue(strlen($res) > 0); // it has a non-empty content
		}
		
		public function testMissingKey() {
			$languageHandler = new LanguageHandler();
			$res = $languageHandler->get("non-existing-key");
			$this->assertSame($res, "non-existing-key");
		}
		
		public function testMissingFile() {
			$languageHandler = new LanguageHandlerStub();
			$this->expectException(DictionaryReadingException::class);
			$languageHandler->setDataDir(__DIR__. "/lang/no_dictionary/");
			$res = $languageHandler->get("whatever");
		}
		
		public function testNoJsonFile() {
			$languageHandler = new LanguageHandlerStub();
			$this->expectException(DictionaryReadingException::class);
			$languageHandler->setDataDir(__DIR__. "/lang/bad_json/");
			$res = $languageHandler->get("whatever");
		}
	}
	
	
	
		

