<?php

namespace Drupal\hawk\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\UserInterface;

interface HawkCredentialInterface extends ContentEntityInterface {

  /**
   * @return int
   */
  public function getOwnerId();

  /**
   * @param int
   * @return $this
   */
  public function setOwnerId($id);

  /**
   * @return UserInterface
   */
  public function getOwner();

  /**
   * @param UserInterface $account
   * @return $this
   */
  public function setOwner(UserInterface $account);

  /**
   * @return string
   */
  public function getKeySecret();

  /**
   * @param string
   * @return $this
   */
  public function setKeySecret($keySecret);

  /**
   * @return string
   */
  public function getKeyAlgo();

  /**
   * @param string
   * @return $this
   */
  public function setKeyAlgo($keyAlgo);

}
