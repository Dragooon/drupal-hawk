<?php

/**
 * @file
 * Contains \Drupal\hawk_auth\Tests\HawkAuthTest.
 */

namespace Drupal\hawk_auth\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Url;

/**
 * Tests for basic hawk authentication.
 *
 * @group hawk_auth
 */
class HawkAuthTest extends WebTestBase {

    use HawkAuthTestTrait;

    /**
     * Modules installed for all tests.
     *
     * @var array
     */
    public static $modules = ['hawk_auth', 'hawk_route_tests'];

    /**
     * Test hawk auth authentication.
     */
    public function testHawkAuth() {
        $account = $this->drupalCreateUser();
        $credential = $this->getHawkCredentials($account);

        $url = Url::fromRoute('hawk_route_test.user');

        $this->hawkAuthGet($url, $credential);
        $this->assertText($account->getUsername(), 'Account name is displayed');
        $this->assertResponse('200', 'HTTP Response is okay');
        $this->curlClose();
        $this->assertFalse($this->drupalGetHeader('X-Drupal-Cache'));
        $this->assertIdentical(strpos($this->drupalGetHeader('Cache-Control'), 'public'), FALSE, 'Cache-Control is not set to public');

        $wrong_credential = clone $credential;
        $wrong_credential->setKeySecret('wrong_key');

        $this->hawkAuthGet($url, $wrong_credential);
        $this->assertNoText($account->getUsername(), 'Account name should not be displayed.');
        $this->assertResponse('403', 'HTTP Access is not granted');
        $this->curlClose();
    }
}
