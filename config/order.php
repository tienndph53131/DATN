<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order status transition rules
    |--------------------------------------------------------------------------
    |
    | Define allowed next statuses by current status name. Use status_name
    | values stored in DB. If a current status is not listed, any transition
    | will be allowed by default.
    |
    */
    'status_transitions' => [
        // From => [allowed next statuses]
        'Chưa xác nhận' => [
            'Đã thanh toán, chờ xác nhận',
            'Đã xác nhận',
            'Hủy đơn hàng',
        ],
        'Đã thanh toán, chờ xác nhận' => [
            'Đã xác nhận',
            'Hủy đơn hàng',
        ],
        'Đã xác nhận' => [
            'Đang chuẩn bị hàng',
            'Đang giao',
            'Hủy đơn hàng',
        ],
        'Đang chuẩn bị hàng' => [
            'Đang giao',
            'Hủy đơn hàng',
        ],
        'Đang giao' => [
            'Đã giao',
            'Hủy đơn hàng',
        ],
        'Đã giao' => [
            'Đã nhận',
            'Hoàn hàng',
        ],
        'Đã nhận' => [
            'Thành công',
        ],
        // Terminal statuses: no further transitions by default
        'Thành công' => [],
        'Hoàn hàng' => [],
        'Hủy đơn hàng' => [],
    ],
];
