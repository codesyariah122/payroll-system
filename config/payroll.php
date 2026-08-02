<?php

return [
    'local_email_test' => [
        'enabled' => env('PAYROLL_LOCAL_EMAIL_TEST', false),
        'recipient' => env('PAYROLL_LOCAL_TEST_EMAIL', 'pujiermanto@gmail.com'),
    ],
];
