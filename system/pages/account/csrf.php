<?php
/**
 * CSRF Token
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 * @link      https://my-aac.org
 */

defined('MYAAC') or die('Direct access not allowed!');

if (isApiRequest()) {
    jsonResponse(['csrf_token' => csrfToken()]);
}
