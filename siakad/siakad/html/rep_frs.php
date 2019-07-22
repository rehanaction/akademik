<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getUIPath('form'));
	// variabel request
	if(Akademik::isMhs())
		$c_nim = Modul::getUserName();
	else
		$c_nim = CStr::removeSpecial($_REQUEST['npm']);
	
$p_tbwidth = 1000;
//####################
	$c_periode = Akademik::getPeriode();
	$c_periodespa=Akademik::getPeriodeSpa();
	
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$c_nim);

	if(substr($a_infomhs['periodemasuk'],0,4)==substr($c_periodespa,0,4))
		$c_periode=Akademik::getPeriodeSpa();
	
	$SQL	= "select a.thnkurikulum, a.kodeunit, a.kodemk, a.kelasmk, b.namamk from akademik.ak_krs a, akademik.ak_matakuliah b 
				where a.nim='$c_nim' and a.periode='$c_periode' and a.kodemk=b.kodemk and a.thnkurikulum=b.thnkurikulum";
				   
	$rsSQL	= $conn->Execute($SQL);
	while(!$rsSQL->EOF)
	{	
		$strSQL = 	"select akademik.f_cekkres('".$rsSQL->fields["thnkurikulum"]."','$c_periode','".$rsSQL->fields["kodeunit"]."',
					'".$rsSQL->fields["kodemk"]."','".$rsSQL->fields["kelasmk"]."','$c_nim');";
		$kres	=	$conn->GetOne($strSQL);
		
		if (!empty($kres)) 
		{
			$kres = $rsSQL->fields["namamk"]." kres dengan ".$kres;
			$count++;
		 
			$infokres .= '<b>XXX '. $kres .' XXX</b><br />';
		}
		$rsSQL->MoveNext();
	}
 	if(!empty($infokres))
		$infokres .= '<b>--- KRS HARAP DIPERBAIKI ---<b />';
 
//#####################


 // Data aktivitas perwalian mahasiswa
 $strPerwalian = "select statusmhs, frsterisi,frsdisetujui,prasyaratspp,t_updatetime from akademik.ak_perwalian where nim='$c_nim' and periode='$c_periode'";
 $rsPerwalian  = $conn->Execute($strPerwalian);
 if (!$rsPerwalian->EOF) {
 	$c_statuskuliah = $rsPerwalian->fields["statusmhs"];
 	$c_frsterisi = $rsPerwalian->fields["frsterisi"];
 	$c_frsdisetujui = $rsPerwalian->fields["frsdisetujui"];
	$c_prasyaratspp = $rsPerwalian->fields["prasyaratspp"];
	$c_t_updatetime = $rsPerwalian->fields["t_updatetime"];
 }
 else 
 {
	$c_statuskuliah = 'A';
	$c_frsterisi = 0;
	$c_frsdisetujui = 0;
	$c_prasyaratspp = 0;
 }

	
	/*$strSQL = "select * from akademik.r_frsnow where nim='$c_nim' and periode='$c_periode' order by kodemk,kelasmk ";
	$rs = $conn->GetArray($strSQL);
	
	$data_krs=array();
	foreach($rs as $krs){
		$data_krs[$krs['kodemk']]=array(
								'kodemk'=>$krs['kodemk'],
								'namamk'=>$krs['namamk'],
								'kelasmk'=>$krs['kelasmk'],
								'sks'=>$krs['sks'],
								'jadwal'=>$krs['jadwal']
								);
		
		
	}
	foreach($rs as $krs2){
		$data_krs[$krs2['kodemk']]['pengajar'][]=$krs2['namapengajar'];
	}*/
	$data_krs = mKRS::getDataPeriode($conn,$c_nim,$c_periode);
	
	/*
	if(!$rs->EOF)
	{
		$c_nip=$rs->fields["nip"];
		$c_namadosen=$rs->fields["namadosen"];
		$c_nama=$rs->fields["nama"];
		$c_kodeunit=$rs->fields["kodeunit"];
	}
	else
	{*/
	/*
		$strInfo = "select m.nim, m.nama, m.kodeunit, u.namaunit, m.nipdosenwali,m.semestermhs, akademik.f_namalengkap(d.gelardepan, d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadosen
					from akademik.ms_mahasiswa m left join sdm.ms_pegawai d on (m.nipdosenwali=d.idpegawai::text) 
					left join gate.ms_unit u on (m.kodeunit=u.kodeunit) where m.nim='$c_nim'";
		$rsInfo  = $conn->Execute($strInfo);
		if (!$rsInfo->EOF) {
			$c_nama = $rsInfo->fields["nama"];	
			$c_semestermhs = $rsInfo->fields["semestermhs"];	
			$c_kodeunit = $rsInfo->fields["kodeunit"];
			$c_namaunit = $rsInfo->fields["namaunit"];	
			$c_nip = $rsInfo->fields["nipdosenwali"];	
			$c_namadosen = CStr::cStrNull($rsInfo->fields["namadosen"]);	
			//$c_periode=$_SESSION["SIA_PERIODE"];
		}
		*/
	//}
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$c_nim);
	$a_jadwalpraktukum=mKelasPraktikum::getJadwalPrak($conn,$a_infomhs['kurikulum'],$c_periode,$a_infomhs['kodeunit']);	
	$a_ambilpraktikum=array();
	foreach($a_jadwalpraktukum as $keyprak=>$val_prak){
		foreach($val_prak as $row_prak){
			$p_key=$keyprak.'|'.$row_prak['kelompok'];
			$a_ambilpraktikum[$p_key]=$row_prak;
		}
	}
	
?>
<html>
<HEAD>
<title>SIAKAD : Kartu Rencana Studi Mahasiswa</title>

<style>
.normaloption { background-color:white; }
.alternateoption { background-color:#EDF1FE; }

.HeaderBG
{
	color: black;
	font-weight: bold;
	background-color: white;
}
</style>
<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 15px; font-weight: bold }
		.div_header { font-size: 15px }
		.div_headertext { font-size: 12px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 11px }
		.tb_data td { font-size: 20px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>

</head>
<div align="center">
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" onLoad="window.print();">

<?php
		include('inc_headerlap.php');
?>

<table width=800><tr>
  <td align=center><font size=2><b><u>KARTU RENCANA STUDI </u></b></font></td>
</tr><tr><td align=center><font size=2><b><?= Akademik::getNamaPeriode($c_periode) ?></b></font></td>
</tr></table>
<table width="<?=$p_tbwidth?>" cellpadding=0>
<tr>
    <td nowrap width="100"><font size="2"><strong>NIM</strong></font></td>
    <td nowrap><font size="2">: 
      <?= $a_infomhs['nim']; ?>
    </font></td>
    <td><font size="-1">&nbsp;</font></td>
    <td nowrap width="100"><font size="2"><strong>Dosen PA</strong></td>
	<td nowrap><font size="2">: <?php echo $a_infomhs['dosenwali']?></td>
</tr>
<tr>
    <td nowrap><font size="2"><strong>Nama</strong></font></td>
    <td nowrap><font size="2">: 
      <?= $a_infomhs['nama']; ?>
    </font></td>
	
</tr>
<tr>
    <td nowrap><font size="2"><strong>Program Studi</strong></font></td>
    <td nowrap><font size="2">:       
      <?= $a_infomhs['jurusan']; ?> 
    </font></td>
	
</tr>
<tr>
    <td nowrap><font size="2"><strong>Basis</strong></font></td>
    <td nowrap><font size="2">:       
      <?= $a_infomhs['namasistemkuliah']; ?> 
    </font></td>
	
</tr>
</table>
<br>
<table width="<?=$p_tbwidth?>" border=1 bordercolor="black" cellspacing=0 cellpadding=1 style="border-collapse:collapse;">
  <tr align="center" bgcolor="#330099" height=10> 
    <td class="HeaderBG" style="text-align: left;" width="250"><font size="-1">&nbsp;&nbsp;Mata Kuliah</font></td>
    <td class="HeaderBG" width="30"><font size="-1">SKS</font></td>
    <td class="HeaderBG" width="30"><font size="-1">Kelas</font></td>
    <td class="HeaderBG" width="150"><font size="-1" >Jadwal/Ruang</font></td>
    <td class="HeaderBG"  style="text-align: left;" width="300"><font size="-1">&nbsp;&nbsp;Dosen</font></td>
    <td class="HeaderBG" width="60"><font size="-1">Status</font></td>
  </tr>
  <?
 $count = 0;
 $jumlahsks=0;
 if (empty($data_krs)) 
   echo "<tr><td align=center colspan=7>KRS masih kosong</td></tr>";
 else
 
 foreach($data_krs as $row) {
	$count++;
	$jumlahsks+=$row['sks'];
	$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$c_periode.'|'.$row['kelasmk'];
	$a_dosen = mMengajar::getDosen($conn, Akademik::getPeriode(), $row['kodemk'], $row['kelasmk'],$row['kodeunit']);
	$key_ambilprak=$row['kodemk'].'|'.$row['kelasmk'].'|'.$row['kelompok_prak'];
	$datakelas=mKelas::getData($conn,$t_key);
	
	$a_jadwal = array();
	if(!empty($datakelas['nohari']))
		$a_jadwal[] = Date::indoDay($datakelas['nohari']).', '.CStr::formatJam($datakelas['jammulai']).' - '.CStr::formatJam($datakelas['jamselesai']).' <b>Ruang '.$datakelas['koderuang'].'</b>';
	if(!empty($datakelas['nohari2']))
		$a_jadwal[] = Date::indoDay($datakelas['nohari2']).', '.CStr::formatJam($datakelas['jammulai2']).' - '.CStr::formatJam($datakelas['jamselesai2']).' <b>Ruang '.$datakelas['koderuang2'].'</b>';
	if(!empty($datakelas['nohari3']))
		$a_jadwal[] = Date::indoDay($datakelas['nohari3']).', '.CStr::formatJam($datakelas['jammulai3']).' - '.CStr::formatJam($datakelas['jamselesai3']).' <b>Ruang '.$datakelas['koderuang3'].'</b>';
	if(!empty($datakelas['nohari4']))
		$a_jadwal[] =Date::indoDay($datakelas['nohari4']).', '.CStr::formatJam($datakelas['jammulai4']).' - '.CStr::formatJam($datakelas['jamselesai4']).' <b>Ruang '.$datakelas['koderuang4'].'</b>';
	//$pengajar=implode('<br>',$row['pengajar']);
	?>
  <tr height=10> 
    <td style="font-size: 12px;" style="text-align: left;"> 
      
      &nbsp;&nbsp;<?= $row['namamk'] ?> (<?= $row['kodemk'] ?>)
      </td>
     <td align="center" style="font-size: 12px;"> 

      <?= $row['sks'] ?>
</td>
    <td align="center" style="font-size: 12px;">

      <?= $row['kelasmk'] ?></td>
    <td align="left" style="font-size: 12px;">
	
      <center><?= implode('<br>',$a_jadwal) ?></center>
    </td>
    <td style="font-size: 12px;">&nbsp;&nbsp;<?php foreach ($a_dosen as $doseng) {
			echo $doseng['namapengajar'];
		} ?></td>
    <td><font size="-4" style="font-size: 12px;"><center><?= $row['isonline']?></center></font></td>
 </tr>
 <?
 	//$rs->MoveNext();
 } // end for
 ?>
  <tr height=10> 
    <td align="left" class="HeaderBG"><font size="-1">&nbsp;&nbsp;TOTAL SKS&nbsp;</font></td>
    <td align="center"> 
      <font size="-1">
      <?= $jumlahsks ?>
      </font></td>
  </tr>
</table>
<?= $infokres ?>
<table width="<?=$p_tbwidth-100?>"  border="0" cellspacing="0" cellpadding="0">
  <tr height="30" valign="middle">
    <td align="left"><font size="-1">&nbsp;</font></td>
    <td width="200" align="left"><font size="-1"><b><center>Bandung,</b> <?= CStr::formatDateInd(date('Y-m-d')) ?></center></font></td>
  </tr>
  <tr>
    <td align="right">
    <?= uForm::getImageMahasiswa($conn,$c_nim,$c_upload,"style='width:3cm;hight:4cm'") ?>
    </td>
    <td align="center">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td align="center"><p><font size="-1"><b>Tanda Tangan Ybs,</b></font></p>
			  <br><br>
			  <p><font size="-1"><u>(
				<?= strtoupper($a_infomhs['nama']) ?>
				)</u><br>
				<?= $a_infomhs['nim'] ?>
			  </font></p></td>
		  </tr>
		</table>
    </td>
  </tr>
</table>

</body>
</div>
</html>
