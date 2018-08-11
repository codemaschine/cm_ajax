<?php
namespace TYPO3\CmAjax\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use \TYPO3\CMS\Core\Utility\GeneralUtility as t3lib_div;
use \TYPO3\CmAjax\Utility\AjaxBuilder;

/**
 * Form view helper. Generates a <form> Tag.
 *
 * = Basic usage =
 *
 * Use <f:form> to output an HTML <form> tag which is targeted at the specified action, in the current controller and package.
 * It will submit the form data via a POST request. If you want to change this, use method="get" as an argument.
 * <code title="Example">
 * <f:form action="...">...</f:form>
 * </code>
 *
 * = A complex form with a specified encoding type =
 *
 * <code title="Form with enctype set">
 * <f:form action=".." controller="..." package="..." enctype="multipart/form-data">...</f:form>
 * </code>
 *
 * = A Form which should render a domain object =
 *
 * <code title="Binding a domain object to a form">
 * <f:form action="..." name="customer" object="{customer}">
 *   <f:form.hidden property="id" />
 *   <f:form.textfield property="name" />
 * </f:form>
 * </code>
 * This automatically inserts the value of {customer.name} inside the textbox and adjusts the name of the textbox accordingly.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class FormViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper {

	/**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   * @inject
   */
  protected $configurationManager;
 
  /**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
	  $this->registerArgument('update', 'string', 'Selector of element(s) which should be updated on success of ajax call.');
	  $this->registerArgument('append', 'string', 'Selector of element(s) in which the result date should be appened on success of ajax call.');
	  $this->registerArgument('prepend', 'string', 'Selector of element(s) in which the result date should be prepended on success of ajax call.');
	  $this->registerArgument('updateJS', 'string', 'JavaScript that should be executed on success of ajax call (after updating the Element with ID $update with the responseText, if $update is given). Response objects \'xhr\' and \'json\' are available.');
	  $this->registerArgument('error', 'string', 'Selector of element(s) which should be updated on error of ajax call. Default is $update, if no $errorJS is given.');
	  $this->registerArgument('errorJS', 'string', 'JavaScript that should be executed on error of ajax call (after updating the Element with ID $error or $update with the responseText, if $error is given). Response objects \'xhr\' and \'json\' are available.');
	  $this->registerArgument('loading', 'string', 'Selector of element(s) which should be updated when startet the ajax call. Default is $update.');
	  $this->registerArgument('loadingText', 'string', 'HTML text to replace with content of $loading, while the ajax call is loading.');
	  $this->registerArgument('dataType', 'string', 'return type of ajax call. Default is "html".');
	  $this->registerArgument('ajaxAction', 'string', 'name of the action for the ajax call. By deflault it is the same as the action parameter.');
	  parent::initializeArguments();
	}
	
	
	/**
	 * Sets the "action" attribute of the form tag
	 *
	 * @return void
	 */
	protected function setFormActionUri()
	{
	  parent::setFormActionUri();
	  
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
	  
	  if ($this->arguments['onsubmit'])
	    $ajaxCall = "if (function(){ {$this->arguments['onsubmit']} }() === false) return false; ".$ajaxCall;
	    
	    $this->tag->addAttribute('onsubmit', $ajaxCall);
	}
	
}

?>