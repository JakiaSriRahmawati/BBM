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

function parse_input($input) {
    $input = preg_replace('/[^0-9]/', '', $input);
    return (int) $input;
}

function format_currency($amount) {
    return 'Rp' . number_format($amount, 0, ',', '.');
}

function validate_numeric($input) {
    return preg_match('/^\d+$/', $input);
}

function main() {
    global $bbm_prices;

    echo "==== Daftar Harga BBM ====\n";
    foreach ($bbm_prices as $jenis => $harga) {
        printf("%-10s : %s per liter\n", $jenis, format_currency($harga));
    }
    echo "==========================\n";

    echo "Pilih jenis BBM (Pertamax, Pertalite, Dexlite, Solar): ";
    $jenis_bbm = trim(fgets(STDIN));

    if (!array_key_exists($jenis_bbm, $bbm_prices)) {
        echo "Jenis BBM tidak valid.\n";
        return;
    }

    echo "Masukkan nominal uang yang dibelikan: ";
    $uang_dibelikan = trim(fgets(STDIN));

    if (!validate_numeric(preg_replace('/[^0-9]/', '', $uang_dibelikan))) {
        echo "Nominal uang yang diinputkan harus berupa angka!.\n";
        return;
    }

    $uang_dibelikan = parse_input($uang_dibelikan);

    echo "Masukkan total uang yang dibayarkan: ";
    $total_uang = trim(fgets(STDIN));

    if (!validate_numeric(preg_replace('/[^0-9]/', '', $total_uang))) {
        echo "Nominal uang yang diinputkan harus berupa angka!.\n";
        return;
    }

    $total_uang = parse_input($total_uang);

    if ($uang_dibelikan > $total_uang) {
        echo "Uang yang anda berikan tidak mencukupi pembelian!!.\n";
        return;
    }

    list($liter_didapat, $kembalian) = calculate_bbm(
        $jenis_bbm,
        $uang_dibelikan,
        $total_uang,
        $bbm_prices[$jenis_bbm]
    );

    $output = [
        'Jenis BBM' => $jenis_bbm,
        'Harga Per Liter' => format_currency($bbm_prices[$jenis_bbm]),
        'Uang Dibayarkan' => format_currency($total_uang),
        'Uang Dibelikan BBM' => format_currency($uang_dibelikan),
        'Jumlah BBM Didapat' => number_format($liter_didapat, 2) . " liter",
        'Kembalian' => format_currency($kembalian)
    ];

    echo "\n==== Data Pembelian BBM ====\n";
    foreach ($output as $key => $value) {
        printf("%-25s: %s\n", $key, $value);
    }

    $yaml_output = "---------------------\n";
    foreach ($output as $key => $value) {
        $yaml_output .= sprintf("%-25s: %s\n", $key, $value);
    }

    file_put_contents('hasil.yaml', $yaml_output);
    echo "\nData pembelian BBM berhasil disimpan ke hasil.yaml\n";
}

main();
