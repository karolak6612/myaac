<?php
defined('MYAAC') or die('Direct access not allowed!');

if (!isRequestMethod('post')) {
    jsonResponse(['errors' => ['Method not allowed']], 405);
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
            'is_admin' => admin(),
        ]
    ];
    jsonResponse($response);
} else {
     jsonResponse(['logged' => false, 'errors' => $errors ?? ['Login failed']], 401);
}
