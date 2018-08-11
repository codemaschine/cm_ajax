<?php
namespace TYPO3\CmAjax\ViewHelpers\Link;

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
use \TYPO3\CmAjax\ViewHelpers\AbstractAjaxViewHelper;
use \TYPO3\CmAjax\Utility\AjaxBuilder;

/**
 * A view helper for creating remote links to extbase actions.
 *
 * = Examples =
 *
 * <code title="link to the show-action of the current controller">
 * <f:link.action action="show">action link</f:link.action>
 * </code>
 * <output>
 * <a href="index.php?id=123&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz">action link</f:link.action>
 * (depending on the current page and your TS configuration)
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ActionViewHelper extends AbstractAjaxViewHelper {
	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;
	
	
	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 * @author Bastian Waidelich <bastian@typo3.org>
	 * @author Jannes Dinse
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
		$this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
		$this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
	}
	
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
	 * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = TRUE
	 * @return string Rendered link
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function render($action = NULL, array $arguments = array(), $includeFormData = false, $controller = NULL, $update = NULL, $append = null, $prepend = null, $updateJS = null, $error = NULL, $errorJS = null, $loading = NULL, $loadingText = NULL, $dataType = "html", $ajaxAction = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $addQueryStringMethod = null) {
	  
	  
    $uriBuilder = $this->controllerContext->getUriBuilder();
		$uriPartObject = $uriBuilder
			->reset()
			->setTargetPageUid($pageUid)
			->setNoCache($noCache)
			->setUseCacheHash(!$noCacheHash)
			->setSection($section)
			->setFormat($format)
			->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
			->setArguments($additionalParams)
			->setCreateAbsoluteUri($absolute)
			->setAddQueryString($addQueryString)
			->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
			->setTargetPageType($pageType)
			->setAddQueryStringMethod($addQueryStringMethod)
			->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
    $this->tag->addAttribute('href', $uriPartObject);
		
    
    $ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->controllerContext,
        $this->arguments['action'],
        $this->arguments['arguments'],
        'this',
        $this->arguments['controller'],
        $this->arguments['update'],
        $this->arguments['append'],
        $this->arguments['prepend'],
        $this->arguments['updateJS'],
        $this->arguments['error'],
        $this->arguments['errorJS'],
        $this->arguments['loading'],
        $this->arguments['loadingText'],
        $this->arguments['dataType'],
        $this->arguments['ajaxAction'],
        $this->arguments['extensionName'],
        $this->arguments['pluginName'],
        $this->arguments['pageUid'],
        $this->arguments['pageType'],
        $this->arguments['noCache'],
        $this->arguments['noCacheHash'],
        $this->arguments['section'],
        $this->arguments['format'],
        $this->arguments['linkAccessRestrictedPages'],
        $this->arguments['additionalParams'],
        $this->arguments['absolute'],
        $this->arguments['addQueryString'],
        $this->arguments['argumentsToBeExcludedFromQueryString']);
    
    
    $onclick = $this->tag->getAttribute('onclick');
    
		$this->tag->addAttribute('onclick', ($onclick ? "if (function(){ $onclick }() === false) return false; " : '').$ajaxCall);
		
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(true);
		
		return $this->tag->render();
	}
	
	
	private function prepArg($arg) {
	  return urlencode($arg);
	}
}
?>