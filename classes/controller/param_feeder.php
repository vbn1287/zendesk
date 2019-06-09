<?php
	
	/**
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