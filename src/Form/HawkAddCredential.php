<?php

namespace Drupal\hawk\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hawk\HawkCredentialStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HawkAddCredential extends FormBase {

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
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'hawk_add_credential_form';
  }


  /**
   * {@inheritDoc}
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

    return parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $newCredential = $this->hawkCredentialStorage->create([
      'key_secret' => user_password(32),
      'key_algo' => $form_state->getValue('key_algo'),
      'uid' => $this->currentUser()->id(),
    ]);
    $newCredential->save();

    $form_state->setRedirect('hawk.user_credential', ['user' => $this->currentUser()->id()]);
  }

}
