<?php

return [
    'start_date' => now()->toDateString(),
    'end_date' => now()->subDays(10)->toDateString(),
    'base_client_id' => '',
    'reg_client_id' => '',
    'branch_id' => '',
];
