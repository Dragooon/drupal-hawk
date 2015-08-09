<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Tests\HawkAuthTestTrait.
 */

namespace Drupal\hawk_auth\Tests;
use Dragooon\Hawk\Client\ClientBuilder;
use Dragooon\Hawk\Credentials\CredentialsInterface;
use Drupal\Core\Url;
use Drupal\hawk_auth\Entity\HawkCredentialInterface;

/**
 * Provides basic functions to test Hawk Authentication requests.
 */
trait HawkAuthTestTrait {

    /**
     * Retrieves a Drupal path or an absolute path using Hawk Credentials.
     *
     * @param \Drupal\Core\Url|string $path
     *   The path to send the request to.
     * @param \Drupal\hawk_auth\Entity\HawkCredentialInterface $credentials
     *   The credentials to authenticate the user as.
     * @param array $options
     *   (optional) Additional options for the user.
     *
     * @return string
     *   The retrieved HTML.
     */
    public function hawkAuthGet($path, HawkCredentialInterface $credentials, array $options = []) {
        return $this->druaplGet($path, $options, $this->getHawkAuthHeaders($path, $credentials));
    }

    /**
     * Returns headers for a hawk auth credential based on a request.
     *
     * @param \Drupal\Core\Url|string $path
     *   The path to send a request to
     * @param \Dragooon\Hawk\Credentials\CredentialsInterface $credentials
     *   The credentials to get the headers for.
     * @param string $method
     *   GET or POST.
     * @param array $options
     *   Additional Hawk options.
     *
     * @return array
     *   List of headers for this Hawk request
     */
    protected function getHawkAuthHeaders($path, CredentialsInterface $credentials, $method = 'GET', array $options = []) {
        $path = $path instanceof Url ? $path->toString() : $path;

        $client = ClientBuilder::create()->build();
        $request = $client->createRequest($credentials, $path, $method, $options);
        return [$request->header()->fieldName() . ': ' . $request->header()->fieldValue()];
    }
    
}