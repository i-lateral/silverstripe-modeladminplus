# SilverStripe ModelAdminPlus

Expanded version of ModelAdmin that adds some extra features:

* Submitting `SearchForm` via a POST (meaning that complex fields that require an Ajax callback should now function).
* Saving search results to a session rather than using the query string.
* Adding bulk editing (using `colymba/gridfield-bulk-editing-tools` by default). 

## Installing

Install this module via composer:

```
composer require i-lateral/silverstripe-modeladminplus
```

## Usage

To use this module, simply have you custom `ModelAdmin` extend `ModelAdminPlus` (instead of `ModelAdmin`). For example:

```php
use ilateral\SilverStripe\ModelAdminPlus\ModelAdminPlus;

class MyModelAdmin extends ModelAdminPlus
{

  // Add standard ModelAdmin code here

}
```
