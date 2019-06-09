<?php
	class NotEnoughArgumentsException extends Exception {
		function __construct($message = NULL, $code = 0, Exception $previous = null) {
			$message = $message ?? "error.not_enough_arguments";
			parent::__construct($message, $code, $previous);
		}
	}