<?php

 //DB Connection----------------------------------------------------------------------------
$DBServer = '172.16.88.21'; // server name or IP address
$DBUser = 'postgres';
$DBPass = 'sembarang';
$DBName = 'akademik';
$dsn = "pgsql:host=$DBServer;port=5432;dbname=$DBName;user=$DBUser;password=$DBPass";
$conn = new PDO($dsn);
$schema = $conn->query('SET search_path TO akademik');
$periode = '20172';

//-------------------------------------------------------------------------------------------------
if(!empty($_GET['kodemk'])){
   $kelasmk = $_GET['kelasmk'];
   $kodemk = $_GET['kodemk'];
   $pertemuan = $_GET['pertemuan'];
   $exec = $conn->query("SELECT * FROM ak_absensikuliah WHERE kodemk = '$kodemk' AND kelasmk = '$kelasmk' AND perkuliahanke = '$pertemuan' AND periode = '$periode' LIMIT 1");
   if($exec->rowCount() > 0){
header('Content-Type: application/json');
  echo json_encode($exec->fetch(PDO::FETCH_ASSOC));
}
   else
  echo "tidak ada";
}
if(isset($_POST['aksi']) && $_POST['aksi'] == 'datadiri'){
   $nim = $_POST['namemhs'];
   $exec = $conn->query("SELECT * FROM ms_mahasiswa WHERE nim='$nim' LIMIT 1");
   $data = $exec->fetch(PDO::FETCH_ASSOC);
   $kodeunit = $data['kodeunit'];
   $exec = $conn->query("SELECT * FROM ak_prodi WHERE kodeunit='$kodeunit' LIMIT 1");
   $dataprodi = $exec->fetch(PDO::FETCH_ASSOC);
   $data['jurusan'] = $dataprodi['nama_program_studi'];
   $kodefak=substr($dataprodi['kodeunit'],0,-2);
    $kodefak=$kodefak."00";
   $exec=$conn->query("SELECT * FROM vp_fakultas WHERE kodeunit='$kodefak' LIMIT 1");
   $dataprodi = $exec->fetch(PDO::FETCH_ASSOC);
  $data['fakultas']=$dataprodi['namaunit'];
header('Content-Type: application/json');
echo json_encode($data);
}
if(isset($_POST['aksi']) && $_POST['aksi'] == 'jadwal'){
$hari = date('N');
$nim=$_POST['namemhs'];
$jadwalup = array();
$exec = $conn->query("select k.kodemk, m.namamk, k.nohari, k.koderuang, k.jammulai, k.jamselesai,
					k.nohari2, k.koderuang2, k.jammulai2, k.jamselesai2 from ak_kelas k
					join ak_matakuliah m using (thnkurikulum,kodemk)
					join ak_krs c using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					where k.periode = '20172' and c.nim = '$nim'
					and (k.nohari = '$hari' or k.nohari2 = '$hari') order by k.kodemk");


while($hasil = $exec->fetch(PDO::FETCH_ASSOC)){
$jadwalup[] = $hasil;
}

header('Content-Type: application/json');
echo json_encode($jadwalup);
}
if(isset($_POST['aksi']) && $_POST['aksi'] == 'absen'){
$hari = date('N');
$nim=$_POST['namemhs'];
$absen = array();
$exec = $conn->query("select thnkurikulum,periode,nim,kodeunit, kodemk, namamk, kelasmk, totalabsenmhs, totalabsenkelas,jeniskuliah,kelompok_prak,kelompok from r_absenmhs
					where nim = '$nim' and periode = '$periode' order by kodemk, kelasmk");


while($hasil = $exec->fetch(PDO::FETCH_ASSOC)){
$absen[] = $hasil;
}

header('Content-Type: application/json');
echo json_encode($absen);

}

if(isset($_POST['aksi']) && $_POST['aksi'] == 'nilai'){
$nim=$_POST['namemhs'];
$nilai = array();
$nilaidet = array();
$exec = $conn->query("select k.thnkurikulum, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, k.nhuruf, k.nangka, k.dipakai, k.nilaimasuk, k.lulus,k.kelompok_prak
					from ak_krs k join ak_matakuliah m using (thnkurikulum,kodemk)
					where k.nim = '$nim' and k.periode = '$periode' order by m.namamk, k.kelasmk");


while($hasil = $exec->fetch(PDO::FETCH_ASSOC)){
$nilai[] = $hasil;
}
$exec2= $conn->query("select k.kodemk, m.namamk,k.kelasmk,u.idunsurnilai,u.namaunsurnilai,u.prosentasenilai,un.nilaiunsur,k.nnumerik
					from akademik.ak_unsurpenilaian u 
					join akademik.ak_krs k using(periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					join akademik.ak_unsurnilaikelas un using (periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, idunsurnilai)
					left join akademik.ak_kurikulum m using (thnkurikulum, kodeunit, kodemk)
					where k.nim = '$nim' and k.periode = '$periode' order by k.kodemk");


while($hasil2 = $exec2->fetch(PDO::FETCH_ASSOC)){
$nilaidet[] = $hasil2;
}
header('Content-Type: application/json');
echo json_encode(array($nilai,$nilaidet));
}
?>