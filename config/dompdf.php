<?php

return [
    'show_warnings' => false,
    'public_path'   => null,
    'convert_entities' => true,

    'font_family' => [
        'SarabunLocal' => [
            'R' => 'Sarabun-Regular.ttf',
            'B' => 'Sarabun-Bold.ttf',
        ],
        'NotoSansThai' => [
            'R' => 'NotoSansThai-Regular.ttf',
            'B' => 'NotoSansThai-Bold.ttf',
        ],
    ],

    'options' => [
        'font_dir'   => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),
        // Prefer NotoSansThai if available; falls back to SarabunLocal via view logic
        'default_font' => 'NotoSansThai',
        'enable_font_subsetting' => true,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ],
];
