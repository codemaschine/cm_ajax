<?php
namespace TYPO3\CmAjax\ViewHelpers;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * A view helper for creating remote Uri to extbase actions in onclick eventhandlers
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class AbstractAjaxViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * @var \TYPO3\CMS\Fluid\Core\Rendering\RenderingContext
	 */
	protected $renderingContext;
  
  protected function ajaxCall($action = NULL, $arguments = array(), $includeFormData = false, $controller = NULL, $update = NULL, $updateJS = NULL, $error = NULL, $errorJS = NULL, $loading = NULL, $loadingText = NULL, $dataType = "html", $ajaxAction = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $return = false) {
    if (TYPO3_MODE === 'FE') {
      $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
  		$remoteUri = $uriBuilder
  			->reset()
  			->setTargetPageUid($pageUid)
  			->setNoCache($noCache)
  			// ->setUseCacheHash(!$noCacheHash)
  			->setSection($section)
  			->setFormat($format)
  			->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
  			->setArguments($additionalParams)
  			->setCreateAbsoluteUri($absolute)
  			->setAddQueryString($addQueryString)
  			->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
    	 	->setTargetPageType(414864114)
  			->uriFor($ajaxAction ? $ajaxAction : $action, $arguments, $controller, $extensionName, $pluginName);
	  	
  	  // JQuery Framework in FE
  	  $ajaxCall = "$.ajax({ url: '$remoteUri'";
  		if ($includeFormData)
  		  $ajaxCall.= ", type: 'POST', data: $(this).parents('form').first().serializeArray()";
  		else if (!empty($arguments)) {
  		  $encodedElements = array();
  		  foreach ($arguments as $key => $value)
  		    array_push($encodedElements, "$key=".urlencode($value));
  		  $ajaxCall.= ", data: '".implode('&', $encodedElements)."'";
  		}
  		if ($update)
  		  $ajaxCall.= ", success: function(data) { $('#$update').html(data);  }";
  		if ($error)
  		  $ajaxCall.= ", error: function(data) { $('#$error').html(data) }";
  
  		$ajaxCall.= ", dataType: '$dataType' }); return ".($return ? "true" : "false").";";
	  }
    else {
      $request = $this->renderingContext->getRequest();
      
      if(!$extensionName)
        $extensionName = $request->getControllerExtensionName();
        
      if (!$controller)
        $controller = $request->getControllerName();
      
      if (!$pluginName)
        $pluginName = $request->getPluginName();
        
      if (!$ajaxAction)
        $ajaxAction = $action ? $action : $request->getControllerActionName();
        
      
  	  $remoteUri = 'ajax.php?ajaxID=cartyAjaxDispatcher&extensionName='.$this->prepArg($extensionName).'&pluginName='.$this->prepArg($pluginName).'&M='.$this->prepArg($pluginName).'&controllerName='.$this->prepArg($controller).'&actionName='.$this->prepArg($ajaxAction);
  		
  	  // Prototype Framework in BE
  	  $ajaxCall = "new Ajax.Request('$remoteUri', { asynchronous:true, evalScripts: true, evalJS: true";
  		if ($arguments) {
  		  $encodedElements = array();
  		  foreach ($arguments as $key => $value)
  		    if (is_object($value))
  		      array_push($encodedElements, "arguments[$key]=".urlencode($value->getUid()));
  		    else
  		      array_push($encodedElements, "arguments[$key]=".urlencode($value));
  		  $ajaxCall.= ", parameters: '".implode('&', $encodedElements)."'";
  		}
  		if ($loadingText)
  		  $ajaxCall.= ", onCreate: function(xhr, json) { $('".($loading ? $loading : $update)."').update('".str_replace("'", "\\'", $loadingText)."') }";
  		if ($update || $updateJS) {
  		  $ajaxCall.= ", onSuccess: function(xhr, json) { ";
  		  if ($update)
  		    $ajaxCall.= "$('$update').update(xhr.responseText);";
  		  if ($updateJS)
  		    $ajaxCall.= $updateJS;
  		  $ajaxCall.= " }";
  		}
  		if ($update || $error || $errorJS) {
  		  $ajaxCall.= ", onFailure: function(xhr, json) { ";
  		  if ($update && empty($errorJS) || $error)
  		    $ajaxCall.= "$('".($error ? $error : $update)."').update(xhr.responseText); ";
  		  if ($errorJS)
  		    $ajaxCall.= $errorJS;
  		  $ajaxCall.= " }";
  		}
      if ($update || $error)
  		  $ajaxCall.= ", onT3Error: function(xhr, json) { $('".($error ? $error : $update)."').update(xhr.responseText) }";
  
  		$ajaxCall.= " }); return ".($return ? "true" : "false").";";
  	}
  	
    return $ajaxCall;
  } 
	

	private function prepArg($arg) {
	  return urlencode($arg);
	}
}
?>
