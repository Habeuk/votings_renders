<?php

namespace Drupal\votings_renders\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for votings_renders routes.
 */
class VotingsRendersController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
