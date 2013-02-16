AwEzpFetchBundle
================

This bundle brings a content query language.


Introduction
------------

This bundle is a facade for the search service. It simplifies content querying.

.. code-block:: php

    // example
    $result = $container->get('aw_ezp_fetch')->fetch("{filter: {parent_location_id {EQ 2}}, limit: 20, sort: {date_modified DESC}}");


CQL (Content Query Langage)
---------------------------

The fetch queries could be either a CQL (Content Query Langage) string or  in php array format.

Why Yaml?
~~~~~~~~~

- Readability: Inline and expanded (multiline) representations
- The great Symfony2 Yaml Component (with literal Boolean and Date detection and parse)
- Query could be stored in yml configuration file and directly used in the fetcher.

Check http://symfony.com/doc/current/components/yaml/yaml_format.html

Query Structure
~~~~~~~~~~~~~~~

If you are familiar with EBNF, check the EBNF definition.

Terminology:

- Map: associative array
- Sequence: indexed array


A Query is a Map structure composed by:

- filter (required)
- sort (optionnal)
- limit (optionnal)
- offset (optionnal)

To understand how to write the query in CQL format, here is the equivalent usage with php arrays

.. code-block:: php

   $query = array();
   $query['filter'] = $condition; #Required
   $query['sort'] = $sort; #Optionnal
   $query['limit'] = number; #Optionnal
   $query['offset'] = number; #Optionnal

   $condition = $criterion | $logical_term;

   $criterion = array($criterion_identifier => array($operator => $operand));
   $operator = 'EQ' | 'NE' | 'GT' | 'GTE' | 'LT' | 'LTE' | 'LIKE' | 'UNLIKE' | 'IN' | 'NIN' | 'BETWEEN' | 'OUTSIDE'; # Case sensitive

   $logical_term = array($logical_factor => array($criterion, $criterion1, $criterion2));
   $logical_factor = 'AND' | 'OR' | 'NAND' | 'NOR'; # Case sensitive

   $sort = array($sort_clause_identifier => $sort_direction, $sort_clause_identifier2 => $sort_direction, $sort_clause_identifier3 => $sort_direction);
   $sort_direction = 'ASC' | 'DESC'; # Case sensitive

CRITERION IDENTIFIERS
---------------------

Identifier are the lower-cased Criterion class names using underscores : hence for contentId we use content_id as identifier

- parent_location_id
- subtree
- content_type
- language_code
- status
- visibility
- full_text
- content_id
- location_remote_id
- remote_id
- object_state_id
- url_alias

Special case of Field, UserMetadata and DateMetadata, we append the target to the identifier using a dot (.) as separator:

- field.<target> : possible target values are field identifiers. Example "field.title"
- user_metadata.<target> : possible target values (owner | creator| modifier | group). Example "user_metadata.creator"
- date_metadata.<target> : possible target values (modified | created). Example : "date_metadata.created"


SORT CLAUSES IDENTIFIERS
------------------------

Identifier are the lower-cased SortClause class names using underscores : hence for contentId we use content_id as identifier

- content_id
- content_name
- date_modified
- date_published
- location_depth
- location_path
- location_path_string
- location_priority
- section_identifier
- section_name

Special case of Field. We append the target to the identifier using a dot as separator:

- field.<target> : target must be in this format : ContentTypeIdentifier/FieldIdentifier. Example "field.article/title"

MATCH OPERATORS:
----------------

+----------+--------------+----------------------------------------------+
| Operator | Operand Type | Comments                                     |
+==========+==============+==============================================+
| EQ       | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| NE       | scalar       | Treated as NOT EQ                            |
+----------+--------------+----------------------------------------------+
| GT       | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| GTE      | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| LT       | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| LTE      | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| LIKE     | scalar       |                                              |
+----------+--------------+----------------------------------------------+
| UNLIKE   | scalar       | Treated as NOT LIKE                          |
+----------+--------------+----------------------------------------------+
| IN       | sequence     | Sequence should contain at least one element |
+----------+--------------+----------------------------------------------+
| NIN      | sequence     | Treated as NOT IN                            |
+----------+--------------+----------------------------------------------+
| BETWEEN  | sequence     | Sequence with exactly two scalars elements   |
|          |              | representing (left, right) ragne bounds      |
+----------+--------------+----------------------------------------------+
| OUTSIDE  | sequence     | Treated as NOT BETWEEN                       |
+----------+--------------+----------------------------------------------+

LOGICAL FACTORS:
----------------

- AND
- OR
- NAND (Treated as NOT AND)
- NOR (Treated as NOT OR)


Fetch CQL (Content Query Language) EBNF Definition
--------------------------------------------------

.. code-block:: ebnf


    query              ::= filter
                       |   '{' filter  (',' sort)? (',' limit)? '}'
                       |   filter
                           (new_line sort)?
                           (new_line offset)?
                           (new_line limit)?

    filter             ::= 'filter' delim  condition

    condition          ::= criterion | logical_term

    criterion          ::= 'criterion_identifier' delim '{' match '}'

    logical_term       ::= logical_factor delim  criteria

    criteria           ::= '[' '{' condition '}'  ( ',' '{' condition '}' )* ']'
                       |    (new_line indent '-' condition)+

    match              ::= (match_compare | match_range | match_enum)

    match_compare      ::= compare_operator delim scalar

    match_enum         ::= enum_operator delim array

    match_range        ::= range_operator delim '[' scalar ',' scalar ']'

    compare_operator   ::= ('EQ' | 'NE' | 'GT' | 'GTE' | 'LT' | 'LTE' | 'LIKE' | 'UNLIKE')

    range_operator     ::= 'BETWEEN' | 'OUTSIDE'

    enum_operator      ::= 'IN' | 'NIN'

    logical_factor     ::= 'AND' | 'OR' | 'NAND' | 'NOR'

    limit              ::= 'limit' delim number

    offset             ::= 'offset' delim number

    sort               ::= 'sort' delim '{' sort_clause  (',' sort_clause)* '}'
                       |   'sort' delim
                           (new_line indent sort_clause)+

    sort_clause        ::= 'sort_clause_identifier' delim  sort_direction

    sort_direction     ::= 'ASC' | 'DESC'

    array              ::= '[' scalar (',' scalar)* ']'

    scalar             ::= 'number' | 'boolean literal' | 'string' | 'date ISO-8601'

    delim              ::= ':' indent

    indent             ::= (tab | space)+

    tab                ::= '\t'

    space              ::= ' '

    new_line           ::= '\n'



Usage samples
-------------

Example 1 compact CQL Query
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // In controller get the fetch service
    $fetcher = $this->get('aw_ezp_fetch');

    $query = "{filter: {parent_location_id {EQ 2}}, limit: 20, sort: {date_modified DESC}}";

    $result = $fetcher->fetch($query);

Example 1 bis equivalent Query in PHP format
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

   // In controller get the fetch service
   $fetcher = $this->get('aw_ezp_fetch');

   $query = array('filter' => array('parent_location_id' => array('EQ' => 2)),
                  'limit' => 20,
                  'sort' => array('date_modified' => 'DESC'));

   $result = $fetcher->fetch($query);


Example 2 compact CQL Query
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // In controller get the fetch service
     $fetcher = $this->get('aw_ezp_fetch');

     $query = "{filter: {AND: [subtree: {EQ '/1/2/60'}, visibility: {EQ true}]}, limit: 20}";

     $result = $fetcher->fetch($query);


Example 2 bis equivalent Query in PHP format
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // In controller get the fetch service
     $fetcher = $this->get('aw_ezp_fetch');

     $query = array('filter' => array('AND' => array(
                                                    array('subtree' => array('EQ' => '/1/2/60')),
                                                    array('visibility' => array('EQ' => true))
                                                    )
                                              ),
                     'limit' => 20);

     $result = $fetcher->fetch($query);


Example 3 expanded CQL Query
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

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
    limit:  5
    offset: 5

    EOS;

    $result = $fetcher->fetch($query);


Example 3 bis expanded CQL (expanded sort)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

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
                  - full_text:  {LIKE Press Release*}

    sort:
         field.landing_page/name: ASC
         date_modified: DESC

    limit:  5
    offset: 5

    EOS;

    $result = $fetcher->fetch($query);


Example 3 bis equivalent Query in PHP format
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    // In controller get the fetch service
    $fetcher = $this->get('aw_ezp_fetch');

    $query = array(
                 'filter' => array(
                                  'AND' => array(
                                           array('parent_location_id' => array('IN' => array(65, 60))),
                                           array('date_metadata.modified' => array('BETWEEN' => array(1355439600, 1359068400))),
                                           array('visibility' => array('EQ' => true)),
                                           array('OR' => array(
                                                          array('field.name' => array('EQ' => 'News')),
                                                          array('full_text' => array( 'LIKE' => 'Press release*')))))),

                 'sort'   => array( 'field.landing_page/name' => 'ASC',
                                  'date_modified' => 'DESC'),
                 'limit'  => 5,
                 'offset' => 5);

    $result = $fetcher->fetch($query);


Prepared Fetch
~~~~~~~~~~~~~~

The concept is the same as for the PDO prepared statements. You prepare the query then you can bind parameters.
Parameter name can be any string. For example for the limit option you can use '@limit' or '?limit?' or '@l@' or
simply limit but for readability of your query you are encouraged to use a distinctive holder: i usualy prepend the holder with @ character.

.. code-block:: php

   // In controller get the fetch service
   $fetcher = $this->get('aw_ezp_fetch');

   // you can also use php array format insead of CQL
   $query = "{filter: {AND: [subtree: {EQ @subtree}, visibility: {EQ true}]}  , limit: @limit, offset: @offset}";

   $preparedFetch = $fetcher->prepare($query);

   $preparedFetch->bindParam('@subtree', '/1/2/60');
   $preparedFetch->bindParam('@offset', 0);
   $preparedFetch->bindParam('@limit', 20);

   $result = $preparedFetch->fetch();

   // you can also chain parameters binding
   $result = $preparedFetch->bindParam('@subtree', '/1/2/60')->bindParam('@offset', 0)->bindParam('@limit', 20)->fetch();

   // you can rebind any parameter and refetch
   $result = $preparedFetch->bindParam('@offset', 20)->fetch();

   // If needed you can reset all parameters before binding new ones
   $result = $preparedFetch->reset()->bindParam('@subtree', '/1/2/60')->bindParam('@offset', 20)->bindParam('@limit', 30)->fetch();


