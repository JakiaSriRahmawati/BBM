<?php
$bbm_prices = [
    "Pertamax" => 12500,
    "Pertalite" => 10000,
    "Dexlite" => 13000,
    "Solar" => 6000
];

function calculate_bbm($jenis_bbm, $uang_dibelikan, $total_uang, $harga_per_liter) {
    $liter_didapat = $uang_dibelikan / $harga_per_liter;
    $kembalian = $total_uang - $uang_dibelikan;
    return [$liter_didapat, $kembalian];
}

function main() {
    global $bbm_prices;

    echo "=== Daftar Harga BBM ===\n";
    foreach ($bbm_prices as $jenis => $harga) {
        echo "$jenis: Rp" . number_format($harga, 0, ',', '.') . " per liter\n";
    }
    echo "=========================\n";


    echo "Pilih jenis BBM (Pertamax, Pertalite, Dexlite, Solar): ";
    $jenis_bbm = trim(fgets(STDIN));

    if (!array_key_exists($jenis_bbm, $bbm_prices)) {
        echo "Jenis BBM tidak valid.\n";
        return;
    }

    echo "Masukkan total uang yang dibayarkan: ";
    $total_uang = (float) trim(fgets(STDIN));


    echo "Masukkan nominal uang yang dibelikan untuk BBM: ";
    $uang_dibelikan = (float) trim(fgets(STDIN));


    if ($uang_dibelikan > $total_uang) {
        echo "Uang yang dibelikan tidak mencukupi!!.\n";
        return;
    }

    $harga_per_liter = (float) $bbm_prices[$jenis_bbm];
    list($liter_didapat, $kembalian) = calculate_bbm($jenis_bbm, $uang_dibelikan, $total_uang, $harga_per_liter);

    echo "\nHasil Pembelian:\n";
    echo "Jumlah liter yang didapat: " . number_format($liter_didapat, 2, ',', '.') . " liter\n";
    echo "Kembalian: Rp" . number_format($kembalian, 2, ',', '.') . "\n";

    $result = [
        'Jenis_BBM' => $jenis_bbm,
        'Harga_per_liter' => number_format($harga_per_liter, 0, ',', '.'),
        'Uang_dibayarkan' => number_format($total_uang, 0, ',', '.'),
        'Uang_dibelikan_BBM' => number_format($uang_dibelikan, 0, ',', '.'),
        'Jumlah_BBM_didapat' => number_format($liter_didapat, 2, ',', '.'),
        'Kembalian' => number_format($kembalian, 2,)
    ];
    file_put_contents('hasil.json', json_encode($result, JSON_PRETTY_PRINT));
}

main();
