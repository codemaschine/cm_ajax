<?php
namespace TYPO3\CmAjax\ViewHelpers;


class IsXhrViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

  /**
   * @var bool
   */
  protected $escapeOutput = false;

  /**
   * Is XmlHttpRequest?
   *
   * @return string
   */
  public function render() {
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? 'true' : null;
  }

}

?>