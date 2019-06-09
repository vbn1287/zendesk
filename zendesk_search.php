<?php
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 10:43
	 */
	include_once("classes/autoload_handler.php");
	
	spl_autoload_register(["AutoloadHandler", "autoloader"]);
	set_exception_handler(["ExceptionHandler", "exceptionHandlerFunction"]);
	
	$feeder = new CliFeeder;
	$zendeskSearcher = new ZendeskSearcher($feeder);
	$zendeskSearcher->run();
		