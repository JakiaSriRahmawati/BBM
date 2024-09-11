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

function main() {
    global $bbm_prices;

    echo "==== Daftar Harga BBM ====\n";
    foreach ($bbm_prices as $jenis => $harga) {
        echo "$jenis: Rp$harga per liter\n";
    }
    echo "=========================\n";

    echo "Pilih jenis BBM (Pertamax, Pertalite, Dexlite, Solar): ";
    $jenis_bbm = trim(fgets(STDIN));

    if (!array_key_exists($jenis_bbm, $bbm_prices)) {
        echo "Jenis BBM tidak valid.\n";
        return;
    }

    echo "Masukkan nominal uang yang dibelikan untuk BBM: ";
    $uang_dibelikan = trim(fgets(STDIN));
    $uang_dibelikan = parse_input($uang_dibelikan);

    echo "Masukkan total uang yang dibayarkan: ";
    $total_uang = trim(fgets(STDIN));
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
        'Jenis_BBM' => $jenis_bbm,
        'Harga_per_liter' => format_currency($bbm_prices[$jenis_bbm]),
        'Uang_dibayarkan' => format_currency($total_uang),
        'Uang_dibelikan_BBM' => format_currency($uang_dibelikan),
        'Jumlah_BBM_didapat' => number_format($liter_didapat, 2),
        'Kembalian' => format_currency($kembalian)
    ];

    $yaml_output = "---------------------\n";
    foreach ($output as $key => $value) {
        $yaml_output .= "$key: $value\n";
    }

    file_put_contents('hasil.yaml', $yaml_output);
    echo "\nData pembelian BBM berhasil disimpan ke hasil.yaml\n";
}

main();
?>
