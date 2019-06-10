<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 10:55
	 */
	class Organization extends Item {
		protected static $attributes = [
			"_id"            => "integer",
			"url"            => "url",
			"external_id"    => "uuid",
			"name"           => "string",
			"domain_names"   => "array",
			"created_at"     => "timestamp",
			"details"        => "string",
			"shared_tickets" => "boolean",
			"tags"           => "array",
		];
		
		protected static $relations = [
			"tickets" => ["hasMany", "_id", "tickets.organization_id"],
			"users"   => ["hasMany", "_id", "users.organization_id"],
		];
		
		protected function toStringAsRelated() {
			$ret = sprintf("#%s (%s)", $this->getAttrValue("_id"), $this->getAttrValue("name"));
			return $ret;
		}
	}