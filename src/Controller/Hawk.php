<?php

namespace Drupal\hawk\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\UserInterface;
use Drupal\hawk\HawkCredentialStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    $this->hawkCredentialStorage = $hawkCredentialStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entityManager = $container->get('entity.manager');

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
  public function credential(UserInterface $user) {
    /** @var \Drupal\hawk\Entity\HawkCredentialInterface[] $credentials */
    $credentials = $this->hawkCredentialStorage->loadByProperties(array('uid' => $user->id()));

    $list = [];

    $list['heading']['#markup'] = $this->t('<a href="!url">Add Credential</a>', [
      '!url' => Url::fromRoute('hawk.user_credential_add')->toString(),
    ]);

    $list['credentials'] = [
      '#type' => 'table',
      '#header' => [
        'key_id' => [
          'data' => t('ID'),
        ],
        'key_secret' => [
          'data' => t('Key Secret'),
        ],
        'key_algo' => [
          'data' => t('Key Algorithm'),
        ],
      ],
      '#rows' => [],
    ];

    foreach ($credentials as $credential) {
      $list['credentials']['#rows'][] = [
        'key_id' => $credential->id(),
        'key_secret' => $credential->getKeySecret(),
        'key_algo' => $credential->getKeyAlgo(),
      ];
    }

    return $list;
  }

}