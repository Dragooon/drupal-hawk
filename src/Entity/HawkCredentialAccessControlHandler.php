<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Entity\HawkCredentialAccessControlHandler
 */

namespace Drupal\hawk_auth\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Hawk Credential Access Control Handler, checks for delete and create permission
 * for individual credentials.
 */
class HawkCredentialAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritDoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    /** @var \Drupal\hawk_auth\Entity\HawkCredentialInterface $entity */

    if ($operation == 'delete') {
      $user = $entity->getOwner();

      return AccessResult::allowedIf(
        $account->hasPermission('administer hawk') ||
        ($account->hasPermission('access own hawk credentials') && $account->id() == $user->id())
      );
    }
    else {
      return parent::checKAccess($entity, $operation, $langcode, $account);
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, array('administer hawk', 'access own hawk credentials'), 'OR');
  }

}