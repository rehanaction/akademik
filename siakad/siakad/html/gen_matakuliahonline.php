<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$a_auth = Modul::getFileAuth();
	// hak akses
	Modul::getFileAuth();
	//$conn->debug=true;
	//$conn->debug=true;
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	$c_delete = $a_auth['candelete'];
	// variabel request
	if(Akademik::isDosen()){
		
		$r_key = Modul::getUserIDPegawai();
		//$r_dosen = Modul::getUserName() ? Modul::getUserName().' - '.$_SESSION['SIAKAD']['MODUL']['USERDESC'] : $_SESSION['SIAKAD']['MODUL']['USERDESC'];
		}
	else{
		$r_key = CStr::removeSpecial($_REQUEST['idpegawai']);
		//$r_dosen = CStr::removeSpecial($_REQUEST['dosen']);
	}

	$r_act = $_POST['act'];
	if($r_act == 'genPeserta') {


	
        //$o_jenisujian = CStr::removeSpecial($_POST['o_jenisujian']);
		$keydata = array();
		$keydata = $_POST['key'];
		$countF=0;
		$countT=0;
		$countAll=0;
		foreach($keydata as $keyall){
			$expKeyall = explode("|" ,$keyall);
			mKelas::updateKelasPertemuanOnline($conn,$keyall);
			mKelas::updatePertemuanOnline($conn,$keyall);
			$keypeserta =$expKeyall[0]."|".$expKeyall[1]."|".$expKeyall[2]."|".$expKeyall[3]."|".$expKeyall[4]."|".$expKeyall[5]."|".$expKeyall[6];
				if($expKeyall[5]=='P')
					$peserta = mKelas::getDataPeserta($conn,$keypeserta,$$expKeyall[6]);
				else
					$peserta = mKelas::getDataPeserta($conn,$keypeserta);
			$course = mMengajar::getCourseByPass($conn_moodle,$expKeyall[2]."".$expKeyall[3]."".$expKeyall[1]."".$expKeyall[4]);
			if(empty($course)){
				
				if(mMengajar::addCourseMoodle($keyall)){
					$mooduser = mMengajar::getUserMoodle($conn,$expKeyall[10]);
					if(!empty($mooduser['users'])){
						mMengajar::enrolDosen($conn_moodle,$conn,$keyall);
						$courses = mMengajar::getCourseByPass($conn_moodle,$expKeyall[2]."".$expKeyall[3]."".$expKeyall[1]."".$expKeyall[4]);
						foreach($peserta as $row){
							$moodMhs = mMengajar::getUserMoodle($conn,$row['nim']);
							if(!empty($moodMhs['users'])){
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}else{
								$d_users=mMengajar::inquiryByusername($conn,$row['nim']);
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::syncUserToElearning($conn,$d_users);
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}
						}
						$countT=$countT+1;
					}else{

						$d_users=mMengajar::inquiryByuserid($conn,$expKeyall[10]);
						mMengajar::syncUserToElearning($conn,$d_users);
						mMengajar::enrolDosen($conn_moodle,$conn,$keyall);
						$courses = mMengajar::getCourseByPass($conn_moodle,$expKeyall[2]."".$expKeyall[3]."".$expKeyall[1]."".$expKeyall[4]);
						foreach($peserta as $row){
							
							$moodMhs = mMengajar::getUserMoodle($conn,$row['nim']);
							if(!empty($moodMhs['users'])){
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}else{
								$d_users=mMengajar::inquiryByusername($conn,$row['nim']);
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::syncUserToElearning($conn,$d_users);
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}
						}
						$countT=$countT+1;
					}
				}else{
					$countF=$countF+1;
				}
				$countAll++;
			}else{
				//delete course
				//insert baru
				mMengajar::DeleteCourse($course);
				if(mMengajar::addCourseMoodle($keyall)){
					$mooduser = mMengajar::getUserMoodle($conn,$expKeyall[10]);
					if(!empty($mooduser['users'])){
						mMengajar::enrolDosen($conn_moodle,$conn,$keyall);
						$courses = mMengajar::getCourseByPass($conn_moodle,$expKeyall[2]."".$expKeyall[3]."".$expKeyall[1]."".$expKeyall[4]);
						foreach($peserta as $row){
							$moodMhs = mMengajar::getUserMoodle($conn,$row['nim']);
							if(!empty($moodMhs['users'])){
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}else{
								$d_users=mMengajar::inquiryByusername($conn,$row['nim']);
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::syncUserToElearning($conn,$d_users);
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}
						}
						$countT=$countT+1;
					}else{

						$d_users=mMengajar::inquiryByuserid($conn,$expKeyall[10]);
						mMengajar::syncUserToElearning($conn,$d_users);
						mMengajar::enrolDosen($conn_moodle,$conn,$keyall);
						$courses = mMengajar::getCourseByPass($conn_moodle,$expKeyall[2]."".$expKeyall[3]."".$expKeyall[1]."".$expKeyall[4]);
						foreach($peserta as $row){
							$moodMhs = mMengajar::getUserMoodle($conn,$row['nim']);
							if(!empty($moodMhs['users'])){
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}else{
								$d_users=mMengajar::inquiryByusername($conn,$row['nim']);
								$keyMhs = $courses."|".$row['nim'];
								mMengajar::syncUserToElearning($conn,$d_users);
								mMengajar::enrolMahasiswa($conn_moodle,$conn,$keyMhs);
							}
						}
						$countT=$countT+1;
					}
				}else{
					$countF=$countF+1;
				}
				$countAll++;
			}
		}
		$p_postmsg = "Total ".$countAll." Mata kuliah Berhasil Syncronisasi ".$countT.", gagal ".$countF;
	}
	$t_mengajar = -1;
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_nama = Akademik::getNamaPegawai($conn,$r_key);
	$r_dosen=$r_nama;
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Generate Matakuliah Online';
	$p_tbwidth = "100%";
	$p_aktivitas = 'MENGAJAR';
	
	$p_model = mMengajar;
	$a_jenis=array('K'=>'Kuliah','P'=>'Praktikum','R'=>'Tutorial');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No.', 'width' =>'10px');
	
	$a_kolom[] = array('kolom' => 'a.kodemk', 'label' => 'Kode Matakuliah', 'width' =>'100px');
	$a_kolom[] = array('kolom' => 'd.namamk', 'label' => 'Nama Matakuliah');	
	$a_kolom[] = array('kolom' => 'c.namaunit', 'label' => 'Prodi', 'width' =>'100px');
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => mMahasiswa::sistemKuliah($conn),'readonly'=>true, 'width' =>'100px');

	//$a_kolom[] = array('kolom' => 'd.semmk', 'label' => 'Smt.');
    $a_kolom[] = array('kolom' => 'a.kelasmk', 'label' => 'Kelas', 'width' =>'10px');
    $a_kolom[] = array('kolom' => 'd.semmk', 'label' => 'Semester', 'width' =>'10px');
	$a_kolom[] = array('kolom' => 'd.sks', 'label' => 'SKS', 'width' =>'10px');
	$a_kolom[] = array('kolom' => 'f_namahari(b.nohari)', 'alias' => 'namahari', 'label' => 'Hari');
	$a_kolom[] = array('kolom' => 'b.jammulai', 'label' => 'Mulai', 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'b.koderuang', 'label' => 'Ruang');
	$a_kolom[] = array('kolom' => 'a.jeniskul', 'label' => 'Jenis', 'type' => 'S', 'option' =>$a_jenis, 'width' =>'10px');
	// /$a_kolom[] = array('kolom' => 'a.kelompok', 'label' => 'Kel(Prakt)');
	$a_kolom[] = array('kolom' => 'b.jumlahpeserta', 'alias' => 'jmlpeserta', 'label' => 'Jumlah Mahasiswa', 'width' =>'10px');
	
	$p_colnum = count($a_kolom)+1;
	$r_sort='namamk,kelasmk';
	$a_filter = Page::setFilter($_POST['filter']);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_key)) $a_filter[] = $p_model::getListFilter('nipdosen',$r_key);
	if(!empty($t_mengajar)) $a_filter[] = $p_model::getListFilter('tugasmengajar',$t_mengajar);
	
	// mendapatkan data
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	
	// membuat filter
	if(empty($r_key))
		$r_dosen = '';
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Dosen', 'combo' => UI::createTextBox('dosen',$r_dosen,'ControlStyle',0,60).' <input type="button" value="Tampilkan" onclick="goSubmit()">');
	
	$a_combodosen=array();
	$a_combodosen[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager_.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<!--div style="float:left; width:15%">
				<?// require_once('inc_sidemenudosen.php');?>
			</div-->
			<div>

			<form name="pageform" id="pageform" method="post">
			<?	if(!empty($p_postmsg)) { ?>
			 
			 <div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
				 <?= $p_postmsg ?>
			 </div>
			  
			 
			 <?	} ?>
			 <div class="Break"></div>
			 <?php require_once('inc_headerdosen.php') ?>
				
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center" id="datatablematakuliah">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th width="30">Dosen Siakad</th>
						<th width="30">Dosen elearning</th>
						<th width="30">isOnline</th>
                        <th width="30">Set online</th>
						<th width="30" style="display:none">Aksi</th>
					</tr>
                    <tr>
                                <th><input type="text" id="myNo" onkeyup="fillTable(this,0)" placeholder="no"></th>
                                <th><input type="text" id="myKode" onkeyup="fillTable(this,1)"placeholder="kode"></th>
                                <th><input type="text" id="myName" onkeyup="fillTable(this,2)" placeholder="matakuliah"></th>
                                <th><input type="text" id="myProdi" onkeyup="fillTable(this,3)" placeholder="prodi"></th>
                                <th><input type="text" id="myBasis" onkeyup="fillTable(this,4)" placeholder="Basis"></th>
                                <th><input type="text" id="myKelas" onkeyup="fillTable(this,5)" placeholder="kelas"></th>
                                <th></th>
                                <th><input type="text" id="mySks" onkeyup="fillTable(this,6)" placeholder="sks"></th>
                                <th><input type="text" id="myHari" onkeyup="fillTable(this,7)" placeholder="hari"></th>
                                <th><input type="text" id="myJM" onkeyup="fillTable(this,8)" placeholder="jam mulai"></th>
                                <th><input type="text" id="myRg" onkeyup="fillTable(this,10)" placeholder="Ruang"></th>
                                <th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
                                <th></th>
                                <th style="display:none"></th>

                    </tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = mKelasPraktikum::getKeyRow($row);
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
				
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_width))
									$t_align = ' align="Center"';
						?>
						<td <?= $t_align ?>><?= $rowcc ?></td>

						<?	} $jk='';?>
						
						<?php if($row['jeniskul']=='Kuliah')
								{$jk='K';
							}elseif(row['jeniskul']=='Praktikum'){
								$jk='P';
							}else{
								$jk='R';
							} ?>
						<td><?=mMengajar::getNamaDosen($conn,$row['nipdosen']) ?></td>
						<td><?=mMengajar::getDosenMoodle($conn_moodle,mMengajar::getCourseByPass($conn_moodle,$row['kodeunit']."".$r_periode."".$row['kodemk']."".$row['kelasmk']),$row['nipdosen'])?></td>
						<td><?php if(!empty(mMengajar::getCourseByPass($conn_moodle,$row['kodeunit']."".$r_periode."".$row['kodemk']."".$row['kelasmk']))) { echo "Y";}else{echo "N";} ?></td>
                        <td><input type="checkbox" name="key[]" value="<?= $r_tahun."|".$row['kodemk']."|".$row['kodeunit']."|".$r_periode."|".$row['kelasmk']."|".$jk."|".$row['kelompok']."|".$row['semmk']."|".$row['namamk']."|".$row['startdate']."|".$row['nipdosen'] ?>" id="chk"></td>
						<td style="display:none"><!--<input type="hidden" name="key[]" id="key[]" value="<? //$r_tahun."|".$row['kodemk']."|".$row['kodeunit']."|".$r_periode."|".$row['kelasmk']."|".$jk."|".$row['kelompok']."|".$row['semmk']."|".$row['namamk'] ?>">--></td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<br/>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
				<tr class="NoHover NoGrid">		
						<td align="center" ><input type="button" value="Generate Matakuliah Online" onclick="goGenPeserta()"></td>
					</tr>
					
				</table>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				
				
					
				<? if(!Akademik::isDosen()) { ?>
				<input type="hidden" id="nip" name="nip" value="<?= $r_key ?>">
				<? } ?>

			</form>
			<!--/div-->
			
		
			
		</div>
	</div>
</div>



<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
function fillTable(elem,index) {
  // Declare variables 
  
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(elem.id);
  filter = input.value.toUpperCase();
  table = document.getElementById("datatablematakuliah");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[index];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	$("#dosen").xautox({strpost: "f=acdosen", targetid: "nip"});
});

function goGenPeserta(){
	
	document.getElementById("act").value="genPeserta";
	goSubmit();
}

</script>
</body>
</html>
