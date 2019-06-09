<?php
	include_once(__DIR__. "/../classes/autoload_handler.php");
	spl_autoload_register(["AutoloadHandler", "autoloader"]);
