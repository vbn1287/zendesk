# Zendesk Search CLI Utility

## Usage:
### Showing manual:

`php zendesk_search.php --help`
`php zendesk_search.php -h`

### Interactive mode:

`php zendesk_search.php`

### Searching items from command line:

`php zendesk_search.php item-type-to-search field-name-to-search field-value-to-search`

### Arguments:

- `item-type-to-search`: 
possible values: `"1"`, `"users"`, `"2"`, `"tickets"`, `"3"`, `"organizations"`
Each of the above number is equivalent to the subsequent type name.
- `field-name-to-search`: 
a field name that is an attribute of the selected item type.
The possible values are listed from the interactive mode, after selecting command 2).
- `field-value-to-search`:
a value that the content of the search field needs to match. 
For boolean values, `"true"` and `"false"` strings are used.
For arrays, a record is found if the given string occurs in the array
For the rest, any string can be given.

### Example:

- searching for all the users called "John Doe"
`php zendesk_search.php users name "John Doe"`
- searching for all the tickets having "Ohio" (and potentially other values) in their tags:
`php zendesk_search.php tickets tags Ohio`

## Requirements:

- php 7.1
- register_argc_argv must be enabled (this is the default)
- memory_limit = 512M
 - current data: 75 users => 52KB
 - projected data: 10k user => 7MB (assuming what we have is representative)
