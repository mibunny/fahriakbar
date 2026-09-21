<?php
$kriteria = [
    'pendapatan' => ['bobot' => 0.27, 'jenis' => 'cost', 'nama' => 'Pendapatan Bulanan'],
    'pekerjaan'  => ['bobot' => 0.27, 'jenis' => 'cost', 'nama' => 'Pekerjaan'],
    'tanggungan' => ['bobot' => 0.25, 'jenis' => 'benefit', 'nama' => 'Jumlah Tanggungan Keluarga'],
    'rumah'      => ['bobot' => 0.22, 'jenis' => 'cost', 'nama' => 'Status Kepemilikan Rumah']
];

$opsi = [
    'pendapatan' => [
        1 => 'Di bawah 2 juta', 
        2 => '2 – 2,49 juta', 
        3 => '2,5 – 2,99 juta', 
        4 => '3 – 3,49 juta', 
        5 => '3,5 juta ke atas'
    ],
    'pekerjaan' => [
        1 => 'Tidak bekerja', 
        2 => 'Pekerjaan serabutan', 
        3 => 'Buruh harian lepas', 
        4 => 'Buruh tetap', 
        5 => 'PNS'
    ],
    'tanggungan' => [
        1 => '0 tanggungan', 
        2 => '1 tanggungan', 
        3 => '2 tanggungan', 
        4 => '3 tanggungan', 
        5 => '4 atau lebih tanggungan'
    ],
    'rumah' => [
        1 => 'Menumpang di tempat umum/tunawisma',
        2 => 'Menumpang dengan keluarga', 
        3 => 'Ngontrak jangka pendek', 
        4 => 'Ngontrak jangka panjang', 
        5 => 'Milik pribadi'
    ]
];
?>
