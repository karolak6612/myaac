<?php
defined('MYAAC') or die('Direct access not allowed!');

if (!isRequestMethod('post')) {
    jsonResponse(['errors' => ['Method not allowed']], 405);
}

require SYSTEM . 'pages/account/login.php';

if ($logged) {
    try {
        $account_rlname = $account_logged->getRLName();
        $account_location = $account_logged->getLocation();
        $recovery_key = $account_logged->getCustomField('key');
        $email_new_time = (int)$account_logged->getCustomField("email_new_time");
        $email_new = ($email_new_time > 1) ? $account_logged->getCustomField("email_new") : '';

        $response = [
            'logged' => true,
            'account' => [
                'id' => $account_logged->getId(),
                'name' => (USE_ACCOUNT_NAME ? $account_logged->getName() : (USE_ACCOUNT_NUMBER ? $account_logged->getNumber() : $account_logged->getId())),
                'email' => $account_logged->getEMail(),
                'created' => $account_logged->getCreated(),
                'prem_days' => $account_logged->getPremDays(),
                'is_premium' => $account_logged->isPremium(),
                'is_admin' => admin(),
                'rlname' => $account_rlname,
                'location' => $account_location,
                'recovery_key_set' => !empty($recovery_key),
                'email_new_time' => $email_new_time,
                'email_new' => $email_new,
            ]
        ];
    } catch (E_OTS_NotLoaded $e) {
        jsonResponse([
            'logged' => false,
            'error' => 'Failed to load account.',
        ], 500);
    }
    jsonResponse($response);
} else {
     jsonResponse(['logged' => false, 'errors' => $errors ?? ['Login failed']], 401);
}
