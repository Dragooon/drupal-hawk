<?php

namespace Drupal\Hawk\Authentication\Provider;

use Dragooon\Hawk\Server\Server;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Hawk implements AuthenticationProviderInterface {

  /**
   * Server interface for Hawk
   *
   * @var Server
   */
  protected $server;

  /**
   * @param Server $server
   */
  public function __construct(Server $server) {
    $this->server = $server;
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

  }

}