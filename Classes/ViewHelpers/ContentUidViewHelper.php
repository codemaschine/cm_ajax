<?php
namespace TYPO3\CmAjax\ViewHelpers;

// http://www.felixnagel.com/blog/artikel/2012/07/20/typo3-use-content-element-uid-in-extbase-fluid-templates/


class ContentUidViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

  /**
   * @var bool
   */
  protected $escapeOutput = false;
 
  /**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   */
  protected $configurationManager;
 
  /**
   * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface An instance of the Configuration Manager
   * @return void
   */
  public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
    $this->configurationManager = $configurationManager;
  }
 
  /**
   * Set uid of the content element
   *
   * @return int $uid The uid of the content element
   */
  public function render() {
    // fallback
    $uid = uniqid();
 
    if ($this->templateVariableContainer->exists("contentObjectData")) {
      // this works for templates but not for partials
      $contentObjectData = $this->templateVariableContainer->get("contentObjectData");
      $uid = $contentObjectData['uid'];
    } else {
      // this should work in every circumstance
      $cObj = $this->configurationManager->getContentObject();
      $uid = $cObj->data['uid'];
    }
 
    return $uid;
  }
}

?>
