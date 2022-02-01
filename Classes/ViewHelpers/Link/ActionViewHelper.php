<?php
namespace TYPO3\CmAjax\ViewHelpers\Link;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use \TYPO3\CmAjax\ViewHelpers\AbstractAjaxViewHelper;
use \TYPO3\CmAjax\Utility\AjaxBuilder;

/**
 * A ViewHelper for creating links to extbase actions.
 *
 * Examples
 * ========
 *
 * link to the show-action of the current controller::
 *
 *    <f:link.action action="show">action link</f:link.action>
 *
 * Output::
 *
 *    <a href="index.php?id=123&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz">action link</a>
 *
 * Depending on the current page and your TypoScript configuration.
 */
class ActionViewHelper extends AbstractAjaxViewHelper
{
  
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
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('includeFormData', 'string', 'Serializes the form data and use it instead of arguments. "true" means selecting the parents form node, "this" or a variable name is giving the form node itself, any other string means a CSS-Selector to select the form to serialize the data. Default is "false".', false, false);
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
        $section = $this->arguments['section'] ?? '';
        $format = $this->arguments['format'] ?? '';
        $linkAccessRestrictedPages = (boolean) $this->arguments['linkAccessRestrictedPages'];
        $additionalParams = (array) $this->arguments['additionalParams'];
        $absolute = (boolean) $this->arguments['absolute'];
        $addQueryString = (boolean) $this->arguments['addQueryString'];
        $argumentsToBeExcludedFromQueryString = (array) $this->arguments['argumentsToBeExcludedFromQueryString'];
        $addQueryStringMethod = $this->arguments['addQueryStringMethod'] ?? '';
      
        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setUseCacheHash(!$noCacheHash)
            ->setSection($section)
            ->setFormat($format)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->setAddQueryStringMethod($addQueryStringMethod)
            ->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
        if ($uri === '') {
            return $this->renderChildren();
        }
        $this->tag->addAttribute('href', $uri);
        
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
        //$ajaxCall = AjaxBuilder::ajaxCall($this->configurationManager, $this->renderingContext->getControllerContext(), $action, $arguments, $includeFormData, $controller, $update, $updateJS, $error, $errorJS, $loading, $loadingText, $dataType, $ajaxAction, $extensionName, $pluginName, $pageUid, $pageType, $noCache, $noCacheHash, $section, $format, $linkAccessRestrictedPages, $additionalParams, $absolute, $addQueryString, $argumentsToBeExcludedFromQueryString);
        
        $onclick = $this->tag->getAttribute('onclick');
        
        $this->tag->addAttribute('onclick', ($onclick ? "if (function(){ $onclick }() === false) return false; " : '').$ajaxCall);
        
        
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);
        return $this->tag->render();
    }
}

?>