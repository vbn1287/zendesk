<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 19:13
	 */
	
	use PHPUnit\Framework\TestCase;
	
	final class CliFeederTest extends TestCase {
		public function testGetNext(): void {
			$cf = new CliFeeder(function() {
				return ["main_executable.php", "foo", "bar"];
			});
			$el = $cf->getNext();
			$this->assertEquals($el, "foo");
			
			$el = $cf->getNext();
			$this->assertEquals($el, "bar");
		}
	}
	
	