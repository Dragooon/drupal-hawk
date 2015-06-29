<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Form\HawkAddCredentialForm.
 */

namespace Drupal\hawk_auth\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hawk_auth\Entity\HawkCredentialStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for adding a hawk credential.
 */
class HawkAddCredentialForm extends FormBase {

  /**
   * Hawk credential entity's storage.
   *
   * @var \Drupal\hawk_auth\Entity\HawkCredentialStorageInterface
   */
  protected $hawkCredentialStorage;

  /**
   * Constructs Hawk controller object.
   *
   * @param HawkCredentialStorageInterface $hawk_credential_storage
   *   Storage for Hawk Credentials' entities.
   */
  public function __construct(HawkCredentialStorageInterface $hawk_credential_storage) {
    $this->hawkCredentialStorage = $hawk_credential_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static(
      $entity_manager->getStorage('hawk_credential')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hawk_add_credential_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $algos = array();
    foreach (hash_algos() as $algo) {
      $algos[$algo] = $this->t($algo);
    }

    $form['key_algo'] = [
      '#type' => 'select',
      '#title' => $this->t('Key Algorithm'),
      '#default_value' => 'sha256',
      '#options' => $algos,
    ];

    $form['save'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $algo = $form_state->getValue('key_algo');
    if (!in_array($algo, hash_algos())) {
      $form_state->setErrorByName('key_alog', $this->t('Selected algorithm is not valid'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $new_credential = $this->hawkCredentialStorage->create([
      'key_secret' => user_password(32),
      'key_algo' => $form_state->getValue('key_algo'),
      'uid' => $this->currentUser()->id(),
    ]);
    $new_credential->save();

    $form_state->setRedirect('hawk_auth.user_credential', ['user' => $this->currentUser()->id()]);
  }

}
