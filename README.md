# cm_ajax

This TYPO3-Extension provides some AJAX-based ViewHelpers similar to the default Fluid ViewHelpers ``<f:link.action>`` and ``<f:form>``. It users their syntax and extends it to provide some more attributes to handle the results returned by the AJAX
call.

## Installation

* Download and install it from the TYPO3 Extension Repository or download it from here and put it into ``typo3conf/ext`` and install the extension.
• In your root template, add the extension template ``cm_ajax`` to the static includes. It should be loaded before your own extension is loaded.


## Prerequisites

The following conditions must be met to use the Ajax-Extension for your extension:
* Make sure to include the TypoScript Static Template in your site template.
* Make sure to include a jQuery-Library into your your Frontend. It is not included by extension, but requires it.
* In your extension TypoScript, set the following value to zero:

>>>
    plugin.tx_your_extension {
      ...
      features {
      ...
      # Should be on by default, but can be disabled if all action in the plugin are uncached
          requireCHashArgumentForActionArguments = 0
        }
    }

If you want to work with the ``form`` ViewHelper or with an AJAX Post Request, insert the following code into your controller:

    protected function initializeCreateAction() {
      $pmConfiguration = $this->arguments['___name_of_your_form_object_here___']->
      getPropertyMappingConfiguration(); $pmConfiguration->allowAllProperties(); $pmConfiguration ->setTypeConverterOption(
        'TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter',
        \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE
      ); 
    }

where you should insert the name of your form object here containing all the fields for your model instance. Also, you need to insert the ``@dontverifyrequesthash`` annotation into your ``createAction`` to get it to work.

## ViewHelpers

In the fluid templates and partials where you want to use the ajax-helpers, inlude this namespace at the beginning of the file:

    {namespace ajax=TYPO3\CmAjax\ViewHelpers}

### ``link.action`` and ``form`` ViewHelper

Now you can replace ``<f:link.action ...>`` with ``<ajax:link.action ... >`` and ``<f:form ...>`` with
``<ajax:form ...>`` to use the ajax functionality.

Both ViewHelpers extend their parent ViewHelpers with the following attributes:

* ``update``: CSS-Selector of element(s) which should be updated on success of ajax call.
* ``append``: CSS-Selector of element(s) in which the result should be appened on success of ajax call.
* ``prepend``: CSS-Selector of element(s) in which the result should be pepended on success of ajax call.
* ``updateJS``: JavaScript that should be executed on success of ajax call (after updating the Element with ID $update with the responseText, if $update is given). Response objects ‘xhr’ and ‘json’ are available.
* ``error``: Selector of element(s) which should be updated on error of ajax call. Default is $update, if no $errorJS is given.
* ``errorJS``: JavaScript that should be executed on error of ajax call (after updating the Element with ID $error or $update with the responseText, if $error is given). Response objects ‘xhr’ and ‘json’ are available.
* ``loading``: Selector of element(s) which should be updated when startet the ajax call. Default is $update. loadingText: HTML text to replace with content of $loading, while the ajax call is loading. dataType: return type of ajax call. Default is “html”.
* ``ajaxAction``: name of the action for the ajax call. By deflault it is the same as the action parameter.

### ``isXhr`` or ``isAjax`` (alias) ViewHelper
Returns true if it’s an AJAX Request.

### ``contentUid`` ViewHelper
Returns the UID of the Content Element. Useful if you need to replace the content of a Content Element after an AJAX
request and you need the element’s ID to create the CSS-Selector for this.

## Controller Helpers
To get some useful helpers in your Controller, extend your controller from ``\TYPO3\CmAjax\Controller\ApplicationController``.

### ``isXhr()`` or ``isAjax()`` (alias)
Parameters: None.
Returns true if it’s an AJAX Request.

### ``persistAll()``
Parameters: None. 
Returns void.

If you create a new element by an AJAX request and now want to return a list of elements containing the new element,
you need to persist your new element to the database before getting your list. Use ``persistAll()`` for this.
