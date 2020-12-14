#Back end Utils

Frequently reusable snippets from Drupal 8 and 9 code, that it seemed logical to
abstract into a reusable module code.

Service name: ```butils```

## Sections / Traits

### Array Trait
Contains helper functions for array handling.

- **arrayMap()** - Get a key from array by path, like ```key.subkey```.

This function can be used if existance of the path is not certain.

### CSV Trait
Contains helper functions for working with CSV files.

- **loadCsv()** - Loads a CSV file into an array using header keys as array keys.
- **writeCsv()** - Writes an array into a CSV file.

### DateTime Trait
Contains helper functions for Drupal datetime transcoding and formatting.

- **strToStamp()** - Converts date to timestamp with the time zone in mind.

- **strToDate()** - Formats the string date into datetime. Needed for datetime
fields.

- **dateToStamp()** - Convert date to timestamp with time zone in mind.

- **dateToFormat()** - Formats the datetime string with time zone in mind.

###DomDocument Trait
DomDocument related utilities.

- **domNodeInnerHtml()** - Get the inner html of a node without parent tag.

### Entity Trait
Contains Drupal content entity related helper functions.

- **toEntity()** - Checks whether an entity with parameters exists, and returns
it, or creates a new entity.

- **deref()** - Gets entity's value by path, like ```key.subkey```.
The function handles also reference fields and webform submissions.

Example: ```field_attached_file.0.field_caption```.

- **getViewModes()** - Gets the Drupal entity's enabled view modes.

- **entityCountWords()** - Counts words in a Drupal entity build using a view mode.

- **entityBuild()** - Build a Drupal entity render array using a view mode.

- **entityRender()** - Renders a Drupal entity using a view mode.

### Field Trait
Helper functions for working with Drupal fields.

- **getFieldDefinitions()** - Gets all Drupal entity field definitions of an 
  entity type and bundle. Can be used to check what fields of what type the 
  bundle has.

- **getFieldDefinitionsDetails()** - Gets additional information for the field
definition. Parameter is an array with field definition array, field name 
(details of whitch need to be loaded), entity type, and bundle.

- **emptyField()** - Empties the Drupal entity field correctly, by removing the 
  field instance.

- **viewField()** - Get a build array of a field of an entity without wrappers
 and labels.

- **renderField()** - Render a field of a Drupal entity without wrappers and labels.

- **getFieldValueByIds()** - Gets value of a field of a Drupal entity without loading
the entity itself. Use with caution, do not use in blanket operations!

### File Trait
Helper functions to work with Drupal file entities.

- **findFilesRecurive()** - Finds files in a folder and subfolders recursively.

- **fileRelativeUrl()** - Gets the file's relative URL.

- **uriToRelative()** - Converts URI to relative URL.

### Html Trait
Contains some helpers for Html and Xml.

- **cleanHtml()** - Cleans out some common HTML abuses. Removes comments, multiple
breaks, multiple subsequent CR and LF elements.

- **htmlToText()** - A wrapper around Drupal's function that converts HTML to text
preserving the document's look where possible (not just strip_tags()).
  
- **truncateHtml()** - Truncates a html string to a number of characters or words.

- **stripLinks()** - Strips links but preserve their text labels.

- **countWords()** - Counts words in a html string or plain text.

### ImageStyle Trait
Contains helper functions to work with Drupal image styles.

- **flushImageStyle()** - Flushes a specific Drupal image style derivatives.

- **flushAllImageStyles()** - Flushes derivatives for all Drupal image styles.

- **flushFileImageStyle()** - Flush the image style derivative of an image file.

- **flushFileAllImageStyles()** - Flash all image style derivatives of an image file.

- **rebuildImageStyles()** - Flush and rebuild image style derivatives for a file.

### Media Trait
Contains helper functions to work with Drupal core media.

- **mediaByFid()** - Find the media entity by it's file id.

### Paragraphs Trait
Drupal Paragraphs related helper functions and utilities.

- **deleteParagraphsRecurively()** - Deletes nested paragraphs recursively.

- **paragraphParentNode()** - Get the paragraph's parent node even for nested
paragraphs, recursively.
  
### SqlQuery Trait
Drupal SqlQuery related helper functions.

- **sqlQueryToString()** - Converts SQL Query to string in cases when the usual
**$query->toString()** does not cut it.
  
### State Trait
Drupal State API helper functions.

- **getState()** - Get State API variable value.

- **setState()** - Set State API variable value.

### String Trait
String related helper functions.

- **cleanString()** - Remove non-UTF characters form string.

### Taxonomy Trait
Drupal Taxonomy related helper functions.

- **toTerm()** - Get an existing taxonomy term or create a new one.

- **toTerms()** - A wrapper around ```toTerm()``` for multiple terms.

- **toHierarchicalTerms()** - A wrapper for ```toTerms()``` for with hierarchical
structure.

- **getTermsList()** - Gets all terms in a vocabulary by key/value conditions.

### Uri Trait
Drupal Uri handling helper functions.

- **uriToString()** - Converts Drupal URI to string.

### User Trait
Drupal User entity helper functions.

- **userAccessRoles()** - Checks an account access against an array of roles.
Use case - "allow access for administrator and editor roles".

### Xml Trait
Functions to facilitate working with Xml.

- **loadXmlFile()** - Loads and decodes and XML file.

- **mapXmlValues()** - Gets XML values by map like ```key.subkey```, respecting
the specific XML single/multiple value handling difference.

- **cleanXml()** - Removes Win-specific charcters from Xml.
