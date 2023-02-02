<?php
namespace TYPO3\CmAjax\ViewHelpers\Uri;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */
use TYPO3\CmAjax\Utility\AjaxBuilder;
use TYPO3\CMS\Extbase\Annotation\Inject;

/**
 * A view helper for creating remote Uri to extbase actions (JavaScript required, no fallback)
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ActionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {
  
  /**
   * @var bool
   */
  protected $escapeOutput = false;

  /**
	 * @var \TYPO3\CMS\Fluid\Core\Rendering\RenderingContext
	 */
	protected $renderingContext;
  
  public function initializeArguments() {
    $this->registerArgument('action', 'string', 'Target action', false, null);
    $this->registerArgument('arguments', 'array', 'Arguments', false, []);
    $this->registerArgument('includeFormData', 'string', 'Serializes the form data and use it instead of arguments. "true" means selecting the parents form node, "this" or a variable name is giving the form node itself, any other string means a CSS-Selector to select the form to serialize the data. Default is "false".', false, false);
    $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used', false, null);
    $this->registerArgument('update', 'string', 'Selector of element(s) which should be updated on success of ajax call.', false, null);
    $this->registerArgument('append', 'string', 'Selector of element(s) in which the result date should be appened on success of ajax call.', false, null);
    $this->registerArgument('prepend', 'string', 'Selector of element(s) in which the result date should be prepended on success of ajax call.', false, null);
    $this->registerArgument('updateJS', 'string', 'JavaScript that should be executed on success of ajax call (after updating the Element with ID $update with the responseText, if $update is given). Response objects \'xhr\' and \'json\' are available.', false, null);
    $this->registerArgument('error', 'string', 'Selector of element(s) which should be updated on error of ajax call. Default is $update, if no $errorJS is given.', false, null);
    $this->registerArgument('errorJS', 'string', 'JavaScript that should be executed on error of ajax call (after updating the Element with ID $error or $update with the responseText, if $error is given). Response objects \'xhr\' and \'json\' are available.', false, null);
    $this->registerArgument('loading', 'string', 'Selector of element(s) which should be updated when startet the ajax call. Default is $update.', false, null);
    $this->registerArgument('loadingText', 'string', 'HTML text to replace with content of $loading, while the ajax call is loading.', false, null);
    $this->registerArgument('dataType', 'string', 'return type of ajax call. Default is "html".', false, 'html');
    $this->registerArgument('ajaxAction', 'string', 'name of the action for the ajax call. By deflault it is the same as the action parameter.', false, null);
    $this->registerArgument('extensionName', 'string', 'Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used', false, null);
    $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used', false, null);
    $this->registerArgument('pageUid', 'integer', 'target page. See TypoLink destination', false, null);
    $this->registerArgument('pageType', 'integer', 'type of the target page. See typolink.parameter', false, 0);
    $this->registerArgument('noCache', 'boolean', 'set this to disable caching for the target page. You should not need this.', false, false);
    $this->registerArgument('noCacheHash', 'boolean', 'set this to supress the cHash query parameter created by TypoLink. You should not need this.', false, false);
    $this->registerArgument('section', 'string', 'the anchor to be added to the URI', false, '');
    $this->registerArgument('format', 'string', 'The requested format, e.g. ".html"', false, '');
    $this->registerArgument('linkAccessRestrictedPages', 'boolean', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.', false, false);
    $this->registerArgument('additionalParams', 'array', 'additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)', false, array());
    $this->registerArgument('absolute', 'boolean', 'If set, the URI of the rendered link is absolute', false, false);
    $this->registerArgument('addQueryString', 'boolean', 'If set, the current query parameters will be kept in the URI', false, false);
    $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'arguments to be removed from the URI. Only active if $addQueryString = TRUE', false, array());
  }
  
  /**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   * @Inject
   */
  public $configurationManager;
  
  /**
   * @param string $action Target action
   * @param array $arguments Arguments
   * @param string $includeFormData Serializes the form data and use it instead of arguments. "true" means selecting the parents form node, "this" or a variable name is giving the form node itself, any other string means a CSS-Selector to select the form to serialize the data. Default is "false".
   * @param string $controller Target controller. If NULL current controllerName is used
   * @param string $update Selector of element(s) which should be updated on success of ajax call.
   * @param string $append Selector of element(s) in which the result date should be appened on success of ajax call.
   * @param string $prepend Selector of element(s) in which the result date should be prepended on success of ajax call.
   * @param string $updateJS JavaScript that should be executed on success of ajax call (after updating the Element with ID $update with the responseText, if $update is given). Response objects 'xhr' and 'json' are available.
   * @param string $error Selector of element(s) which should be updated on error of ajax call. Default is $update, if no $errorJS is given.
   * @param string $errorJS JavaScript that should be executed on error of ajax call (after updating the Element with ID $error or $update with the responseText, if $error is given). Response objects 'xhr' and 'json' are available.
   * @param string $loading Selector of element(s) which should be updated when startet the ajax call. Default is $update.
   * @param string $loadingText HTML text to replace with content of $loading, while the ajax call is loading.
   * @param string $dataType return type of ajax call. Default is "html".
   * @param string $ajaxAction name of the action for the ajax call. By deflault it is the same as the action parameter.
   * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
   * @param string $pluginName Target plugin. If empty, the current plugin name is used
   * @param integer $pageUid target page. See TypoLink destination
   * @param integer $pageType type of the target page. See typolink.parameter
   * @param boolean $noCache set this to disable caching for the target page. You should not need this.
   * @param boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
   * @param string $section the anchor to be added to the URI
   * @param string $format The requested format, e.g. ".html"
   * @param boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
   * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
   * @param boolean $absolute If set, the URI of the rendered link is absolute
   * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
   * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
   * @return string Rendered link
   * @author Sebastian Kurfürst <sebastian@typo3.org>
   * @author Bastian Waidelich <bastian@typo3.org>
   */
  public function render() {
    
    $action = $this->arguments['action'];
    $arguments = (array) $this->arguments['arguments'];
    $includeFormData = $this->arguments['includeFormData'];
    $controller = $this->arguments['controller'];
    $update = $this->arguments['update'];
    $append = $this->arguments['append'];
    $prepend = $this->arguments['prepend'];
    $updateJS = $this->arguments['updateJS'];
    $error = $this->arguments['error'];
    $errorJS = $this->arguments['errorJS'];
    $loading = $this->arguments['loading'];
    $ajaxAction = $this->arguments['ajaxAction'];
    $extensionName = $this->arguments['extensionName'];
    $pluginName = $this->arguments['pluginName'];
    $pageUid = (int) $this->arguments['pageUid'];
    $pageType = (int) $this->arguments['pageType'];
    $noCache = (boolean) $this->arguments['noCache'];
    $noCacheHash = (boolean) $this->arguments['noCacheHash'];
    $section = $this->arguments['section'];
    $format = $this->arguments['format'];
    $linkAccessRestrictedPages = (boolean) $this->arguments['linkAccessRestrictedPages'];
    $additionalParams = (array) $this->arguments['additionalParams'];
    $absolute = (boolean) $this->arguments['absolute'];
    $addQueryString = (boolean) $this->arguments['addQueryString'];
    $argumentsToBeExcludedFromQueryString = (array) $this->arguments['argumentsToBeExcludedFromQueryString'];
    
    $ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->renderingContext->getControllerContext(),
      $action,
      $arguments,
      $includeFormData,
      $controller,
      $update, $append, $prepend,
      $updateJS,
      $error,
      $errorJS,
      $loading,
      NULL,
      "html",
      $ajaxAction,
      $extensionName,
      $pluginName,
      $pageUid,
      $pageType,
      $noCache,
      $noCacheHash,
      $section,
      $format,
      $linkAccessRestrictedPages,
      $additionalParams,
      $absolute,
      $addQueryString,
      $argumentsToBeExcludedFromQueryString);
    //$ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->renderingContext->getControllerContext(), $action, $arguments, $includeFormData, $controller, $update, $append, $prepend, $updateJS, $error, $errorJS, $loading, $loadingText, $dataType, $ajaxAction, $extensionName, $pluginName, $pageUid, $pageType, $noCache, $noCacheHash, $section, $format, $linkAccessRestrictedPages, $additionalParams, $absolute, $addQueryString, $argumentsToBeExcludedFromQueryString, false);
    
    return $ajaxCall;
  }
}
?>
