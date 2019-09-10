#Back end Utils

This module provides a set of frequently used back end utilities as services.

Service name: ```butils```

## Sections / Traits

### Array
Contains helper functions for array handling.

- **arrayMap()** - Get a key from array by path, like ```key.subkey```.

This function can be used if existance of the path is not certain.

### DateTime
Contains helper functions for datetime transcoding and formatting.

- **strToStamp()** - Converts date to timestamp with the time zone in mind.

- **strToDate()** - Formats the string date into datetime. Needed for datetime
fields.

- **dateToStamp()** - Convert date to timestamp with time zone in mind.

- **dateToFormat()** - Formats the datetime string with time zone in mind.

### Entity
Contains entity related helper functions.

- **toEntity()** - Checks whether an entity with parameters exists, and returns
it, or creates a new entity.

- **deref()** - Gets entity's value by path, like ```key.subkey```.
The function handles also reference fields and webform submissions.

Example: ```field_attached_file.0.field_caption```.

- **getViewModes()** - Gets the entity's enabled view modes.

- **emptyField()** - Empties the field correctly, by removing the field instance.

### Field
Helper functions for working with fields.

- **getFieldDefinitions()** - Gets all field definitions of an entity type and
bundle. Can be used to check what fields of what type the bundle has.

- **getFieldDefinitionsDetails()** - Gets additional information for the field
definition. Parameter is an array with field definition array, field name 
(details of whitch need to be loaded), entity type, and bundle.

### File
Helper functions to work with files.

- **findFilesRecurive()** - Finds files in a folder and subfolders recursively.

- **fileRelativeUrl()** - Gets the file's relative URL.

- **uriToRelative()** - Converts URI to relative URL.

### Html
Contains some helpers for Html and Xml.

- **cleanHtml()** - Cleans out some common HTML abuses. Removes comments, multiple
breaks, multiple subsequent CR and LF elements.

- **htmlToText()** - A wrapper around Drupal's function that converts HTML to text
preserving the document's look where possible (not just strip_tags()).

### String
String related functions.

- **cleanString()** - Remove non-UTF characters form string.

- **regexRecursive()** - Run a set of regex on a string recursively.

### Taxonomy
Taxonomy related helper functions.

- **toTerm()** - Get an existing taxonomy term or create a new one.

- **toTerms()** - A wrapper around ```toTerm()``` for multiple terms.

- **toHierarchicalTerms()** - A wrapper for ```toTerms()``` for with hierarchical
structure.

- **getTermsList()** - Gets all terms in a vocabulary by key/value conditions.

### Xml
Functions to facilitate working with Xml.

- **loadXmlFile()** - Loads and decodes and XML file.

- **mapXmlValues()** - Gets XML values by map like ```key.subkey```, respecting
the specific XML single/multiple value handling difference.

- **cleanXml()** - Removes Win-specific charcters from Xml.
