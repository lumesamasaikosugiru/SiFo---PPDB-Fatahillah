<?php
$target = '/home/u306985438/domains/ppdbfatahillah.my.id/storage/app/public';
$link   = '/home/u306985438/domains/ppdbfatahillah.my.id/public_html/storage';
if (symlink($target, $link)) {
    echo "Symlink berhasil dibuat";
} else {
    echo "Gagal buat symlink";
}