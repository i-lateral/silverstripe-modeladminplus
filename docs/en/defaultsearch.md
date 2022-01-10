## Default Search Filter

Model Admin Plus allows you to define a "default filter" for your managed object, which means when a `ModelAdminPlus`
instance first loads and there is no existing search data,
the list is automatically filtered by either a config variable
from the model object or by overwriting a method on the `SearchFilter`

### Default Filter Via Config

The simplest search filter can be added via a config variable
on your object class, for example:

```php
class MyDataObject extends DataObject
{
  private static $db = [
    "Name" => "Varchar",
    "URL"  => "Varchar(255)"
  ];

  // Filter by default anyone not called "Steve"
  private static $default_search_filter = [
    "Name:not" => "Steve"
  ];
}

```

### Custom SearchContext

If you want a more complex default search, then you can do this by overwriting `SearchFilter::getDefaultFilter()`.

For Example:

```php
class MySearchContext extends ilateral\SilverStripe\ModelAdminPlus\SearchContext
{
  // Only show Steve's items created in the last seven
  // days by default
  public function getDefaultFilter()
  {
    $start = new DateTime('-7 days');
    $end = new DateTime();
    return [
      'Created:GreaterThanOrEqual' => $start->format('Y-m-d H:i:s'),
      'Created:LessThanOrEqual' => $end->format('Y-m-d H:i:s')
    ];
  }
}

class MyDataObject extends DataObject
{
  private static $db = [
    "Name" => "Varchar",
    "URL"  => "Varchar(255)"
  ];

  // Provide custom search context
  public function getModelAdminSearchContext()
  {
      return MySearchContext::create(
          get_class($this),
          $this->scaffoldSearchFields(),
          $this->defaultSearchFilters()
      );
  }
}

```