<?php
	
	/**
	 * Thrown when the JSON data files cannot be read or processed.
	 *
	 * Class DictionaryReadingException
	 */
	class DataFileLoadErrorException extends Exception {
		function __construct($message = NULL, $code = 0, Exception $previous = null) {
			$message = $message ?? "error.data_file_load_error";
			parent::__construct($message, $code, $previous);
		}
	}