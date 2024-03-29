<?php
	
	/**
	 * Thrown when the help file cannot be read.
	 *
	 * Class DictionaryReadingException
	 */
	class HelpNotFoundException extends Exception {
		function __construct($message = NULL, $code = 0, Exception $previous = null) {
			$message = $message ?? "error.help_file_not_found";
			parent::__construct($message, $code, $previous);
		}
	}