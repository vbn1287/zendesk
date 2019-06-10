<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 22:07
	 */
	
	use PHPUnit\Framework\TestCase;
	
	class DataHandlerProxy extends DataHandler  {
		public function setDataDir($dir) {
			$this->dataDir = $dir;
		}
	}
	
	final class DataHandlerTest extends TestCase {
		
		public function testBadJson() {
			$dataHandler = new DataHandlerProxy();
			$dataHandler->setDataDir(__DIR__. "/data/bad_json/");
			$this->expectException(DataFileLoadErrorException::class);
			$dataHandler->readData();
		}
		
		public function testNonArrayJson() {
			$dataHandler = new DataHandlerProxy();
			$dataHandler->setDataDir(__DIR__. "/data/not_array_json/");
			$this->expectException(DataFileLoadErrorException::class);
			$dataHandler->readData();
		}
		
		public function testNoIdInArrayElementInJson() {
			$dataHandler = new DataHandlerProxy();
			$dataHandler->setDataDir(__DIR__. "/data/no_id_in_array/");
			$this->expectException(DataFileLoadErrorException::class);
			$dataHandler->readData();
		}
		
		public function testSearchOneUser() {
			$dataHandler = new DataHandlerProxy();
			$dataHandler->setDataDir(__DIR__. "/data/good_jsons/");
			$data = $dataHandler->readData();
			$this->assertTrue(count($data) > 1);
		}
		
	}
	
	
	
		

