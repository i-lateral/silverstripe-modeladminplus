# SilverStripe ModelAdminPlus

Expanded version of ModelAdmin that adds some extra features:

* Submitting `SearchForm` via a POST (meaning that complex fields that require an Ajax callback should now function).
* Saving search results to a session rather than using the query string.
* Adding bulk editing (using `colymba/gridfield-bulk-editing-tools` by default). 
* When generating export fields for a `DataObject` also checks if the object has `export_fields`

## Installing

Install this module via composer:

```
composer require i-lateral/silverstripe-modeladminplus
```

## Usage

This module is designed to be "hot-swappable" with standard `ModelAdmin` extensions.
To generate your own admin interface using it, simply have you custom `ModelAdmin`
extend `ModelAdminPlus` (instead of `ModelAdmin`). For example:

```php
use ilateral\SilverStripe\ModelAdminPlus\ModelAdminPlus;

class MyModelAdmin extends ModelAdminPlus
{

  // Add standard ModelAdmin code here

}
```

## Custom Export Fields

`ModelAdminPlus` allows for a `DataObject` to define it's own `export_fields` when it
performs a bulk export. Export fields are a config variable, and are defined in exactly
the same way as `summary_fields`, EG:

```php
class MyDataObject extends DataObject
{
  private static $db = [
    "Name" => "Varchar",
    "URL"  => "Varchar(255)"
  ];

  /**
   * Fields shown in GridField
   *
   * @return array
   */
  private static $summary_fields = [
    "ID" => "ID",
    "Name" => "Your Name"
  ];

  /**
   * Custom fields to export to CSV in
   * ModelAdminPlus
   *
   * @return array
   */
  private static $export_fields = [
    "Name" => "Your Name",
    "URL"  => "Website"
  ];
}
```

You can also overwrite the export_fields using the `updateExportFields` extension hook.