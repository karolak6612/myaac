<?php
/**
 * Logout Account
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Logout';

require __DIR__ . '/base.php';

if(!$logged) {
	if (isApiRequest()) {
		jsonResponse(['success' => true, 'message' => 'Not logged in.']);
	}
	return;
}

require SYSTEM . 'logout.php';

if (isApiRequest()) {
	jsonResponse(['success' => true, 'message' => 'Logged out successfully.']);
}

$twig->display('account.logout.html.twig');
