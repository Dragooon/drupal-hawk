<?php

namespace Drupal\hawk\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Plugin\views\argument_validator\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\user\UserInterface;
use Drupal\hawk\HawkCredentialStorageInterface;

class Hawk extends ControllerBase {

  /**
   * @var \Drupal\hawk\HawkCredentialStorageInterface
   */
  protected $hawkCredentialStorage;

  /**
   * Constructs Hawk controller object
   *
   * @param HawkCredentialStorageInterface $hawkCredentialStorage
   */
  public function __construct(HawkCredentialStorageInterface $hawkCredentialStorage) {
    die('test');
    $this->hawkCredentialStorage = $hawkCredentialStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entityManager = $container->get('entity.manager');
die('test');
    return new static(
      $entityManager->getStorage('hawk_credential')
    );
  }

  /**
   * Displays an user's credentials which they can manipulate
   *
   * @param UserInterface $user
   * @return array
   */
  public function credentials(UserInterface $user) {
    $credentials = $this->hawkCredentialStorage->loadByProperties(array('uid' => $user->id()));
    die(var_dump($credentials));
  }
}