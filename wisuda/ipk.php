<?php
    /*perintah koneksi ke db ditulis di sini */
    /* membuat awal dan header tabel*/

    echo "<table>";
    echo "<tr><th>No</th><th>Bidang</th><th>Jum Karyawan</th><th>Gol. Satu</th><th>Gol. Dua</th><th>Gol. Tiga</th></tr>";  
.
    /* nilai awal untuk nomor urut data*/

    $no = 1;
    $totalKaryawan = 0;
    $totalGol1 = 0;
    $totalGol2 = 0;
    $totalGol3 = 0;
    
    //embaca semua data bidang (analog mahasiswa)

    $query = "SELECT * FROM bidang";
    $hasil = mysql_query($query);

    while ($data = mysql_fetch_array($hasil))

    {
    // baca kode bidang (NIM)
    $kodeBidang = $data['kd_bidang'];
    // baca nama bidang (nama mahasiswa)
    $namaBidang = $data['nama_bidang'];
    // cari jumlah karyawan untuk setiap kode bidang
37.
$query2 = "SELECT count(*) as jum FROM karyawan WHERE kd_bidang = '$kodeBidang'";
38.
$hasil2 = mysql_query($query2);
39.
$data2 = mysql_fetch_array($hasil2);
40.
$jumlah = $data2['jum'];
41.
 
42.
// menjumlahkan setiap jumlah karyawan setiap bidang
43.
// untuk menghitung total seluruh karyawan
44.
$totalKaryawan += $jumlah;
45.
 
46.
// cari jumlah karyawan untuk setiap kode bidang bergolongan 1
47.
$query2 = "SELECT count(*) as jum1 FROM karyawan WHERE kd_bidang = '$kodeBidang' AND kd_gol = 1";
48.
$hasil2 = mysql_query($query2);
49.
$data2 = mysql_fetch_array($hasil2);
50.
$jumGol1 = $data2['jum1'];
51.
 
52.
// menjumlahkan setiap jumlah karyawan gol 1 di setiap bidang
53.
// untuk menghitung total seluruh karyawan bergolongan 1
54.
$totalGol1 += $jumGol1;
55.
 
56.
// cari jumlah karyawan untuk setiap kode bidang bergolongan 2
57.
$query2 = "SELECT count(*) as jum2 FROM karyawan WHERE kd_bidang = '$kodeBidang' AND kd_gol = 2";
58.
$hasil2 = mysql_query($query2);
59.
$data2 = mysql_fetch_array($hasil2);
60.
$jumGol2 = $data2['jum2'];
61.
 
62.
// menjumlahkan setiap jumlah karyawan gol 2 di setiap bidang
63.
// untuk menghitung total seluruh karyawan bergolongan 2
64.
$totalGol2 += $jumGol2;
65.
 
66.
// cari jumlah karyawan untuk setiap kode bidang bergolongan 3
67.
$query2 = "SELECT count(*) as jum3 FROM karyawan WHERE kd_bidang = '$kodeBidang' AND kd_gol = 3";
68.
$hasil2 = mysql_query($query2);
69.
$data2 = mysql_fetch_array($hasil2);
70.
$jumGol3 = $data2['jum3'];
71.
 
72.
// menjumlahkan setiap jumlah karyawan gol 3 di setiap bidang
73.
// untuk menghitung total seluruh karyawan bergolongan 3
74.
$totalGol3 += $jumGol3;
75.
 
76.
// tampilkan baris datanya untuk setiap bidang
77.
echo "<tr><td>".$no."</td><td>".$namaBidang."</td><td>".$jumlah."</td><td>".$jumGol1."</td><td>".$jumGol2."</td><td>".$jumGol3."</td></tr>";
78.
 
79.
// increment untuk nomor urut data
80.
$no++;
81.
}
echo "<tr><td colspan='2'>Jumlah</td><td>".$totalKaryawan."</td><td>".$totalGol1."</td><td>".$totalGol2."</td><td>".$totalGol3."</td></tr>";
85.
 
86.
// membuat akhir dari tabel
87.
echo "</table>";
88.
88.
 
89.
?>
?>