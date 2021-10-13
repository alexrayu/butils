<?php

namespace Drupal\butils;

use Drupal\views\Views;
use Drupal\Core\Form\FormState;

/**
 * Trait Views.
 *
 * Views related utils.
 */
trait ViewsTrait {

  /**
   * Get the exposed views form.
   *
   * @param string $view_name
   *   Name of the view.
   * @param string $display_name
   *   Name of the display.
   *
   * @return array
   *   Views exposed form.
   */
  public function viewsExposedForm($view_name, $display_name = 'default') {
    $view = Views::getView($view_name);
    $view->setDisplay($display_name);
    $view->initHandlers();
    $form_state = (new FormState())
      ->setStorage([
        'view' => $view,
        'display' => &$view->display_handler->display,
        'rerender' => TRUE,
      ])
      ->setMethod('get')
      ->setAlwaysProcess()
      ->disableRedirect();
    $form_state->set('rerender', NULL);
    return \Drupal::formBuilder()->buildForm('\Drupal\views\Form\ViewsExposedForm', $form_state);
  }

}
