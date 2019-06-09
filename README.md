Zendesk Search CLI Utility

Usage:
	Showing manual:
		php zendesk_search 
		or
		php zendesk_search --help
	
	Searching items:
		php zendesk_search search <item-type-to-search> <field-name-to-search> <field-value-to-search>
		or
		php zendesk_search search
		
		The latter one asks for the arguments in an interactive way.
		
		Arguments:
			<item-type-to-search>: 
				possible values: "1", "users", "2", "tickets", "3", "organizations"
				each of the above number is equivalent to the subsequent type name
			<field-name-to-search>: a field name that is an attribute of the selected item type.  
				possible values for 
					"users":
						"_id"
						"url"
						"external_id"
						"name"
						"alias"
						"created_at"
						"active" <bool>
						"verified" <bool>
						"shared" <bool>
						"locale"
						"timezone"
						"last_login_at"
						"email"
						"phone"
						"signature"
						"organization_id"
						"tags" <strings>
						"suspended" <bool>
						"role"
					"tickets":	
						"_id" 
						"url" 
						"external_id" 
						"created_at" 
						"type" 
						"subject" 
						"description" 
						"priority" 
						"status" 
						"submitter_id" 
						"assignee_id" 
						"organization_id" 
						"tags" <strings>
						"has_incidents" <bool> 
						"due_at" 
						"via" 
					"organizations":
						"_id" 
						"url" 
						"external_id" 
						"name" 
						"domain_names" <strings> 
						"created_at" 
						"details" 
						"shared_tickets" <bool>
						"tags" <strings>
										
			<field-value-to-search>
				For the fields denoted by <bool>, the "true" and "false" values can be used.
				For the fields denoted by <strings>, a pipe-separated list of strings can be given which means records that have at least one value from the list.
				For the rest, any string can be given.	   

		Example:
			searching for all the users called "John Doe"
				php zendesk_search search users name "John Doe"
			searching for all the tickets having "Ohio" or "Alaska" or both (and potentially others) in their tags:
			 	php zendesk_search search tickets tags "Ohio|Alaska"
			