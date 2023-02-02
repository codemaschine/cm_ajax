<?php
/*
 * From:
 *
 * http://lbrmedia.net/codebase/Eintrag/extbase-eid-bootstrap/
 *
 *
 */
namespace TYPO3\CmAjax\Utility;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\Inject;
use \TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class could called via eID
 */
class FeAjaxBootstrap {
  
  /**
   * @var \array
   */
  protected $configuration;
  
  /**
   * @var \TYPO3\CMS\Extbase\Core\Bootstrap
   * @Inject
   */
  public $bootstrap;
  
  /**
   * The main Method
   *
   * @return string
   */
  public function run() {
    return $this->bootstrap->run( '', $this->configuration );
  }
  
  /**
   * Initialize Extbase
   *
   * @param \array $TYPO3_CONF_VARS
   */
  public function __construct($TYPO3_CONF_VARS) {
    /**
     * Gets the Ajax Call Parameters
     */
    $ajax = GeneralUtility::_GP('request');
    $pid = GeneralUtility::_GP('pid');
    
    /**
     * Set Vendor and Extension Name
     *
     * Vendor Name like your Vendor Name in namespaces
     * ExtensionName in upperCamelCase
     */
    if (!$ajax['vendorName'])
      $ajax['vendorName'] = 'TYPO3';
      
      $arg_key = 'tx_'.strtolower($ajax['extensionName']).'_'.strtolower($ajax['pluginName']);
      
      
      if (! $_POST[$arg_key]['action']) { // set default action, if not set
        $_POST[$arg_key]['action'] = $ajax['action'];
      }
      $_POST[$arg_key]['controller'] = $ajax['controller']; // set controller
      
      
      if (is_array($_GET['arguments'])) { // set GET-arguments
        foreach ($_GET['arguments'] as $key => $value) {
          if (!$_POST[$arg_key][$key])
            $_POST[$arg_key][$key] = $value;
        }
      }
      //GeneralUtility::devLog('Post: '.var_export($_POST, true), 'jdtest');
      
      
      
      // get User
      // $feUserObj = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();
      
      // set PID
      $pid = (GeneralUtility::_GET( 'id' )) ? GeneralUtility::_GET( 'id' ) : 1;
      
      // Create and init Frontend
      $GLOBALS['TSFE'] = GeneralUtility::makeInstance( 'TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $TYPO3_CONF_VARS, $pid, 0, TRUE );
      $GLOBALS['TSFE']->set_no_cache();
      // $GLOBALS['TSFE']->connectToDB();
      // $GLOBALS['TSFE']->fe_user = $feUserObj;
      $GLOBALS['TSFE']->id = $pid;
      $GLOBALS['TSFE']->determineId();
      // $GLOBALS['TSFE']->getCompressedTCarray();  // Deprecated!  // Full TCA is always loaded during bootstrap in FE, this method is obsolete.
      // $GLOBALS['TSFE']->initTemplate();
      $GLOBALS['TSFE']->getConfigArray();
      $GLOBALS['TSFE']->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
      //$GLOBALS['TSFE']->includeTCA();  // Deprecated! // since 6.1, will be removed in two versions. Obsolete in regular frontend, eid scripts should use \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA()
      
      // EidUtility::initTCA();
      // EidUtility::initFeUser();
      
      // Get Plugins TypoScript
      $TypoScriptService = new \TYPO3\CMS\Core\TypoScript\TypoScriptService();
      
      if (!$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_'.strtolower($ajax['extensionName']).'.'])
        throw new \Exception('No TypoScript-Setup for Extension '.$ajax['extensionName'].' available. Forgotten to include Static TypoScript for this plugin?');
        $pluginConfiguration = $TypoScriptService->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_'.strtolower($ajax['extensionName']).'.']);
        //GeneralUtility::devLog(var_export($pluginConfiguration, 1), 'ajaxdebug');
        //TODO: ContentID übertragen, FlexForm-Settings aus dem Contentelement laden und mit Settings mischen.
        
        // Set configuration to call the plugin
        $this->configuration = array (
            'pluginName' => $ajax['pluginName'],
            'vendorName' => $ajax['vendorName'],
            'extensionName' => $ajax['extensionName'],
            'controller' => $ajax['controller'],
            'action' => $_POST[$arg_key]['action'],
            'mvc' => array (
                'requestHandlers' => array (
                    'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler' => 'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler'
                )
            ),
            'settings' => $pluginConfiguration['settings'],
            'persistence' => array (
                'storagePid' => $pluginConfiguration['persistence']['storagePid']
            )
        );
        
        // Workaround WK: Argumente in settings speichern, da sie sonst nicht an Controller übergeben werden.
        foreach ($_GET['arguments'] as $key => $value) {
          $this->configuration['settings'][$key] = $value;
        }
  }
}

global $TYPO3_CONF_VARS;

if ($TYPO3_CONF_VARS['SYS']['systemLocale']) {
  setlocale(LC_COLLATE, $TYPO3_CONF_VARS['SYS']['systemLocale']);
  setlocale(LC_CTYPE, $TYPO3_CONF_VARS['SYS']['systemLocale']);
  setlocale(LC_MONETARY, $TYPO3_CONF_VARS['SYS']['systemLocale']);
  setlocale(LC_TIME, $TYPO3_CONF_VARS['SYS']['systemLocale']);
  setlocale(LC_MESSAGES, $TYPO3_CONF_VARS['SYS']['systemLocale']);
  // do not set LC_NUMERIC, because float values are displayed wrong in frontend. Use ViewHelpers instead.
}

/**
 * make instance of bootstrap and run
 * @var FeAjaxBootstrap
 */
$eid = GeneralUtility::makeInstance( FeAjaxBootstrap::class, $TYPO3_CONF_VARS );

echo $eid->run();
?>
