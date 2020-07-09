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
use \TYPO3\CmAjax\ViewHelpers\AbstractAjaxViewHelper;
use \TYPO3\CmAjax\Utility\AjaxBuilder;
/**
 * A view helper for creating remote Uri to extbase actions (JavaScript required, no fallback)
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ActionViewHelper extends AbstractAjaxViewHelper {
  
  /**
   * @var bool
   */
  protected $escapeOutput = false;
  
    
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;
    
    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
      $this->configurationManager = $configurationManager;
    }
    
   
    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('extensionName', 'string', 'Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used');
        $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter');
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.');
        $this->registerArgument('noCacheHash', 'bool', 'Set this to suppress the cHash query parameter created by TypoLink. You should not need this.');
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.');
        $this->registerArgument('additionalParams', 'array', 'Additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)');
        $this->registerArgument('absolute', 'bool', 'If set, the URI of the rendered link is absolute');
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI');
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'Arguments to be removed from the URI. Only active if $addQueryString = TRUE');
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
        $this->registerArgument('arguments', 'array', 'Arguments for the controller action, associative array');
        
        $this->registerArgument('update', 'string', 'Selector of element(s) which should be updated on success of ajax call.');
        $this->registerArgument('append', 'string', 'Selector of element(s) in which the result date should be appened on success of ajax call.');
        $this->registerArgument('prepend', 'string', 'Selector of element(s) in which the result date should be prepended on success of ajax call.');
        $this->registerArgument('updateJS', 'string', 'JavaScript that should be executed on success of ajax call (after updating the Element with ID $update with the responseText, if $update is given). Response objects \'xhr\' and \'json\' are available.');
        $this->registerArgument('error', 'string', 'Selector of element(s) which should be updated on error of ajax call. Default is $update, if no $errorJS is given.');
        $this->registerArgument('errorJS', 'string', 'JavaScript that should be executed on error of ajax call (after updating the Element with ID $error or $update with the responseText, if $error is given). Response objects \'xhr\' and \'json\' are available.');
        $this->registerArgument('loading', 'string', 'Selector of element(s) which should be updated when startet the ajax call. Default is $update.');
        $this->registerArgument('loadingText', 'string', 'HTML text to replace with content of $loading, while the ajax call is loading.');
    }

    /**
     * @return string Rendered link
     */
    public function render()
    {
        $action = $this->arguments['action'];
        $controller = $this->arguments['controller'];
        $extensionName = $this->arguments['extensionName'];
        $pluginName = $this->arguments['pluginName'];
        $pageUid = (int)$this->arguments['pageUid'] ?: null;
        $pageType = (int)$this->arguments['pageType'];
        $noCache = (bool)$this->arguments['noCache'];
        $noCacheHash = (bool)$this->arguments['noCacheHash'];
        $section = (string)$this->arguments['section'];
        $format = (string)$this->arguments['format'];
        $linkAccessRestrictedPages = (bool)$this->arguments['linkAccessRestrictedPages'];
        $additionalParams = (array)$this->arguments['additionalParams'];
        $absolute = (bool)$this->arguments['absolute'];
        $addQueryString = (bool)$this->arguments['addQueryString'];
        $argumentsToBeExcludedFromQueryString = (array)$this->arguments['argumentsToBeExcludedFromQueryString'];
        $addQueryStringMethod = $this->arguments['addQueryStringMethod'];
        $parameters = $this->arguments['arguments'];
        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
            
        $ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->renderingContext->getControllerContext(),
            $action,
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
            $pageUid,
            $pageType,
            $noCache,
            $noCacheHash,
            $this->arguments['section'],
            $this->arguments['format'],
            $this->arguments['linkAccessRestrictedPages'],
            $additionalParams,
            $absolute,
            $addQueryString,
            $argumentsToBeExcludedFromQueryString);
        
    
    return $ajaxCall;
  }
  
  
  private function prepArg($arg) {
    return urlencode($arg);
  }
}
?>