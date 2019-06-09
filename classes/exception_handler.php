<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 17:26
	 */
	class ExceptionHandler {
		public static function exceptionHandlerFunction(Throwable $exception) {
			print "Fatal error: ". $exception->getMessage();
		}
	}