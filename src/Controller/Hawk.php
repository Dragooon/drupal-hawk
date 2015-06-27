<?php

namespace Drupal\hawk\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\UserInterface;
use Drupal\hawk\Entity\HawkCredentialStorageInterface;
use Symfony\Component\Routing\Route;

class Hawk extends ControllerBase implements AccessInterface {

  /**
   * @var \Drupal\hawk\Entity\HawkCredentialStorageInterface
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
        'operations' => [
          'data' => t('Operations'),
        ],
      ],
      '#rows' => [],
    ];

    foreach ($credentials as $credential) {
      $list['credentials']['#rows'][] = [
        'key_id' => $credential->id(),
        'key_secret' => $credential->getKeySecret(),
        'key_algo' => $credential->getKeyAlgo(),
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'delete' => [
                'title' => t('Delete'),
                'url' => Url::fromRoute('hawk.user_credential_delete', ['hawk_credential' => $credential->id()]),
              ],
            ],
          ],
        ],
      ];
    }

    return $list;
  }

  /**
   * Checks for access for viewing a user's hawk credentials
   *
   * @param Route $route
   *    The route to check against
   * @param RouteMatchInterface $route_match
   *    The current route being accessed
   * @param AccountInterface $account
   *    The account currently logged in
   * @return AccessResultInterface
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    if ($route_match->getRouteName() == 'hawk.user_credential') {
      /** @var AccountInterface $user */
      $user = $route_match->getParameter('user');

      return AccessResult::allowedIf(
        $account->hasPermission('administer hawk') ||
        ($account->hasPermission('access own hawk credentials') && $account->id() == $user->id())
      );
    }
  }
}
