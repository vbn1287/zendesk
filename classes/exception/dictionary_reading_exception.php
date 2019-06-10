<?php
	
	/**
	 * Thrown when the dictionary file cannot be read or processed.
	 *
	 * Class DictionaryReadingException
	 */
	class DictionaryReadingException extends Exception {
		function __construct($message = NULL, $code = 0, Exception $previous = null) {
			$message = $message ?? "error.dictionary_reading_error";
			parent::__construct($message, $code, $previous);
		}
	}