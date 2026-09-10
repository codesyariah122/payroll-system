<?php

return [
    'local_email_test' => [
        'enabled' => env('PAYROLL_LOCAL_EMAIL_TEST', false),
        'recipient' => env('PAYROLL_LOCAL_TEST_EMAIL', 'pujiermanto@gmail.com'),
    ],

    // Salinan internal untuk arsip HC. Secara default memakai mailbox pengirim
    // agar slip dan PDF juga dapat dicek melalui inbox mail server hosting.
    'audit_email' => env('PAYROLL_AUDIT_EMAIL', env('MAIL_FROM_ADDRESS')),
];
