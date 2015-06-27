<?php

namespace Drupal\Hawk\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the HawkCredential entity class.
 *
 * @ContentEntityType(
 *   id = "hawk_credential",
 *   label = @Translation("Hawk Credential"),
 *   handlers = {
 *     "access" = "Drupal\hawk\Entity\HawkCredentialAccessControl",
 *     "storage" = "Drupal\hawk\Entity\HawkCredentialStorage",
 *     "storage_schema" = "Drupal\hawk\Entity\HawkCredentialStorageSchema",
 *     "form" = {
 *        "delete" = "Drupal\hawk\Form\HawkDeleteCredential",
 *     },
 *   },
 *   base_table = "hawk_credentials",
 *   entity_keys = {
 *     "id" = "cid"
 *   },
 * )
 */
class HawkCredential extends ContentEntityBase implements HawkCredentialInterface {

  /**
   * @return int
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * @param int
   * @return $this
   */
  public function setOwnerId($id) {
    $this->set('uid', $id);
    return $this;
  }

  /**
   * @return UserInterface
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * @param UserInterface $account
   * @return $this
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * @return string
   */
  public function getKeySecret() {
    return $this->get('key_secret')->value;
  }

  /**
   * @param string
   * @return $this
   */
  public function setKeySecret($keySecret) {
    $this->set('key_secret', $keySecret);
    return $this;
  }

  /**
   * @return string
   */
  public function getKeyAlgo() {
    return $this->get('key_algo')->value;
  }

  /**
   * @param string
   * @return $this
   */
  public function setKeyAlgo($keyAlgo) {
    $this->set('key_algo', $keyAlgo);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = [];

    $fields['cid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Credential ID'))
      ->setDescription(t('The credential ID'))
      ->setReadOnly(true)
      ->setSetting('unsigned', true);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('ID of the owner for this credential'))
      ->setSetting('target_type', 'user');

    $fields['key_secret'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Key Secret'))
      ->setDescription(t('Secret for this credential'));

    $fields['key_algo'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Key Algorithm'))
      ->setDescription(t('Encryption algorithm used by requests for this key'));

    return $fields;
  }

}
