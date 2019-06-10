<?php
	
	/**
	 * Created by PhpStorm.
	 * User: halmai
	 * Date: 2019.06.10.
	 * Time: 11:16
	 */
	class Ticket extends Item {
		protected static $attributes = [
			"_id"             => "uuid",
			"url"             => "url",
			"external_id"     => "uuid",
			"created_at"      => "timestamp",
			"type"            => "string",
			"subject"         => "string",
			"description"     => "string",
			"priority"        => "string",
			"status"          => "string",
			"submitter_id"    => "integer",
			"assignee_id"     => "integer",
			"organization_id" => "integer",
			"tags"            => "array",
			"has_incidents"   => "boolean",
			"due_at"          => "timestamp",
			"via"             => "string",
		];
		
	}