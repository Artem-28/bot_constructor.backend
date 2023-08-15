<?php
return [
    'request' => [
        'base' => 'error.request.bad'
    ],
    'tariff' => [
        'activate' => 'error.tariff.activate',
        'validation' => [
            'admin' => 'error.tariff.validation.admin',
            'respondent' => 'error.tariff.validation.respondent',
            'storage' => 'error.tariff.validation.storage',
            'period' => 'error.tariff.validation.period'
        ],
    ],
    'sale' => [
        'validation' => [
            'base' => 'error.sale.validation.base'
        ]
    ],
    'payment' => [
        'set_transaction' => 'error.payment.set_transaction',
        'update_transaction' => 'error.payment.update_transaction',
        'invalid_request' => 'error.payment.invalid_request',
        'invalid_notification' => 'error.payment.invalid_notification',
        'invalid_payment' => 'error.payment.invalid_payment',
        'invalid_metadata' => 'error.payment.invalid_metadata'
    ],
    'project' => [
        'update' => 'error.project.update'
    ]
];
