<?php

return [
    'show_warnings'   => false,
    'orientation'     => 'portrait',
    'defines'         => [
        'DOMPDF_ENABLE_AUTOLOAD'     => false,
        'DOMPDF_ENABLE_REMOTE'       => false,
        'DOMPDF_ENABLE_CSS_FLOAT'    => true,
        'DOMPDF_ENABLE_HTML5PARSER'  => true,
        // Semua path pakai storage - tidak bergantung pada public_path
        'DOMPDF_FONT_DIR'            => storage_path('fonts/'),
        'DOMPDF_FONT_CACHE'          => storage_path('fonts/'),
        'DOMPDF_TEMP_DIR'            => sys_get_temp_dir(),
        // chroot ke base_path - bukan public_path
        'DOMPDF_CHROOT'              => base_path(),
        'DOMPDF_DEFAULT_PAPER_SIZE'  => 'a4',
        'DOMPDF_DEFAULT_FONT'        => 'DejaVu Sans',
        'DOMPDF_DPI'                 => 96,
        'DOMPDF_ENABLE_PHP'          => false,
        'DOMPDF_ENABLE_JAVASCRIPT'   => false,
        'DOMPDF_ENABLE_FONT_SUBSETTING' => true,
        'DOMPDF_PDF_BACKEND'         => 'CPDF',
    ],
];
