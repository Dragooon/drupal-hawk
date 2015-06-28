<?php

namespace Drupal\hawk_auth\Authentication\Provider;

use Dragooon\Hawk\Server\ServerInterface;
use Dragooon\Hawk\Server\UnauthorizedException;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Hawk implements AuthenticationProviderInterface {

  /**
   * Server interface for Hawk
   *
   * @var ServerInterface
   */
  protected $server;

  /**
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * @param ServerInterface $server
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(ServerInterface $server, EntityManagerInterface $entityManager) {
    $this->server = $server;
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritDoc}
   */
  public function applies(Request $request) {
    return $this->server->checkRequestForHawk($request->headers->get('authorization'));
  }

  /**
   * {@inheritDoc}
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
      return null;
    }
  }

}