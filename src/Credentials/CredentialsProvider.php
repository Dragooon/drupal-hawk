<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Credentials\CredentialsProvider
 */

namespace Drupal\hawk_auth\Credentials;

use Dragooon\Hawk\Credentials\Credentials;
use Dragooon\Hawk\Credentials\CredentialsNotFoundException;
use Dragooon\Hawk\Credentials\CredentialsProviderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\hawk_auth\Entity\HawkCredentialInterface;

/**
 * Credentials callback for Hawk server, loads them from database when
 * authenticating.
 */
class CredentialsProvider implements CredentialsProviderInterface {

  /**
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * @param EntityManagerInterface $entity_manager
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
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
