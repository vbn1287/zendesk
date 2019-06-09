<?php
	
	/**
	 * This is the abstraction of the feeder which is needed to make it possible in the future
	 * to read commands not from the standard input but from a web service or any other source.
	 *
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.09.
	 * Time: 17:32
	 */
	abstract class ParamFeeder {
		/*
		 * @returns string as the next item from the parameter list, or "" if there is no more item.
		 */
		abstract function getNext(): string;
	}