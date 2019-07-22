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
	$conn->debug = false;
	// atur session 
	$pilih = $_REQUEST["pilih"];
	if(kosong($_REQUEST["pilih"])){
		$pilih = $_SESSION["SESS_PILIH"];	
	}
	if(!$pilih){
		$pilih = "dbf";		
	}
	$_SESSION["SESS_PILIH"]= $pilih;
	
	if($_REQUEST["tahun"])
	{
		$c_tahun = $_REQUEST["tahun"];
		
	}
	
	if($_REQUEST["periode"])
	{
		$c_periode = $_REQUEST["periode"];
		
	}
	
	if($_REQUEST["unit"])
	{
		$c_kodeprodi = $_REQUEST["unit"];
		
	}
	
	$message = '';
	
	if (isset($_REQUEST["act"]))
  	{
		$r_act = $_REQUEST["act"];
		$r_rule = $_REQUEST["rule"];
		$pathaddress = 'epsbed_file/';
		if ($r_act=="export" and $c_edit) 
		{
			if ($r_rule == 'e_msmhs'){				
				include("eps_msmhs.php");				
			}
			else if ($r_rule == 'e_tbkmk')
				include("eps_tbkmk.php");
			else if ($r_rule == 'e_trakd')
				include("eps_trakd.php");
			else if ($r_rule == 'e_trakm')
				include("eps_trakm.php");
			else if ($r_rule == 'e_trlsm')
				include("eps_trlsm.php");
			else if ($r_rule == 'e_trnlm')
				include("eps_trnlm.php");			
		}
		else if ($r_act == 'clear' and $c_delete){
			include("eps_cleardata.php");
		}
		
	}
	
	// Combo filter unit
	$strUnit = "select repeat('.',(level::int*2))||namaunit as namaunit, epskodeprodi from $schema.ms_unit where satker<>'' and info_left between $_SESSION[SIA_TREE_UNIT] ";
	$strUnit.="order by kodeunit,parentunit";
	$rsUnit = $conn->Execute($strUnit);
	$listProgramStudi = $rsUnit->GetMenu2("unit",$c_unit,false,false,0," id=\"unit\" class=ControlStyle");
	
	// Combo filter unit
	//$listProgramStudi = UI::cbFilterUnitUniv('unit',$c_unit,'',"style='z-index:-3;width:200px'");
	
	//combo kurikulum
	$strKurikulum="select thnkurikulum from ak_tahun order by thnkurikulum desc";
	$rsKurikulum = $conn->Execute($strKurikulum);
	if (!$rsKurikulum->EOF)
	  	$listKurikulum = $rsKurikulum->GetMenu2("thnkurikulum",$c_kurikulum,false,false,0," id=\"thnkurikulum\" class=ControlStyle");
		
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
	$listPelaporan = $rsPelaporan->GetMenu2("tahun",$c_tahun,false,false,0," id=\"tahun\" class=ControlStyle");
	
	//log 
	$rs_log = $connp->GetRow("select * from ms_downloadpdpt order by iddownloadpdpt desc limit 1");
	$rs_prodi = $conn->GetRow("select kodeunit,namaunit from ms_unit where kodeunit='".$rs_log['kode_program_studi']."'"); 
	$rs_periode = convertPeriode($rs_log['periode']);
	$waktu = explode(" ",$rs_log['t_updatetime']);
	if($rs_log['tabelpdpt']=='tmst_dosen')	
		$download = "MSDOS ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_daya_tampung')	
		$download = "TRKAP ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tmst_program_Studi')	
		$download = "MSPST ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tmst_sarana_pt')	
		$download = "TRFAS ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_publikasi_dosen_tetap')	
		$download = "TRPUD ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_riwayat_status_dosen')	
		$download = "TRLSD ".$rs_log['format'] ;
	
	if($rs_log['tabelpdpt']=='msmhs')	
		$download = "MSMHS ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tmst_matakuliah')	
		$download = "TBKMK ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_aktivitas_mengajar_dosen')	
		$download = "TRAKD ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_nilai_semester_mhs')	
		$download = "TRNLM ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_riwayat_status_mhs')	
		$download = "TRLSM ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='tran_kuliah_mhs')	
		$download = "TRAKM ".$rs_log['format'] ;
	
	if($rs_log['tabelpdpt']=='pdpt_all')	
		$download = "Semua File ".$rs_log['format'] ;
	if($rs_log['tabelpdpt']=='pdpt_all_dbf')	
		$download = "Semua File ".$rs_log['format'] ;
?>
<html>
<HEAD>
<title>SIAKAD : Download Data PDPT</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<meta http-equiv="Pragma" CONTENT="no-cache"> 
<link rel="STYLESHEET" href="style/pager.css" type="text/css">
<link href="style/pager.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="StyleSheet" href="style/tab.webfx.css" />
<script type="text/javascript" src="scripts/tabpane.js"></script>
<link rel="stylesheet" href="style/button.css">
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
  		<td align=center><font size=5><b><u>Download Data PDPT </u></b></font></td>
	</tr>
	<tr>
		<td align=center><font color="#00CC33"><?= kosong($message) ? '' : $message ?></font></td>
	</tr>
	<tr>
		<td align=center>&nbsp;</td>
	</tr>
</table>
      <table width="600" border=1  cellspacing=0 cellpadding="4" class="GridStyle" bgcolor="#FFFFFF">
		  	<tr>
				<td colspan="2" class="SubHeaderBG">Download Data PDPT</td>
			</tr>	
			<tr><td>Format</td>
				<td>
					<select name="pilih" id="pilih" class="ControlStyle" onChange="doFilter()">					
      						<option value="dbf" <?= $pilih=="dbf"? "selected" : "";?>>DBF</option>
      						<option value="csv" <?= $pilih=="csv"? "selected" : "";?>>CSV</option>
    				</select>					
				</td>	
			</tr>			
			<tr><td>Tahun Pelaporan</td><td>
				<?= $listPelaporan ?></td>	
			</tr>
			<tr><td>Kurikulum</td><td>
				<?= $listKurikulum ?></td>	
			</tr>
			<tr><td>Periode Pelaporan</td><td>
				<select name="periode" id="periode" class="ControlStyle">
					<?=$listPeriode;?>
				</select></td>
			</tr>
			<tr><td>Program Studi</td><td>
				<?= $listProgramStudi; ?></td>	
			</tr>				
			<tr><td>Data</td><td>			
			<? if($pilih=='dbf'){ ?>		
			<select name="filter" class="ControlStyle" id="filter">	
				<option value="all_dbf">Semua</option>
				<option value="msmhs_dbf">MSMHS (Data mahasiswa, Format DBF)</option>				
				<option value="tbkmk_dbf">TBKMK (Data matakuliah, Format DBF)</option>
				<option value="trakd_dbf">TRAKD (Data aktivitas mengajar dosen, Format DBF)</option>
				<option value="trakm_dbf">TRAKM (Data kuliah mahasiswa, Format DBF)</option>
				<option value="trlsm_dbf">TRLSM (Data riwayat status mahasiswa, Format DBF)</option>
				<option value="trnlm_dbf">TRNLM (Data nilai semester mahasiswa, Format DBF)</option>
				
				<option value="msdos_dbf">MSDOS (Data Dosen, Format DBF)</option>
				<option value="trkap_dbf">TRKAP (Data transaksi Kapasitas Mahasiswa Baru, Format DBF)</option>
				<option value="mspst_dbf">MSPST (Data Program Studi, Format DBF)</option>
				<option value="trlsd_dbf">TRLSD (Data Transaksi Cuti, Format DBF)</option>
				<option value="trpud_dbf">TRPUD (Data Transaksi Publikasi Dosen, Format DBF)</option>
				<option value="trfas_dbf">TRFAS (Data Fasilitas Program Studi, Format DBF)</option>
			</select>
			<? }else if($pilih=='csv'){?>
			<select name="filter" class="ControlStyle" id="filter">	
				<option value="all">Semua</option>				
				<option value="msmhs">MSMHS (Data mahasiswa, Format CSV)</option>
				<option value="tbkmk">TBKMK (Data matakuliah, Format CSV)</option>
				<option value="trakd">TRAKD (Data aktivitas mengajar dosen, Format CSV)</option>
				<option value="trakm">TRAKM (Data kuliah mahasiswa, Format CSV)</option>
				<option value="trlsm">TRLSM (Data riwayat status mahasiswa, Format CSV)</option>
				<option value="trnlm">TRNLM (Data nilai semester mahasiswa, Format CSV)</option>
				
				<option value="msdos">MSDOS (Data Dosen, Format CSV)</option>
				<option value="trkap">TRKAP (Data transaksi Kapasitas Mahasiswa Baru, Format CSV)</option>
				<option value="mspst">MSPST (Data Program Studi, Format CSV)</option>
				<option value="trlsd">TRLSD (Data Transaksi Cuti, Format CSV)</option>
				<option value="trpud">TRPUD (Data Transaksi Publikasi Dosen, Format CSV)</option>
				<option value="trfas">TRFAS (Data Fasilitas Program Studi, Format CSV)</option>
			</select>
			<? }?>			
			</td></tr>
			<tr>
				<td valign="top">Activity Log</td>
				<td>
					Download: <b><?= $download;?><br></b>
					Tahun Pelaporan: <b><?= $rs_log['thnpelaporan'];?><br></b>
					Kurikulum: <b><?= $rs_log['thnkurikulum'];?><br></b>
					Periode Pelaporan: <b><?= $rs_periode?><br></b>
					Program Studi: <b><?= $rs_prodi['kodeunit']." - ".$rs_prodi['namaunit']?><br></b>
					User: <b><?= $rs_log['nip'].' - '.$rs_log['namapetugas'];?><br></b>
					Waktu: <b><?= formatDate($waktu[0])?> &nbsp;&nbsp;&nbsp; <?= $waktu[1];?></b>
				</td>
			</tr>
			<tr><td colspan=2 align="center"><br><a class="buttonshort" href="javascript:goDownload()">Download</a>
			</td></tr>
		  </table>	
	<!--</div>-->
</div>
<br>
<input name="submit" type="submit" id="submit" style="display:none;visibility:hidden;" value="submit">
<input type="hidden" name="act" id="act">
<input type="hidden" name="pilihfilter" id="pilihfilter" value="<?=$pilih;?>">
<input type="hidden" name="rule" id="rule">
</form>
</div>
</body>
<script language="javascript">
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
		document.getElementById("act").value = 'export';
		document.getElementById("rule").value = rule;
		document.getElementById("submit").click();
	}
	
	function goClear()
	{
		document.getElementById("act").value = 'clear';
		document.getElementById("submit").click();
	}
	
	function goDownload(){	
		var pilih = document.getElementById("pilihfilter").value;
		
		if(pilih=="csv"){
		    
			if(document.getElementById("filter").value == "all")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_all.php')?>";			
			if(document.getElementById("filter").value == "msmhs")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_msmhs2.php')?>";	
			if(document.getElementById("filter").value == "tbkmk")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_tbkmk2.php')?>";	
			if(document.getElementById("filter").value == "trakd")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakd2.php')?>";	
			if(document.getElementById("filter").value == "trakm")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakm2.php')?>";
			if(document.getElementById("filter").value == "trlsm")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trlsm2.php')?>";	
			if(document.getElementById("filter").value == "trnlm")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trnlm2.php')?>";
			//ari
			if(document.getElementById("filter").value == "msdos")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_msdos2.php')?>";
			if(document.getElementById("filter").value == "trkap")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trkap2.php')?>";
			if(document.getElementById("filter").value == "mspst")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_mspst2.php')?>";
			if(document.getElementById("filter").value == "trlsd")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trlsd2.php')?>";
			if(document.getElementById("filter").value == "trpud")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trpud2.php')?>";
			if(document.getElementById("filter").value == "trfas")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trfas2.php')?>";
			
			document.getElementById("submit").click();	
		}else if(pilih=="dbf"){
			if(document.getElementById("filter").value == "all_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_all_dbf.php')?>";
			if(document.getElementById("filter").value == "msmhs_dbf"){
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_msmhs_dbf.php')?>";
			}			
			if(document.getElementById("filter").value == "tbkmk_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_tbkmk_dbf.php')?>";	
			if(document.getElementById("filter").value == "trakd_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakd_dbf.php')?>";	
			if(document.getElementById("filter").value == "trakm_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trakm_dbf.php')?>";
			if(document.getElementById("filter").value == "trlsm_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trlsm_dbf.php')?>";	
			if(document.getElementById("filter").value == "trnlm_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trnlm_dbf.php')?>";

			//ari
			if(document.getElementById("filter").value == "msdos_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_msdos_dbf.php')?>";
			if(document.getElementById("filter").value == "trkap_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trkap_dbf.php')?>";
			if(document.getElementById("filter").value == "mspst_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_mspst_dbf.php')?>";
			if(document.getElementById("filter").value == "trlsd_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trlsd_dbf.php')?>";
			if(document.getElementById("filter").value == "trpud_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trpud_dbf.php')?>";
			if(document.getElementById("filter").value == "trfas_dbf")
				document.getElementById("cForm").action="<?= Helper::navAddress('pdpt_trfas_dbf.php')?>";
				
			document.getElementById("submit").click();	
		}
	}

	function doFilter() {
		document.getElementById("cForm").target = "_self";
		document.getElementById("cForm").action = "<?= $i_phpfile ?>";
		document.getElementById("submit").click();		
	}
</script>
</html>