<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Authentication\Provider\HawkAuth.
 */

namespace Drupal\hawk_auth\Authentication\Provider;

use Dragooon\Hawk\Server\ServerInterface;
use Dragooon\Hawk\Server\UnauthorizedException;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\hawk_auth\Entity\HawkCredentialInterface;
use Drupal\migrate_drupal\Tests\Table\d6\Permission;
use Drupal\user\PermissionHandlerInterface;
use Drupal\user\RoleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Hawk Authentication provider.
 */
class HawkAuth implements AuthenticationProviderInterface {

  /**
   * Server interface for Hawk.
   *
   * @var ServerInterface
   */
  protected $server;

  /**
   * Entity manager.
   *
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The permission handler.
   *
   * @var \Drupal\user\PermissionHandlerInterface
   */
  protected $permissionHandler;

  /**
   * Constructs a HawkAuth object.
   *
   * @param ServerInterface $server
   *   Server interface for hawk.
   * @param EntityManagerInterface $entity_manager
   *   Entity Manager.
   * @param PermissionHandlerInterface $permission_handler
   *   Permission handler.
   */
  public function __construct(ServerInterface $server, EntityManagerInterface $entity_manager, PermissionHandlerInterface $permission_handler) {
    $this->server = $server;
    $this->entityManager = $entity_manager;
    $this->permissionHandler = $permission_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request) {
    return $this->server->checkRequestForHawk($request->headers->get('authorization'));
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {
    try {
      $response = $this->server->authenticate(
        $request->getMethod(),
        $request->getHost(),
        $request->getPort(),
        $request->getRequestUri(),
        $request->headers->get('content_type'),
        $request->getContent(),
        $request->headers->get('authorization')
      );
      /** @var HawkCredentialInterface $credentials */
      $credentials = $this->entityManager->getStorage('hawk_credential')->load($response->credentials()->id());

      $revoke_permissions = $credentials->getRevokePermissions();
      if (!empty($revoke_permissions)) {
        // We can't let the user save roles if we're revoking permissions.
        // Saving role in this state would permanently remove those
        // permissions from the user.
        // @todo: Maybe figure out a better way to revoke permissions.
        $revoke_permissions[] = 'administer permissions';

        $roles = $credentials->getOwner()->getRoles();
        /** @var RoleInterface[] $roles */
        $roles = $this->entityManager->getStorage('user_role')->loadMultiple($roles);
        foreach ($roles as $role) {
          // Admins by default have access to all permissions and cannot be
          // revoked. Set them as non-admin and grant all permissions to that
          // role.
          if ($role->isAdmin()) {
            $role->setIsAdmin(FALSE);
            foreach ($this->permissionHandler->getPermissions() as $perm => $data) {
              $role->grantPermission($perm);
            }
          }

          foreach ($revoke_permissions as $permission) {
            if ($role->hasPermission($permission)) {
              $role->revokePermission($permission);
            }
          }
        }
      }

      return $credentials->getOwner();
    }
    catch (UnauthorizedException $e) {
      return NULL;
    }
  }

}
