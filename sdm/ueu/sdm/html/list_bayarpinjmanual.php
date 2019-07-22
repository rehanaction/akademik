<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pinjaman'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = Modul::setRequest($_POST['key'],'IDPINJAMAN');;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPinjaman;
		
	$p_tbwidth = "800";
	$p_title = "Data Pembayaran Pinjaman";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pe_bayarpinjaman";
	$p_key = "idpinjaman";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'jnspinjaman', 'label' => 'Jenis Pinjaman', 'readonly' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'readonly' => true);
	$a_input[] = array('kolom' => 'keperluan', 'label' => 'Keperluan', 'readonly' => true);
	$a_input[] = array('kolom' => 'noperjanjian', 'label' => 'No Perjanjian', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglperjanjian', 'label' => 'Tgl. Perjanjian', 'readonly' => true, 'type' => 'D');
	$a_input[] = array('kolom' => 'jmldisetujui', 'label' => 'Jml. Pinjaman disetujui', 'readonly' => true, 'type' => 'N');
	$a_input[] = array('kolom' => 'jmlcicilandisetujui', 'label' => 'Jml. Cicilan disetujui', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'biayaadministrasi', 'label' => 'Biaya Administrasi', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'totalpinjaman', 'label' => 'Total Pinjaman', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'saldo', 'label' => 'Saldo Pinjaman', 'type' => 'N', 'readonly' => true);
	
	$r_act = $_POST['act'];
	if($r_act == 'savebyr' and $c_edit) {
		$recbyr = array();
		$recbyr['nobkm'] = CStr::removeSpecial($_POST['nobkm']);
		$recbyr['tglbayar'] = CStr::formatDate($_POST['tglbayar']);
		$recbyr['keterangan'] = CStr::removeSpecial($_POST['keterangan']);
		$recbyr['jmlbayar'] = str_replace('.','',$_POST['jmlbayar']);
		$recbyr['bayarvia'] = 'K';
		$recbyr['idpinjaman'] = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recbyr,true,$p_dbtable,'',$recbyr,true,$r_subkey);
		
		//update angsuran
		if(!$p_posterr){
			if(count($_POST['dibayar'])>0){
				foreach($_POST['dibayar'] as $noangs){
					$recangs = array();
					$recangs['idbayarpinjaman'] = $r_subkey;
					$recangs['isdibayar'] = 'Y';
					
					$key = $r_key.'|'.$noangs;
					$colkey = 'idpinjaman,noangsuran';
					$p_model::updateRecord($conn,$recangs,$key,false,'pe_angsuran',$colkey);
				}
			}
		}
	}
	else if($r_act == 'deletebyr' and $c_delete) {
		$r_subkey = $_POST['subkey'];
		
		$recangs = array();
		$recangs['idbayarpinjaman'] = 'null';
		$recangs['isdibayar'] = 'N';
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$recangs,$r_subkey,false,'pe_angsuran','idbayarpinjaman');
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,'idbayarpinjaman');
	}
	
	$sql = $p_model::getDataEditPembayaran($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,'',$p_key,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['id'] == 'saldo')
			$saldo = $t_row['value'];
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$rowd = array();
	$rowd = $p_model::getProsesBayar($conn,$r_key);
	
	//daftar angsuran
	$rsa = $p_model::getAngsuranPinj($conn,$r_key);
	$a_status = $p_model::statusBayar();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<table width="100%">
				<tr>
					<td>
						<form name="pageform" id="pageform" method="post">
							<?	/**************/
								/* JUDUL LIST */
								/**************/
								
								if(!empty($p_title) and false) {
							?>
							<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
							<br>
							<?	}
								
								/*****************/
								/* TOMBOL-TOMBOL */
								/*****************/
								
								if(empty($p_fatalerr)){
							?>
									<table border="0" cellspacing="10" align="center">
										<tr>
											<?	if($c_edit) { ?>
										   <td id="be_edit" class="TDButton" onclick="goEdit()">
												<img src="images/edit.png"> Sunting
											</td>
											<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
												<img src="images/undo.png"> Batal
											</td>
											<?	} ?>
										</tr>
									</table>
							<?
								}
								if(!empty($p_postmsg)) { ?>
							<center>
							<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
								<?= $p_postmsg ?>
							</div>
							</center>
							<div class="Break"></div>
							<?	}
							
								if(empty($p_fatalerr)) { ?>
							<center>
								<header style="width:<?= $p_tbwidth ?>px">
									<div class="inner">
										<div class="left title">
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title; ?></h1>
										</div>
									</div>
								</header>
								<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
								<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
									<tbody>
										<tr>
											<td width="200"><?= Page::getDataLabel($row,'jnspinjaman') ?></td>
											<td>:</td>
											<td><?= Page::getDataValue($row,'jnspinjaman') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'namalengkap') ?></td>
											<td>:</td>
											<td><?= Page::getDataValue($row,'namalengkap') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'keperluan') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'keperluan') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'noperjanjian') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'noperjanjian') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'tglperjanjian') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'tglperjanjian') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'jmldisetujui') ?></td>
											<td>:</td>
											<td>
												<?= Page::getDataInput($row,'jmldisetujui') ?> 
												&nbsp;&nbsp;&nbsp;Angsuran : <?= Page::getDataInput($row,'jmlcicilandisetujui') ?> x
											</td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'biayaadministrasi') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'biayaadministrasi') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'totalpinjaman') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'totalpinjaman') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'saldo') ?></td>
											<td>:</td>
											<td>
												<span id="ssaldo"><?= Page::getDataInput($row,'saldo') ?></span>
												<input type="hidden" name="saldo" id="saldo" value="<?= $saldo?>">
												<input type="hidden" name="saldoskrg" id="saldoskrg">
											</td>
										</tr>
									</tbody>
								</table>
								<br>
								
								<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
									<tr>
										<td colspan="5" class="DataBG">Proses Pembayaran Pinjaman</td>
									</tr>
									<tr>
										<th align="center" class="HeaderBG">No. Bukti</th>
										<th align="center" class="HeaderBG">Tgl. Bayar</th>
										<th align="center" class="HeaderBG">Keterangan</th>
										<th align="center" class="HeaderBG">Jml. Bayar</th>
										<th align="center" class="HeaderBG" width="50">Aksi</th>
									</tr>
									<?	$i = 0;
										if(count($rowd)>0){
											foreach($rowd as $rowb) {
												if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
												$no = $rowb['idbayarpinjaman'];
									?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td><?= $rowb['nobkm'];?></td>
										<td align="center"><?= CStr::formatDate($rowb['tglbayar']);?></td>
										<td><?= $rowb['keterangan'];?></td>
										<td align="right"><?= CStr::formatNumber($rowb['jmlbayar']);?></td>
										<td align="center">
											<? if ($c_delete) {?>
											<span id="edit" style="display:none">
											<img id="<?= $no ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteByr(this)" style="cursor:pointer">
											</span>
											<? } ?>
										</td>
									</tr>
									<?	}
									}
										if($i == 0) { ?>
									<tr>
										<td align="center" colspan="5">Data kosong</td>
									</tr>
									<?	} ?>
									
									<? if ($c_edit) {?>
									<tr valign="top" class="LeftColumnBG" id="edit" style="display:none">
										<td><?= UI::createTextBox('nobkm','','ControlStyle',30,30,$c_edit);?></td>
										<td>
											<?= UI::createTextBox('tglbayar','','ControlStyle',10,10,$c_edit);?>
											<img src="images/cal.png" id="tglbayar_trg" style="cursor:pointer;" title="Pilih tanggal bayar">
											<script type="text/javascript">
											Calendar.setup({
												inputField     :    "tglbayar",
												ifFormat       :    "%d-%m-%Y",
												button         :    "tglbayar_trg",
												align          :    "Br",
												singleClick    :    true
											});
											</script>
										</td>
										<td><?= UI::createTextBox('keterangan','','ControlStyle',255,30,$c_edit);?></td>
										<td><?= UI::createTextBox('jmlbayar','','ControlStyle',14,14,$c_edit,'onkeydown="return onlyNumber(event,this,true,true);" onKeyUp="hitungSaldo(this.value);numberFormat(this);"');?></td>
										<td align="center">
											<img id="tblsave" title="Tambah Data" src="images/disk.png" onclick="goInsertByr()" style="cursor:pointer;display:none">
										</td>
									</tr>
									<? } ?>
								</table>			
								</div>
							</center>
							<br>
							<center>
								<b>Keterangan : </b><span id="sketerangan">Jumlah angsuran harus sesuai dengan jumlah pembayaran</span>
							</center>
							<br>
							<center>
								<table class="GridStyle" width="<?= $p_tbwidth?>" cellspacing="2" cellpadding="4" align="center">
									<tr>
										<td class="DataBG" colspan="7">Detail Angsuran Pinjaman (Pilih untuk angsuran ke berapa)</td>
									</tr>
									<tr>
										<th class="HeaderBG" align="center">Angsuran Ke</th>
										<th class="HeaderBG" align="center">Jumlah Angsuran</th>
										<th class="HeaderBG" align="center">Status</th>
										<th class="HeaderBG" align="center">Periode Gaji Tunda</th>
										<th class="HeaderBG" align="center">Keterangan</th>
										<th class="HeaderBG" align="center">No. Bukti Bayar</th>
										<th id="edit" class="HeaderBG" width="50" align="center" style="">Aksi</th>
									</tr>
									<?
										$i = 0;
										while($rowa = $rsa->FetchROw()){
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
											$jmlangsuran += $rowa['jmlangsuran'];
											$no = $rowa['noangsuran'];
									?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td align="center"><?= $rowa['noangsuran']?></td>
										<td align="right"><?= CStr::formatNumber($rowa['jmlangsuran'])?></td>
										<td align="center" bgcolor="<?= $rowa['isdibayar'] == 'Y' ? 'green' : ($rowa['isdibayar'] == 'T' ? 'yellow' : '')?>">
											<?= $a_status[$rowa['isdibayar']]?>
										</td>
										<td align="center"><?= $a_bulan[$rowa['bulan']].' '.$rowa['tahun']?></td>
										<td align="center"><?= $rowa['keterangan']?></td>
										<td align="center"><?= $rowa['nobkm']?></td>
										<td align="center">
											<span id="edit" style="display:none">
											<?if($c_edit and $rowa['isdibayar'] != 'Y'){?>
												<input type="checkbox" name="dibayar[]" id="dibayar" value="<?=$no?>" onclick="cekAngsuran(this)">
												<input type="hidden" id="jmlangs<?=$no?>" name="jmlangs<?=$no?>" value="<?= CStr::formatNumber($rowa['jmlangsuran'])?>">
											<?}?>
											</span>
										</td>
									</tr>
									<?}?>
									<tr>
										<td align="center"><b>Jumlah</b></td>
										<td align="right"><b><?= CStr::formatNumber($jmlangsuran)?></b></td>
										<td colspan="5">&nbsp;</td>
									</tr>
								</table>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
							<input type="hidden" name="detail" id="detail">
							<input type="hidden" name="subkey" id="subkey">
						</form>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var detailreq = "<?= @implode(',',$a_detailreq) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
});

function goInsertByr(){
	if(cfHighlight('nobkm')){
		document.getElementById("act").value = "savebyr";
		goSubmit();
	}
}

function goDeleteByr(elem){
	var hapus = confirm("Apakah anda yakin akan menghapus data pembayaran?");
	if(hapus) {
		document.getElementById("act").value = "deletebyr";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function cekAngsuran(elem){
	if(cfHighlight('jmlbayar')){
		//cek apakah ada yang sudah dicentang
		if($("input[id='dibayar']:checked").val() == null){
			$("#sketerangan").html('Jumlah angsuran harus sesuai dengan jumlah pembayaran');
			$("#tblsave").hide();
		}else{
			var jmlangs = 0;
			$("input[id='dibayar']:checked").each(function() {
				var jmlangscek = $("#jmlangs"+$(this).val()).val().replace(/\./g,'');
				jmlangs = parseInt(jmlangs) + parseInt(jmlangscek);
			});
			
			var posted = "f=gbyrpinjam&q[]="+jmlangs+"&q[]="+$("#jmlbayar").val();
			$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
				var text = text.split('|');
				if(text[0] == 'err'){
					$("#sketerangan").html('<font color="red">'+text[1]+'</font>');
					$("#tblsave").hide();
				}else{
					$("#sketerangan").html('<font color="green">Jumlah angsuran sesuai dengan jumlah bayar</font>');
					$("#tblsave").show();
				}
			});
		}
	}else{
		elem.checked = false;
	}
}

function hitungSaldo(val){
	var posted = "f=gsaldopinjamam&q[]="+val+"&q[]="+$("#saldo").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#ssaldo").html(text);
		$("#saldoskrg").val(text);
	});
}

function numberFormat(num) {
	var ret = '';
	var j = 0;
	valnum = num.value;
	
	if(valnum != ''){	
		valnum = String(valnum);
		valnum = valnum.replace(/\./g,'');
		for(i=valnum.length-1;i>=0;i--) {
			if(j == 3) {
				ret = "." + ret;
				j = 0;
			}
			ret = valnum.charAt(i) + ret;
			j++;
		}
		
		num.value = ret;
	}
}
</script>
</body>
</html>
</html>