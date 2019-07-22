<? 
	session_start();
	
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// pengecekan tipe session user
	$a_auth = Helper::checkRoleAuth($conng,false);
	
	// otorisasi user
	$c_readlist = $a_auth['canlist'];
	$c_add = $a_auth['cancreate'];
	$c_edit = $a_auth['canedit'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];

	include("function.inc.php");
	$rs_setting = $conn->GetRow("select periodesekarang,thnkurikulum from ms_setting limit 1");
		
	//$conn->debug=true;
	$message = '';
	if($_POST['tahun'] != '')
		$r_thn_pelaporan = $_POST['tahun'];
	else
		$r_thn_pelaporan = date('Y');
	
	if($_POST['periode'] != '')
		$r_periode = $_POST['periode'];
	else
		$r_periode = '20131';
	
	if($_POST['thnkurikulum'] != '')
		$r_thn_kurikulum = $_POST['thnkurikulum'];
	else{
		$r_thn_kurikulum = $rs_setting['thnkurikulum'];
	}	
		
	if($_REQUEST["kodeunit"]){
		$c_kodeprodi = $_REQUEST["kodeunit"];
	}
	
	if($_REQUEST["periode"]){
		$c_periode = $_REQUEST["periode"];
	}else{
		$c_periode = $rs_setting['periodesekarang'];
	}
		
	if (isset($_REQUEST["act"]))
  	{
		$r_act = $_REQUEST["act"];
		$r_rule = explode(':',$_REQUEST["rule"]);
		$r_unit = $r_rule[1];
		$pathaddress = 'epsbed_file/';
		if ($r_act=="export" and $c_edit) 
		{
			if ($r_rule[0] == 'e_msmhs')			
				include("eps_msmhs.php");	
			else if ($r_rule[0] == 'e_tbkmk')
				include("eps_tbkmk.php");
			else if ($r_rule[0] == 'e_trakd')
				include("eps_trakd.php");
			else if ($r_rule[0] == 'e_trakm')
				include("eps_trakm.php");
			else if ($r_rule[0] == 'e_trlsm')
				include("eps_trlsm.php");
			else if ($r_rule[0] == 'e_trnlm')
				include("eps_trnlm.php");
			//ari
			else if ($r_rule[0] == 'e_msdos')
				include("eps_msdos.php");
			else if ($r_rule[0] == 'e_trkap')
				include("eps_trkap.php");
			else if ($r_rule[0] == 'e_mspst')
				include("eps_mspst.php");
			else if ($r_rule[0] == 'e_trlsd')
				include("eps_trlsd.php");
			else if ($r_rule[0] == 'e_trpud')
				include("eps_trpud.php");
			else if ($r_rule[0] == 'e_trfas')
				include("eps_trfas.php");
		}
		else if ($r_act == 'clear' and $c_delete){
			include("eps_cleardata.php");
		}
	}
	
	// Combo filter unit
	/*$strUnit = "select repeat('.',(level::int*2))||namaunit as namaunit, epskodeprodi from $schema.ms_unit where satker<>'' and info_left between $_SESSION[SIAUNLAM_TREE_UNIT] ";
	$strUnit.="order by kodeunit,parentunit";
	$rsUnit = $conn->Execute($strUnit);
	$listProgramStudi = $rsUnit->GetMenu2("unit",$c_unit,false,false,0," id=\"unit\" class=ControlStyle");*/
	
	//combo kurikulum
	$strKurikulum="select thnkurikulum from ak_tahun order by thnkurikulum desc";
	$rsKurikulum = $conn->Execute($strKurikulum);
	if (!$rsKurikulum->EOF)
	  	$listKurikulum = $rsKurikulum->GetMenu2("thnkurikulum",$r_thn_kurikulum,false,false,0," id=\"thnkurikulum\" class=ControlStyle onchange='doSubmit();'");
		
	//list periode
	$strPeriode = "select periode from $schema.ms_periode where 1=1 order by periode desc";
 	$rsPeriode  = $conn->Execute($strPeriode);
 	while (!$rsPeriode->EOF) {
		$listPeriode .= "\n<option value=\"" . $rsPeriode->fields["periode"] . "\"";
		if ($rsPeriode->fields["periode"]==$c_periode)
			$listPeriode .= " selected";
		$listPeriode .= ">" . convertPeriode($rsPeriode->fields["periode"]) . "</option>";
		$rsPeriode->MoveNext();
 	}
	
	//list tahun pelaporan
	$strPelaporan = "select substr(periode,1,4) as tahun from ms_periode where 1=1 group by substr(periode,1,4) order by tahun desc";
 	$rsPelaporan  = $conn->Execute($strPelaporan);
	$listPelaporan = $rsPelaporan->GetMenu2("tahun",$r_thn_pelaporan,false,false,0," id=\"tahun\" onchange='doSubmit();' class=ControlStyle");
	
	// array data pdpt	
	$a_all = array('e_msmhs' => 'MSMHS', 'e_tbkmk' => 'TBKMK', 'e_trakd' => 'TRAKD', 'e_trakm' => 'TRAKM', 'e_trlsm' => 'TRLSM', 'e_trnlm' => 'TRNLM', 'e_mspst' => 'MSPST',
					'e_msdos' => 'MSDOS', 'e_trkap' => 'TRKAP', 'e_mspst' => 'MSPST', 'e_trlsd' => 'TRLSD', 'e_trpud' => 'TRPUD', 'e_trfas' => 'TRFAS');
	//$l_all = UI::createSelect('isall',$a_all,$row['isall'],'ControlStyle');
	
	$sql = "select kodeunit,repeat('..',(level::int*2))||namaunit as namaunit,namasingkat,satker from ms_unit where satker is not null order by kodeunit,parentunit";
	$rsUnit=$conn->Execute($sql);
	
	#query untuk dapatkan log
	$sql_log = "select * from ms_transferpdpt where periode='$c_periode' and thnkurikulum='$r_thn_kurikulum' and thnpelaporan='$r_thn_pelaporan' order by idtransferpdpt asc ";
	$rs_log = $connp->Execute($sql_log);
	$arr_log = array();
	while($row = $rs_log->FetchRow()){
		$waktu_exp = explode(' ',$row['t_updatetime']);
		$arr_log[$row['kode_program_studi']][$row['tabelpdpt']] = $row['namapetugas'].'<br>'.formatDate($waktu_exp[0]).'<br>'.$waktu_exp[1];
	}
	
	$a_cols = count($a_all);
	
?>
<html>
<HEAD>
<title>SIAKAD : Transfer Data PDPT</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<meta http-equiv="Pragma" CONTENT="no-cache"> 
<link rel="STYLESHEET" href="style/pager.css" type="text/css">
<link href="style/pager.css" rel="stylesheet" type="text/css">
<!--<link href="style/style.css" rel="stylesheet" type="text/css">-->
<link type="text/css" rel="StyleSheet" href="style/tab.webfx.css"/>
<script type="text/javascript" src="scripts/tabpane.js"></script>
<link rel="stylesheet" href="style/button.css">
<style>
.darken {
	background-color: rgb(0, 0, 0);
	opacity: 0.4;
	-moz-opacity: 0.40;
	filter: alpha(opacity=40);
	z-index: 20;
	height: 100%;
	width: 100%;
	background-repeat: repeat;
	position: fixed;
	top: 0px;
	left: 0px;
}

.lighten {
	z-index: 50;
	height: 100%;
	width: 100%;
	background-repeat: repeat;
	position: fixed;
	top: 0px;
	left: 0px;
}
</style>
</head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<? 
	// Draw Top Menu
	include ("inc_menu.php"); 
?>
<div align="center">
<form name="cForm" id="cForm" method="post" action="<?= $i_phpfile ?>" enctype="multipart/form-data">
<table width="850">
	<tr>
  		<td align=center><font size="5"><b><u>Transfer Data PDPT </u></b></font></td>
	</tr>
	<tr>
		<td align=center><font color="#00CC33"><?= kosong($message) ? '' : $message ?></font></td>
	</tr>
	<tr>
		<td align=center>&nbsp;</td>
	</tr>
</table>
<table width="800">
	<tr>
            <td width="100" align="left"><strong>Tahun Pelaporan</strong></td>
			<td width="10" align="center"><b>:</b></td>
			<td width="285" align="left">				
					<?= $listPelaporan;?>				
			</td>
	</tr>
	<tr>
            <td width="100" align="left"><strong>Kurikulum </strong></td>
			<td width="10" align="center"><b>:</b></td>
			<td width="285" align="left">				
					<?= $listKurikulum;?>				
			</td>
	</tr>
	<tr>
            <td width="100" align="left"><strong>Periode </strong></td>
			<td width="10" align="center"><b>:</b></td>
			<td width="285" align="left">
				<select name="periode" id="periode" onchange='doSubmit();' class="ControlStyle">
					<?=$listPeriode;?>
				</select>
			</td>
	</tr>
	<!--<tr>
            <td width="100" align="left"><strong>Program Studi </strong></td>
			<td width="10" align="center"><b>:</b></td>
			<td width="285" align="left"><?= $listProgramStudi;  ?></td>
	</tr>-->	
</table>
<br>
  <table border=0 cellspacing=0 cellpadding="4" width="800">
<tr>
  	<td bordercolor="#FFFFFF"> 
	  <table width="800"  border="1" cellspacing="0" cellpadding="4" class="GridStyle" align="center">
        <tr class="HeaderBG" align="center">
          <td colspan="<?= 4+$a_cols?>">.: Transfer Data PDPT :.</td>
        </tr>
        <tr>
          <td width="10%" align="center" class="SubHeaderBG">No</td>
          <td width="50%" align="center" class="SubHeaderBG">Nama Program Studi</td>
		  <?  
		  foreach($a_all as $kode => $namapdpt){?>
		  <td width="10%" align="center" class="SubHeaderBG"><?= $namapdpt;?></td>
          <? }?>
          <? /* if($c_edit) { ?><td width="10%" class="SubHeaderBG">&nbsp;</td><? } */?>
        </tr>
	    <?
		$i=0;
		while(!$rsUnit->EOF){
			if ($i % 2) $rowStyle = "NormalBG";  else $rowStyle = "AlternateBG";
				$i++;
  		?>
        <tr class=<?= $rowStyle; ?> align="center">
          <td><?= $i ?></td>
          <td nowrap align="left"><?= $rsUnit->fields["namaunit"] ?></td>
		  <?  
		  foreach($a_all as $kode => $namapdpt){?>
		  <td nowrap width="70" align="center">
		  <img src="images/database_go.png" title="Export to PDPT <?= $namapdpt?>" style="cursor:pointer" onClick="goExport('<?=$kode.":".$rsUnit->fields["kodeunit"]?>')">
		  <br>
			<?
				if($kode == 'e_msdos')
					$tabelpdpt = 'tmst_dosen';
				if($kode == 'e_msmhs')
					$tabelpdpt = 'msmhs';
				if($kode == 'e_mspst')
					$tabelpdpt = 'tmst_program_studi';
				if($kode == 'e_tbkmk')
					$tabelpdpt = 'tmst_matakuliah';
				if($kode == 'e_trakd')
					$tabelpdpt = 'tran_aktivitas_mengajar_dosen';
				if($kode == 'e_trakm')
					$tabelpdpt = 'tran_kuliah_mhs';
				if($kode == 'e_trfas')
					$tabelpdpt = 'tmst_sarana_pt';
				if($kode == 'e_trkap')
					$tabelpdpt = 'tran_daya_tampung';
				if($kode == 'e_trlsd')
					$tabelpdpt = 'tran_riwayat_status_dosen';
				if($kode == 'e_trlsm')
					$tabelpdpt = 'tran_riwayat_status_mhs';
				if($kode == 'e_trnlm')
					$tabelpdpt = 'tran_nilai_semester_mhs';
				if($kode == 'e_trpud')
					$tabelpdpt = 'tran_publikasi_dosen_tetap';
					
			echo $arr_log[$rsUnit->fields["kodeunit"]][$tabelpdpt];
			?>
		  </td>
		  <? }?>
		  <? $rsUnit->moveNext(); } ?>
         
        </tr>		
		<tr class="HeaderBG"><td colspan="<?= 4+$a_cols?>">&nbsp;</td></tr>
      </table> 	</td>
  </tr>  
</table>
	  
		 <!-- <table width="700" border=1  cellspacing=0 cellpadding="4" class="GridStyle" bgcolor="#FFFFFF">
		  	<tr>
				<td colspan="2" class="SubHeaderBG">Transfer Data Transaksi</td>
			</tr>
			<tr>
				<td width="5%">1.</td>
				<td><img src="images/database_go.png" title="Export to PDPT" style="cursor:pointer" onClick="goExport('e_msmhs')">&nbsp;<strong>MSMHS (Data mahasiswa)</strong></td>
			</tr>
			<tr>
				<td width="5%">2.</td>
				<td><img src="images/database_go.png" title="Export to PDPT" style="cursor:pointer" onClick="goExport('e_tbkmk')">&nbsp;<strong>TBKMK (Data matakuliah)</strong></td>
			</tr>
			<tr>
				<td width="5%">3.</td>
				<td><img src="images/database_go.png" title="Export to PDPT" style="cursor:pointer" onClick="goExport('e_trakd')">&nbsp;<strong>TRAKD (Data aktivitas mengajar dosen)</strong></td>
			</tr>
			<tr>
				<td width="5%">4.</td>
				<td><img src="images/database_go.png" title="Export to PDPT" style="cursor:pointer" onClick="goExport('e_trakm')">&nbsp;<strong>TRAKM (Data kuliah mahasiswa)</strong></td>
			</tr>
			<tr>
				<td width="5%">5.</td>
				<td><img src="images/database_go.png" title="Export to PDPT" style="cursor:pointer" onClick="goExport('e_trlsm')">&nbsp;<strong>TRLSM (Data riwayat status mahasiswa)</strong></td>
			</tr>
			<tr>
				<td width="5%">6.</td>
				<td><img src="images/database_go.png" title="Start" style="cursor:pointer" onClick="goExport('e_trnlm')">&nbsp;<strong>TRNLM (Data nilai semester mahasiswa)<strong></td>
			</tr>	
			<tr>
				<td width="5%">7.</td>
				<td><img src="images/database_go.png" title="Start" style="cursor:pointer" onClick="goExport('e_mspst')">&nbsp;<strong>MSPST (Data program studi)<strong></td>
			</tr>	
		  </table>-->
</div>
<br>

<table width="700" border=0 cellpadding="4" cellspacing=0 bordercolor="#999999" bgcolor="#dbebfa" class="GridStyle" align="center">
  <tr>
    <td><strong><u>Keterangan </u></strong><br>
	  - Pilih Tab Transfer Data dan Klik ikon <img src="images/database_go.png" style="cursor:pointer"> untuk melakukan transfer data ke PDPT. <br>
	</td>
  </tr>
</table>
<input name="submit" type="submit" id="submit" style="display:none;visibility:hidden;" value="submit">
<input type="hidden" name="act" id="act">
<input type="hidden" name="rule" id="rule">
<!-- <input type="hidden" name="epskodeprodi" id="epskodeprodi" value="<?= $currPage ?>">-->
</form>
</div>
<div id="div_dark" class="darken" style="display:none"></div>
<div id="div_progressbar" class="lighten" style="display:none" align="center">
		<table height="100%" style="border-collapse:collapse;"><tr>
		<td align="center">
		<table bgcolor="#FFFFFF"><tr><td >
		Mohon tunggu...<br><br><img src="images/progressbar.gif"></td></tr>
		</table>
		</td>
		</tr></table>
</div>
<!--<div id="progressbar" style="position:absolute;visibility:hidden;left:0px;top:0px;">
	<table bgcolor="#FFFFFF" border="1" style="border-collapse:collapse;"><tr><td align="center">
	Mohon tunggu...<br><br><img src="images/progressbar.gif">
	</td></tr></table>
</div>-->

</body>
<script language="javascript">

	function doSubmit() {
		// cForm.page.value = 1;
		document.getElementById("submit").click();
	}

	function goLook(rule) {
		window.open("open.eps.php?rule=" + rule +"&nomenu=1","_blank","status=no,menubar=no,location=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,left=100,height=550");
	}
	
	function goEx(rule) {
	if(rule=='prodi')
		window.open("eps_tbkmk?nomenu=1","_blank","status=no,menubar=no,location=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,left=100,height=550");
	else if(rule=='mhs')
		window.open("eps_msmhs?nomenu=1","_blank","status=no,menubar=no,location=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,left=100,height=550");
	else if(rule=='dosen')
		window.open("eps_msdos?nomenu=1","_blank","status=no,menubar=no,location=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,left=100,height=550");
	}
	
	function goExport(rule)
	{	
		/*if(document.getElementById("kodeunit").value == '')
		{
			alert ("Kode Prodi Epsbed Kosong!!");
		}
		else{*/
		
			$("#div_dark").show();
			$("#div_progressbar").show();
			document.getElementById("act").value = 'export';
			document.getElementById("rule").value = rule;
			document.getElementById("submit").click();
		//}
	}
	
	function goClear()
	{
		document.getElementById("act").value = 'clear';
		document.getElementById("submit").click();
	}
	
	function goDownload(){
	/*var pesan = confirm("Apakah Anda yakin akan melakukan import data ?");
	if(pesan)
		document.cform.submit();*/
		if(document.getElementById("filter").value == "msmhs")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_msmhs.php')?>";			
		if(document.getElementById("filter").value == "tbkmk")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_tbkmk.php')?>";	
		if(document.getElementById("filter").value == "trakd")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakd.php')?>";	
		if(document.getElementById("filter").value == "trakm")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakm.php')?>";
		if(document.getElementById("filter").value == "trlsm")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trlsm.php')?>";	
		if(document.getElementById("filter").value == "trnlm")
			document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trnlm.php')?>";	
		document.getElementById("submit").click();		
	}
</script>
</html>