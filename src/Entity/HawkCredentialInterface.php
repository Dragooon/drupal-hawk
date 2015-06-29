<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Entity\HawkCredentialInterface.
 */

namespace Drupal\hawk_auth\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\UserInterface;

/**
 * Interface defines individual hawk credential's model.
 */
interface HawkCredentialInterface extends ContentEntityInterface {

  /**
   * Returns the ID of the User this credential belongs to.
   *
   * @return int
   */
  public function getOwnerId();

  /**
   * Sets the ID of the User this credential belongs to.
   *
   * @param int
   * @return $this
   */
  public function setOwnerId($id);

  /**
   * Returns the object of the user this credential belongs to.
   *
   * @return UserInterface
   */
  public function getOwner();

  /**
   * Sets the ID of the owner from the object of the user this credential
   * belongs to.
   *
   * @param UserInterface $account
   * @return $this
   */
  public function setOwner(UserInterface $account);

  /**
   * Returns the key secret.
   *
   * @return string
   */
  public function getKeySecret();

  /**
   * Sets the key secret.
   *
   * @param string
   * @return $this
   */
  public function setKeySecret($keySecret);

  /**
   * Returns the algorithm for hashing.
   *
   * @return string
   */
  public function getKeyAlgo();

  /**
   * Sets the algorithm for hashing.
   *
   * @param string
   * @return $this
   */
  public function setKeyAlgo($keyAlgo);

}
