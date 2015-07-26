<?php

/**
 * @file
 * Contains \Drupal\hawk_auth_qr\Response\HawkAuthQrImageResponse.
 */

namespace Drupal\hawk_auth_qr\Resonse;

use Drupal\hawk_auth\Entity\HawkCredentialInterface;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for Hawk Auth QR, primary purpose is to generate and display
 * QR codes for individual credentials.
 */
class HawkAuthQrImageResponse extends Response {

  /**
   * The credential we are generating a response for.
   *
   * @var HawkCredentialInterface
   */
  protected $credential;

  /**
   * Qr library.
   *
   * @var QrCode
   */
  protected $qrCode;

  /**
   * Constructs this class' object.
   *
   * @param HawkCredentialInterface $credential
   *   The credential to generate a response for
   * @param QrCode $qr_code
   *   Library to generate QR Codes.
   *
   * {@inheritdoc}
   */
  public function __construct(HawkCredentialInterface $credential, QrCode $qr_code, $content = '', $status = 200, $headers = []) {
    parent::__construct($content, $status, $headers);

    $this->credential = $credential;
    $this->qrCode = $qr_code;
  }
}
