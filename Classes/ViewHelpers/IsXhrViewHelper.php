<?php
namespace TYPO3\CmAjax\ViewHelpers;


class IsXhrViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

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
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? 'true' : null;
  }

}

?>
