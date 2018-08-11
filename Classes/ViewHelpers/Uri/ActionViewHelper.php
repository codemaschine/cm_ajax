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
use \TYPO3\CMS\Core\Utility\GeneralUtility as t3lib_div;
use TYPO3\CmAjax\Utility\AjaxBuilder;

/**
 * A view helper for creating remote Uri to extbase actions (JavaScript required, no fallback)
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ActionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

  /**
   * @var bool
   */
  protected $escapeOutput = false;

	/**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   * @inject
   */
  protected $configurationManager;
 
  /**
	 * @param string $action Target action
	 * @param array $arguments Arguments
	 * @param boolean $includeFormData (Alpha, FE only) Serializes the form data and use it instead of arguments. Default is FALSE.
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
	public function render($action = NULL, array $arguments = array(), $includeFormData = false, $controller = NULL, $update = NULL, $updateJS = NULL, $append = null, $prepend = null, $error = NULL, $errorJS = NULL, $loading = NULL, $loadingText = NULL, $dataType = "html", $ajaxAction = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array()) {
	  
		
	  $ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->controllerContext, $action, $arguments, $includeFormData, $controller, $update, $append, $prepend, $updateJS, $error, $errorJS, $loading, $loadingText, $dataType, $ajaxAction, $extensionName, $pluginName, $pageUid, $pageType, $noCache, $noCacheHash, $section, $format, $linkAccessRestrictedPages, $additionalParams, $absolute, $addQueryString, $argumentsToBeExcludedFromQueryString, false);
	  
		return $ajaxCall;
	}
	

	private function prepArg($arg) {
	  return urlencode($arg);
	}
}
?>