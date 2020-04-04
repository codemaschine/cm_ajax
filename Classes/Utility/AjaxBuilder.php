<?php
namespace TYPO3\CmAjax\Utility;

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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Functions to build ajax call
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class AjaxBuilder {


  public static function ajaxCall($configurationManager, $controllerContext,
				    $action = NULL,
				    $arguments = array(),
				    $includeFormData = false,
				    $controller = NULL,
				    $update = NULL, $append = NULL, $prepend = NULL,
				    $updateJS = NULL,
				    $error = NULL,
				    $errorJS = NULL,
				    $loading = NULL,
				    $loadingText = NULL,
				    $dataType = "html",
				    $ajaxAction = NULL,
				    $extensionName = NULL,
				    $pluginName = NULL,
				    $pageUid = NULL,
				    $pageType = 0,
				    $noCache = FALSE,
				    $noCacheHash = FALSE,
				    $section = '',
				    $format = '',
				    $linkAccessRestrictedPages = FALSE,
				    array $additionalParams = array(),
				    $absolute = FALSE,
				    $addQueryString = FALSE,
				    array $argumentsToBeExcludedFromQueryString = array(),
				    $return = false,
   					$vendorName = NULL) {
    
   					  
	  $request = $controllerContext->getRequest();
	  
	  if(!$vendorName)
	    $vendorName = $request->getControllerVendorName();
	    
    if(!$extensionName)
      $extensionName = $request->getControllerExtensionName();
      
    if (!$controller)
      $controller = $request->getControllerName();
      
    if (!$pluginName)
      $pluginName = $request->getPluginName();
      
    if (!$ajaxAction)
      $ajaxAction = $action ? $action : $request->getControllerActionName();
    
    $extension_prefix = 'tx_'.strtolower(str_replace('_', '', $extensionName)).'_'.strtolower(str_replace('_', '', $pluginName));
    $argument_prefix = $extension_prefix.'[arguments]';
      


    if (TYPO3_MODE === 'FE') {
      /*
    	$uriBuilder = $controllerContext->getUriBuilder();
  		$remoteUri = $uriBuilder
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
    	 	->setTargetPageType(414864114)
  			->uriFor($ajaxAction ? $ajaxAction : $action, $arguments, $controller, $extensionName, $pluginName);
	  	*/

    	


    	if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    		$cObj = $configurationManager->getContentObject();
    		//t3lib_div::devLog('hierrrrrrr'.$cObj->data['pages'].'-', 'jdtest');
    		if ($cObj) {
    			$storagePages = $cObj->data['pages'];
    			$cUid = $cObj->data['uid'];
    		}
    	}
    	else
    		$cUid = t3lib_div::_GP('cUid');

    	
    	$remoteUri = '/index.php?eID=cmAjaxDispatcher&id='.$GLOBALS['TSFE']->id.'&request[vendorName]='.self::prepArg($vendorName).'&request[extensionName]='.self::prepArg($extensionName).'&request[pluginName]='.self::prepArg($pluginName).'&request[controller]='.self::prepArg($controller).'&request[action]='.self::prepArg($ajaxAction).'&cUid='.self::prepArg($cUid);

    	foreach ($additionalParams as $key => $value) {
    		$remoteUri .= '&'.self::prepArg($key).'='.self::prepArg($value);
    	}

    	if ($storagePages) {
    		//t3lib_div::devLog('ab doch', 'jdtest');
    		$remoteUri .= "&storagePages=".self::prepArg($storagePages);
    	} else if (t3lib_div::_GP('storagePages'))
    		$remoteUri .= "&storagePages=".self::prepArg(t3lib_div::_GP('storagePages'));
    	//else
    	//	t3lib_div::devLog('ab nischhhhhhh', 'jdtest');

  	  // JQuery Framework in FE
  	  $ajaxCall = "$.ajax({ url: '$remoteUri'";
  	  if ($includeFormData) {
				$encodedElementsForData = array();
			  foreach ($arguments as $key => $value) {
			    array_push($encodedElementsForData, "{name: \"".$extension_prefix."[$key]\", value: \"".urlencode($value instanceof AbstractEntity ? $value->getUid() : $value)."\"}");
				}
  		  $ajaxCall.= ", type: 'POST', data: $.merge($(this)".($includeFormData === 'this' ? '' : ".parents('form').first()").".serializeArray(), [".implode(', ', $encodedElementsForData)."])";
  		} else if (!empty($arguments)) {
  		  $encodedElements = array();
  		  foreach ($arguments as $key => $value)
  		    array_push($encodedElements, "arguments[$key]=".urlencode($value instanceof AbstractEntity ? $value->getUid() : $value));
  		  $ajaxCall.= ", data: '".implode('&', $encodedElements)."'";
  		}
  		if ($loading || $loadingText) {
  			$ajaxCall.= ", beforeSend: function(xhr) { ";
  			if ($loading)
  				$ajaxCall .= "$('$loading').show();";
  			if ($loadingText)
  			  $ajaxCall .= "$('".($loading ? $loading : $update)."').html('".str_replace("'", "\\'", $loadingText)."');";
  			$ajaxCall.=  " }";

  			if ($loading)
  				$ajaxCall .= ", complete: function(xhr) { $('$loading').hide(); }";
  		}
  		if ($update || $updateJS) {
  		  $ajaxCall.= ", success: function(data) { ";
  		  if ($update)
  		  	$ajaxCall .= " $('$update').html(data);"; // code to find and execute script tags is not needed, script tags are automatically executed by jQuery //  var dom = $(data); dom.find('script').each(function(){ $.globalEval(this.text || this.textContent || this.innerHTML || '');});
  		  if ($prepend)
  		  	$ajaxCall .= " $('$prepend').prepend(data);";
  		  if ($append)
  		  	$ajaxCall .= " $('$append').append(data);";
  		  if ($updateJS)
  		  	$ajaxCall .=  $updateJS;
  		  $ajaxCall.=  " }";
  		}
  		if ($update || $error || $errorJS) {
  			$ajaxCall.= ", error: function(xhr, textStatus, errorThrown) { ";
  			if ($update && empty($errorJS) || $error)
  				$ajaxCall.= "$('".($error ? $error : $update)."').html(xhr); ";
  			if ($errorJS)
  				$ajaxCall.= $errorJS;
  			$ajaxCall.=  " }";

  		}

  		$ajaxCall.= ", dataType: '$dataType' }); return ".($return ? "true" : "false").";";
	  }
    else {
      
      $extension_prefix = 'tx_'.strtolower(str_replace('_', '', $extensionName)).'_'.strtolower($pluginName);
      $argument_prefix = $extension_prefix.'[arguments]';

      $remoteUriArgs = '&vendorName='.self::prepArg($vendorName).'&extensionName='.self::prepArg($extensionName).'&pluginName='.self::prepArg($pluginName).'&M='.self::prepArg($pluginName).'&controllerName='.self::prepArg($controller).'&actionName='.self::prepArg($ajaxAction);

  	  foreach ($arguments as $key => $value) {
  	  	$remoteUriArgs .= '&arguments['.self::prepArg($key).']='.self::prepArg($value instanceof AbstractEntity ? $value->getUid() : $value);
  	  }

  	  // JQuery Framework
  	  $ajaxCall = "$.ajax({ url: TYPO3.settings.ajaxUrls['cm_backend_router']+'$remoteUriArgs'";
  	  if ($includeFormData) {
				$encodedElementsForData = array();
			  foreach ($arguments as $key => $value) {
			    array_push($encodedElementsForData, "{name: '".$extension_prefix."[$key]', value: '".urlencode($value instanceof AbstractEntity ? $value->getUid() : $value)."'}");
				}
				$ajaxCall.= ", type: 'POST', data: $.merge($(this)".($includeFormData === 'this' ? '' : ".parents('form').first()").".serializeArray(), [".implode(', ', $encodedElementsForData)."])";
  	  	//$ajaxCall.= ", type: 'POST', data: $(this)".($includeFormData === 'this' ? '' : ".parents('form').first()").".serializeArray()";
			} /*else if (!empty($arguments)) {
  	  	$encodedElements = array();
  	  	foreach ($arguments as $key => $value)
  	  		array_push($encodedElements, "$key=".urlencode($value));
  	  	$ajaxCall.= ", data: '".implode('&', $encodedElements)."'";
  	  }*/
  	  if ($loading || $loadingText) {
  	  	$ajaxCall.= ", beforeSend: function(xhr) { ";
  	  	if ($loading)
  	  		$ajaxCall .= "$('$loading').show();";
  	  	if ($loadingText)
  	  		$ajaxCall .= "$('".($loading ? $loading : $update)."').html('".str_replace("'", "\\'", $loadingText)."');";
  	  	$ajaxCall.=  " }";

  	  	if ($loading)
  	  		$ajaxCall .= ", complete: function(xhr) { $('$loading').hide(); }";
  	  }
  	  if ($update || $updateJS) {
  	  	$ajaxCall.= ", success: function(data) { ";
  	  	if ($update)
  	  		$ajaxCall .= " $('$update').html(data); var dom = $(data); dom.find('script').each(function(){ $.globalEval(this.text || this.textContent || this.innerHTML || '');});";
  	  	if ($append)
  	  	  $ajaxCall .= " $('$append').append(data);";
  	  	if ($prepend)
  	  	  $ajaxCall .= " $('$prepend').prepend(data);";
  	  	if ($updateJS)
  	  		$ajaxCall .=  $updateJS;
  	  		$ajaxCall.=  " }";
  	  }
  	  if ($update || $error || $errorJS) {
  	  $ajaxCall.= ", error: function(xhr, textStatus, errorThrown) { ";
  			if ($update && empty($errorJS) || $error)
  	    			$ajaxCall.= "$('".($error ? $error : $update)."').html(xhr); ";
  	    					if ($errorJS)
  				$ajaxCall.= $errorJS;
  	    				$ajaxCall.=  " }";

  	  }

  	  $ajaxCall.= ", dataType: '$dataType' }); return ".($return ? "true" : "false").";";

  	  /*
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

  		*/
  	}

    return $ajaxCall;
  }


	private static function prepArg($arg) {
	  return urlencode($arg);
	}
}
?>
