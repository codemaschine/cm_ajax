<?php 
/*

$TYPO3_CONF_VARS['FE']['eID_include']['cmAjaxDispatcher'] = t3lib_extMgm::extPath('cm_ajax').'Classes/Utility/AjaxDispatcher.php:Tx_CmAjax_Utility_AjaxDispatcher->dispatch';
$TYPO3_CONF_VARS['BE']['AJAX']['cmAjaxDispatcher'] = t3lib_extMgm::extPath('cm_ajax').'Classes/Utility/AjaxDispatcher.php:Tx_CmAjax_Utility_AjaxDispatcher->dispatch';

*/
//$TYPO3_CONF_VARS['FE']['eID_include']['cmAjaxDispatcher'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cm_ajax').'Classes/Utility/FeAjaxDispatcher.php';
$TYPO3_CONF_VARS['FE']['eID_include']['cmAjaxDispatcher'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cm_ajax').'Classes/Utility/FeAjaxBootstrap.php';
//$TYPO3_CONF_VARS['BE']['AJAX']['cmBeAjaxDispatcher'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cm_ajax').'Classes/Utility/AjaxDispatcher.php:Tx_CmAjax_Utility_AjaxDispatcher->dispatch';

?>