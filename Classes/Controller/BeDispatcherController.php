<?php

namespace TYPO3\CmAjax\Controller;

/***************************************************************
* Copyright notice
*
*   2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
* All rights reserved
*
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
use \TYPO3\CMS\Core\Utility\GeneralUtility as t3lib_div; 
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
* Utility to include defined frontend libraries as jQuery and related CSS
*  
*
* @package Utility
* @author Daniel Lienert <daniel@lienert.cc>
*/
 
class BeDispatcherController {
     
     
    /**
     * Array of all request Arguments
     * 
     * @var array
     */
    protected $requestArguments = array();
     
     
     
    /**
     * Extbase Object Manager
     * @var ObjectManager
     */
    protected $objectManager;
     
     
    /**
     * @var string
     */
    protected $vendorName;
     
     
    /**
     * @var string
     */
    protected $extensionName;
     
     
    /**
     * @var string
     */
    protected $pluginName;
     

    /**
     * @var string
     */
    protected $moduleName;
    
    
    /**
     * @var string
     */
    protected $controllerName;
     
     
    /**
     * @var string
     */
    protected $actionName;
     
     
    /**
     * @var array
     */
    protected $arguments;
     
    /**
     * @var array
     */
    protected $additionalParams;
     
    /**
     * @var string
     */
    protected $method = 'GET';
    
    
    
    
     
    /**
     * Called by ajax.php / eID.php
     * Builds an extbase context and returns the response
     */
    public function dispatch(
        ServerRequestInterface $mwRequest,
        \TYPO3\CMS\Core\Http\Response $mwResponse
        ) {
        
          $this->prepareCallArguments();
         
        $configuration = array();
        $configuration['extensionName'] = $this->extensionName;
        $configuration['pluginName'] = $this->pluginName;
        $configuration['vendorName'] = $this->vendorName;
        if (!empty($this->moduleName)) {
          define('TYPO3_MODE','BE');
          $configuration['pluginName'] = $this->moduleName;
        }
        #$res = mysql(TYPO3_db, "select pages from tt_content where list_type = '".mysql_escape_string($this->pluginName).'" and pid = '.mysql_escape_string(t3lib_div::_GP('id')));
        #t3lib_div::devLog("select pages from tt_content where list_type = '".mysql_escape_string($this->pluginName).'" and pid = '.mysql_escape_string(t3lib_div::_GP('id')), "carty");
        #if ($row = mysql_fetch_row($res)) {
        #  $configuration['storagePid'] = $row[0];
        #}
        
        
        
        //t3lib_div::devLog(var_export($this->arguments, true), "cm_ajax");
         
        
        $bootstrap =  t3lib_div::makeInstance('TYPO3\\CMS\\Extbase\\Core\\Bootstrap'); //  t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');  
        $bootstrap->initialize($configuration);
         
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        
        $request = $this->buildRequest();
        $response = $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Response::class);
        
        $dispatcher =  $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Dispatcher::class);
        $dispatcher->dispatch($request, $response);
        
        $response->setHeader('Content-Type','text/html; charset=UTF-8');
        $response->sendHeaders();
        echo $response->getContent();
         
        $this->cleanShutDown();
        return $mwResponse->withHeader('Content-Type','text/html; charset=UTF-8');
    }
 
     
    protected function cleanShutDown() {
        $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
        //$this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService')->shutdown();
    }
     
     
    /**
     * Build a request object
     * 
     * @return Request $request
     */
    protected function buildRequest() {
        $request = $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Request::class);
        $request->setControllerVendorName($this->vendorName);
        $request->setControllerExtensionName($this->extensionName);
        $request->setPluginName($this->pluginName);
        $request->setControllerName($this->controllerName);
        $request->setControllerActionName($this->actionName);
        $request->setArguments($this->arguments);
        
        $request->setMethod($this->method); 
        //$request->setHmacVerified(TRUE);    // TODO: Doesn't work right now.
         
        return $request;
    }
     
 
    /**
     * Prepare the call arguments
     */
    protected function prepareCallArguments() {
        $request = t3lib_div::_GP('request');
        
        $this->setRequestArgumentsFromGetPost();
        if($request) {
          $this->setRequestArgumentsFromJSON($request);
        }
        $this->vendorName				 = $this->requestArguments['vendorName'];
        $this->extensionName     = $this->requestArguments['extensionName'];
        $this->pluginName        = $this->requestArguments['pluginName'];
        $this->moduleName        = $this->requestArguments['moduleName'];
        $this->controllerName    = $this->requestArguments['controllerName'];
        $this->actionName        = $this->requestArguments['actionName'];
        $this->additionalParams  = $this->requestArguments['additionalParams'];
        
        $this->arguments         = $this->requestArguments['arguments'];
        if (empty($this->arguments) || !is_array($this->arguments)) 
          $this->arguments = array();
          
        $post = t3lib_div::_POST(strtolower('tx_'.$this->extensionName.'_'.$this->pluginName));
        if ($post) {
        	
          foreach($post as $key => $value)
            $this->arguments[$key] = $value;
          
          $this->method = 'POST';
        }
    }
     
     
     
    /**
     * Set the request array from JSON
     * 
     * @param string $request
     */
    protected function setRequestArgumentsFromJSON($request) {
        $requestArray = json_decode($request, true);
        if(is_array($requestArray)) {
            $this->requestArguments = t3lib_div::array_merge_recursive_overrule($this->requestArguments, $requestArray);
        }
    }
     
     
     
    /**
     * Set the request array from the getPost array
     */
    protected function setRequestArgumentsFromGetPost() {
        $validArguments = array('vendorName', 'extensionName','moduleName','pluginName','controllerName','actionName','arguments','additionalParams');
        foreach($validArguments as $argument) {
            if(t3lib_div::_GP($argument)) $this->requestArguments[$argument] = t3lib_div::_GP($argument);
        }
    }
}
?>