<?php
if (!empty($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'local') {
    return [
        'SITE_NAME' => 'Oeno',
        'ADMIN_EMAIL' => 'sunilkumar@1wayit.com',
        'SERVER_URL' => 'http://www.admin.easemrlr.com',
        'PER_PAGE' => 50,
    ];
} else {
    return [
        'SITE_NAME' => 'Oeno',
        'ADMIN_EMAIL' => 'sunilkumar@1wayit.com',
        'SERVER_URL' => 'http://easemylr.com',
        'PER_PAGE' => 50,
        
    ];
}