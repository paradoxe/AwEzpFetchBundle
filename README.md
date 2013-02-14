# AwEzpFetchBundle


## Presentation

AwEzpFetchBundle brings a content query language for eZ Publish 5.

## Usage examples

### Example 1
```php
<?php

// In controller get the fetch service
$fetcher = $this->get('aw_ezp_fetch');

$query = "{filter: {parent_location_id {EQ 2}}, limit: 20, sort: {date_modified DESC}}";

$result = $fetcher->fetch($query);

```

### Example 2

```php
<?php

// In controller get the fetch service
$fetcher = $this->get('aw_ezp_fetch');

$query = "{filter: {AND [subtree: {EQ '/1/2'}, visibility: {EQ true}]}, limit: 20}";

$result = $fetcher->fetch($query);

```

### Example 3
```php
<?php
// In controller get the fetch service
$fetcher = $this->get('aw_ezp_fetch');

$query = <<<EOS
filter:
      AND:
           - parent_location_id: {IN [2, 60]}
           - date_metadata.modified: {BETWEEN [2012-12-14, 2013-01-25]}
           - visibility: {EQ  true}
           - OR:
              - field.name: {EQ News}
              - full_text: {LIKE 'Press Release*'}

sort: {field.landing_page/name ASC, date_modified DESC}
limit: 12
offset: 14

EOS;

$result = $fetcher->fetch($query);

```

### Example 4

```php
<?php

// In controller get the fetch service
$fetcher = $this->get('aw_ezp_fetch');

$query = <<<EOS
filter:
      AND:
           - parent_location_id: {IN [2, 60]}
           - datemetadata.modified: {BETWEEN [2012-12-14, 2013-01-25]}
           - visibility: {EQ  true}
           - OR:
              - field.name: {EQ News}
              - full_text: {LIKE Press Release*}

sort:
     field.landing_page/name: ASC
     date_modified: DESC

limit: 12
offset: 14

EOS;

$result = $fetcher->fetch($query);

```
### Example 5

```php
<?php

// In controller get the fetch service
$fetcher = $this->get('aw_ezp_fetch');

$query = "{filter: {AND [subtree: {EQ @subtree}, visibility: {EQ true}]}  , limit: @limit, offset: @offset}";

$preparedFetch = $fetcher->prepare($query);

$preparedFetch->bindParam('@subtree', '/1/2');
$preparedFetch->bindParam('@offset', 0);
$preparedFetch->bindParam('@limit', 20);

$result = $preparedFetch->fetch();

//you can rebind any parameter and refetch

$preparedFetch->bindParam('@offset', 20);

$result = $preparedFetch->fetch();

```

## Bundle documentation

The bundle documentation is available in `Resources/doc/index.rst`

## License
The code is released under the MIT License. You can find in `Resources/meta/LICENCE`
