<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 10:55
	 */
	class User extends Item {
		protected static $attributes = [
			"_id"             => "integer",
			"url"             => "url",
			"external_id"     => "uuid",
			"name"            => "string",
			"alias"           => "string",
			"created_at"      => "timestamp",
			"active"          => "boolean",
			"verified"        => "boolean",
			"shared"          => "boolean",
			"locale"          => "string",
			"timezone"        => "string",
			"last_login_at"   => "timestamp",
			"email"           => "email",
			"phone"           => "string",
			"signature"       => "string",
			"organization_id" => "integer",
			"tags"            => "array",
			"suspended"       => "boolean",
			"role"            => "string",
		];
	}