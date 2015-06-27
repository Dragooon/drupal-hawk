<?php

namespace Drupal\hawk\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hawk\Entity\HawkCredentialStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HawkDeleteCredential extends ConfirmFormBase {

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
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'hawk_delete_credential_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete this Hawk credential?');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action cannot be undone. It will disable all clients
      relying on this credential.');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->url('hawk.user_credential', ['user' => $this->currentUser()->id()]);
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = null) {
    $form = parent::buildForm($form, $form_state);

    $form['cid'] = array(
      '#type' => 'hidden',
      '#value' => $cid,
    );

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\hawk\Entity\HawkCredentialInterface $credential */
    $credential = $this->hawkCredentialStorage->load($form_state->getValue('cid'));

    if (empty($credential) || $credential->getOwnerId() != $this->currentUser()->id()) {
      throw new AccessDeniedHttpException('Invalid credential owner', null, 403);
    }

    $credential->delete();

    $form_state->setRedirect('hawk.user_credential', ['user' => $this->currentUser()->id()]);
  }

}
