<?php

namespace Drupal\Hawk\Nonce;

use Dragooon\Hawk\Nonce\NonceValidatorInterface;

class NonceValidator implements NonceValidatorInterface {
  /**
   * {@inheritDoc}
   */
  public function validateNonce($nonce, $timestamp) {
    //@todo: validate this properly
    return true;
  }
}
