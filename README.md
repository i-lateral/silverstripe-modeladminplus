# SilverStripe ModelAdminPlus

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/i-lateral/silverstripe-modeladminplus/badges/quality-score.png?b=1)](https://scrutinizer-ci.com/g/i-lateral/silverstripe-modeladminplus/?branch=1)
[![Build Status](https://travis-ci.org/i-lateral/silverstripe-modeladminplus.svg?branch=1)](https://travis-ci.org/i-lateral/silverstripe-modeladminplus)

Expanded version of ModelAdmin that adds some extra features:

* Submitting `SearchForm` via a POST (meaning that complex fields that require an Ajax callback should now function).
* Saving search results to a session rather than using the query string.
* Adding bulk editing (using `colymba/gridfield-bulk-editing-tools` by default). 
* When generating export fields for a `DataObject` also checks if the object has `export_fields`
* Automatically find and format `DBDate`/`DBDatetime` fields to `DBDate.Nice`

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

## Disable automatic date/datetime conversion

If you do not want `ModelAdminPlus` to automatically convert `DBDate`/`DBDateTime` fields
to a formatted column (default to `DBDate.Nice`), you can disable this via SilverStripe
config, using:

  `ilateral\SilverStripe\ModelAdminPlus.auto_convert_dates`

## Changing default date format

If you want to change how ModelAdminPlus formats dates (without effecting the Nice method),
you can do this via the helper class `GridFieldDateFinder`. You can configure this either
globally, or per instance called.

### Changing date format globally

You can change all instances of date re-format by setting the following SilverStripe config
variable:

  `ilateral\SilverStripe\ModelAdminPlus.default_date_type`

### Changing date format per instance

You can change specific instances of the date format every time you call the GridFieldDateFinder
class, by using the following method:

  `ilateral\SilverStripe\ModelAdminPlus::setDateType("newDateType")`