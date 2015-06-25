<?php

namespace Drupal\Hawk\Credentials;

use Dragooon\Hawk\Credentials\Credentials;
use Dragooon\Hawk\Credentials\CredentialsNotFoundException;
use Dragooon\Hawk\Credentials\CredentialsProviderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\hawk\Entity\HawkCredentialInterface;

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
    /** @var HawkCredentialInterface $credential */
    $credential = $this->entityManager->getStorage('hawk_credential')->load($id);

    if (empty($credential)) {
      throw new CredentialsNotFoundException($id . ' is not a valid credential ID');
    }

    return new Credentials($credential->getKeySecret(), $credential->getKeyAlgo(), $credential->id());
  }

}