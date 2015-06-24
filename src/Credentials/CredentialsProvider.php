<?php

namespace Drupal\Hawk\Credentials;

use Dragooon\Hawk\Credentials\Credentials;
use Dragooon\Hawk\Credentials\CredentialsProviderInterface;
use Drupal\Core\Entity\EntityManagerInterface;

class CredentialsProvider implements CredentialsProviderInterface {

  /**
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritDoc}
   */
  public function loadCredentialsById($id) {
    $credential = $this->entityManager->getStorage('hawk_credential')->load($id);

    if (empty($credential)) {
      return false;
    }

    return new Credentials($credential->getKeySecret(), $credential->getKeyAlgo(), $credential->id());
  }

}