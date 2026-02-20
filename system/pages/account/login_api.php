<?php
defined('MYAAC') or die('Direct access not allowed!');

if (!isRequestMethod('post')) {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

require SYSTEM . 'pages/account/login.php';

if ($logged) {
    $response = [
        'logged' => true,
        'account' => [
            'id' => $account_logged->getId(),
            'name' => (USE_ACCOUNT_NAME ? $account_logged->getName() : (USE_ACCOUNT_NUMBER ? $account_logged->getNumber() : $account_logged->getId())),
            'email' => $account_logged->getEMail(),
            'created' => $account_logged->getCreated(),
            'prem_days' => $account_logged->getPremDays(),
            'is_premium' => $account_logged->isPremium(),
            'rlname' => $account_logged->getRLName(),
            'location' => $account_logged->getLocation(),
            'recovery_key_set' => !empty($account_logged->getCustomField('key')),
            'email_new_time' => $account_logged->getCustomField("email_new_time"),
            'email_new' => $account_logged->getCustomField("email_new"),
            'is_admin' => admin()
        ]
    ];
    jsonResponse($response);
} else {
     jsonResponse(['logged' => false, 'errors' => $errors ?? ['Login failed']], 401);
}
