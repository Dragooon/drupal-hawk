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
   * Constructs a HawkAuth object.
   *
   * @param ServerInterface $server
   *   Server interface for hawk.
   * @param EntityManagerInterface $entity_manager
   *   Entity Manager.
   */
  public function __construct(ServerInterface $server, EntityManagerInterface $entity_manager) {
    $this->server = $server;
    $this->entityManager = $entity_manager;
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
      $credentials = $this->entityManager->getStorage('hawk_credential')->load($response->credentials()->id());
      return $credentials->getOwner();
    }
    catch (UnauthorizedException $e) {
      return NULL;
    }
  }

}
