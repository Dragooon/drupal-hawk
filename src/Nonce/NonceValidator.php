<?php

/**
 * @file
 * Contains Drupal\hawk_auth\Nonce\NonceValidator
 */

namespace Drupal\hawk_auth\Nonce;

use Dragooon\Hawk\Nonce\NonceValidatorInterface;

/**
 * Validator for nonce values during hawk requests
 */
class NonceValidator implements NonceValidatorInterface {
  /**
   * {@inheritDoc}
   */
  public function validateNonce($nonce, $timestamp) {
    //@todo: validate this properly
    return true;
  }
}
