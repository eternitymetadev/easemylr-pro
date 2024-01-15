<?php

return [
    'start_date' => now()->firstOfMonth()->toDateString(), // Current month's first day
    'end_date' => now()->toDateString(), // Current date

    'last_month_start_date' => now()->subMonth()->firstOfMonth()->toDateString(),
    'last_month_end_date' => now()->subMonth()->lastOfMonth()->toDateString(),

    'third_last_month_start_date' => now()->subMonths(2)->firstOfMonth()->toDateString(),
    'third_last_month_end_date' => now()->subMonths(2)->lastOfMonth()->toDateString(),

    'base_client_id' => '',
    'reg_client_id' => '',
    'branch_id' => '',
];
