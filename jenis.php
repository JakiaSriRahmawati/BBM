
<?php
// Konstanta untuk Pajak Pertambahan Nilai (PPN)
const PPN_RATE = 11;

$bbm_prices = [
    "Pertamax" => 12500,
    "Pertalite" => 10000,
    "Dexlite" => 13000,
    "Solar" => 6000
];

function calculate_bbm($jenis_bbm, $uang_dibelikan, $total_uang, $harga_per_liter, $diskon = 0) {
    // Menghitung harga setelah diskon
    $harga_setelah_diskon = $harga_per_liter - ($harga_per_liter * ($diskon / 100));
    // Menambahkan PPN 11% setelah diskon
    $harga_setelah_ppn = $harga_setelah_diskon + ($harga_setelah_diskon * (PPN_RATE / 100));
    $liter_didapat = $uang_dibelikan / $harga_setelah_ppn;
    $kembalian = $total_uang - $uang_dibelikan;
    return [$liter_didapat, $kembalian, $harga_setelah_diskon, $harga_setelah_ppn];
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

function validate_discount_format($input) {
    return preg_match('/^\d+%$/', $input);
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

    // Diskon (optional)
    echo "Masukkan diskon (misalnya 10% atau kosongkan saja jika tidak ada): ";
    $diskon_input = trim(fgets(STDIN));

    if ($diskon_input === '') {
        $diskon = 0; 
    } elseif (!validate_discount_format($diskon_input)) {
        echo "Format diskon tidak valid! Gunakan format seperti 10%.\n";
        return;
    } else {
        $diskon = (int) rtrim($diskon_input, '%');
    }

    if ($diskon < 0) {
        echo "Diskon tidak boleh negatif!.\n";
        return;
    }
    if ($diskon > 100) {
        echo "Diskon tidak boleh lebih dari 100%!.\n";
        return;
    }

    list($liter_didapat, $kembalian, $harga_setelah_diskon, $harga_setelah_ppn) = calculate_bbm(
        $jenis_bbm,
        $uang_dibelikan,
        $total_uang,
        $bbm_prices[$jenis_bbm],
        $diskon
    );

    $output = [
        'Jenis BBM' => $jenis_bbm,
        'Harga Per Liter' => format_currency($bbm_prices[$jenis_bbm]),
        'Diskon' => $diskon . "%",
        'Harga Setelah Diskon' => format_currency($harga_setelah_diskon),
        'PPN (' . PPN_RATE . '%)' => format_currency($harga_setelah_diskon * (PPN_RATE / 100)),
        'Harga Setelah PPN' => format_currency($harga_setelah_ppn),
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
