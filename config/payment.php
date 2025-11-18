<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment status CSS classes
    |--------------------------------------------------------------------------
    |
    | Map payment status names (as stored in DB) to bootstrap badge classes.
    | This avoids hard-coding numeric IDs in views and centralizes styling.
    |
    */
    'status_classes' => [
        'Chưa thanh toán' => 'badge bg-warning text-dark',
        'Đã thanh toán' => 'badge bg-success',
        // additional statuses can be added here
        'Hoàn tiền' => 'badge bg-danger',
    ],
];
