<?php

namespace Drupal\hawk\PageCache;

// hawk.module isn't loaded at this point of execution
if (file_exists(dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php')) {
  require_once(dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');
}

use Dragooon\Hawk\Server\ServerInterface;
use Drupal\Core\PageCache\RequestPolicyInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Cache policy for requests served through Hawk authentication
 *
 * Disable any page caching for requests containing Hawk authorization header to avoid
 * leaking of cached pages to anonymous users or other users who may not have the
 * required permissions. Otherwise it would cache the URL and serve the same page back
 * to other users.
 */
class DisallowHawkRequests implements RequestPolicyInterface {

  /**
   * @var \Dragooon\Hawk\Server\ServerInterface
   */
  protected $server;

  /**
   * @param \Dragooon\Hawk\Server\ServerInterface $server
   */
  public function __construct(ServerInterface $server) {
    $this->server = $server;
  }

  /**
   * {@inheritDoc}
   */
  public function check(Request $request) {
    if ($this->server->checkRequestForHawk($request->headers->get('authorization'))) {
      return self::DENY;
    }
  }

}
