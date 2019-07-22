<?php 
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
// hak akses
$a_auth = Modul::getFileAuth();

$c_insert = $a_auth['caninsert'];
$c_delete = $a_auth['candelete'];
$c_edit = $a_auth['canupdate'];
// include
require_once(Route::getModelPath('dosenwali'));
require_once(Route::getModelPath('kelas'));
require_once(Route::getUIPath('combo'));
$conn->debug = false;
// variabel request

$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
$r_dosen = Modul::setRequest($_POST['dosen'],'DOSEN');
$r_periode=Akademik::getPeriode();
if(strpos($r_dosen,' - ')){
    $arr=explode(' - ',$r_dosen);
    $r_dosen=$arr[0];
}
$r_kelas = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
$r_jurusan = Modul::setRequest($_POST['jurusan'],'JURUSAN');
$r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');

//variabel dari text autocomplete
$r_dosen2 = Modul::setRequest($_POST['dosen2'],'DOSEN');
$r_nama = Modul::setRequest($_POST['dosen2'],'DOSEN');
// combo
$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
//$l_dosen = uCombo::dosen($conn,$r_dosen,'','dosen','onchange="goSubmit()" style="width:300px"',false);
$i_dosen=UI::createTextBox('dosen2',$r_dosen2,'ControlStyle', 40, 40);
$i_dosen.=' <input type="button" value="Tampilkan" onclick="goSubmit()">';
$i_dosen.='<input type="hidden" name="dosen">';
if(empty($r_dosen))
		$c_delete=false;

$p_title = 'Setting Dosen Wali';
$p_tbwidth = 750;
$p_aktivitas = 'DOSEN';
$p_detailpage = Route::getDetailPage();

$p_model = mDosenwali;

$a_data=$p_model::getDataDosen($conn);

$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_dosen)) $a_filter[] = $p_model::getListFilter('dosen',$r_dosen);
	$a_data=$p_model::getDataDosen($conn);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	//$a_filtercombo[] = array('label' => 'Dosen', 'combo' => $l_dosen);
	$a_filtercombo[] = array('label' => 'Dosen', 'combo' => $i_dosen);
	$l_kelas=uCombo::kelas($conn,$r_kelas,'sistemkuliah','',false);
	$l_jurusan=uCombo::jurusan($conn,$r_jurusan,'','jurusan','',false,true);
	$l_angkatan=uCombo::angkatan($conn,$r_angkatan,'angkatan','',false);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
    <div id="wrapper">
		<div class="SideItem" id="SideItem">
        <form name="pageform" id="pageform" method="post">
		<center>
        <input type="text" id="txtSearch" onkeyup="fSearch()" placeholder="Cari Berdasarkan nama.." title="Input Nama " style="width:700px;font-size:14px;padding: 12px 20px 12px 40px;border: 1px solid #ddd;margin-bottom: 12px;">
		</center>
        <?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
                 <center>
            
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	} ?>
								
							</div>
							<?	} ?>
						</div>
					</header>
				</center>
                <?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table id="tWaldos" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
                                        <th>idpegawai</th>
                                        <th>Nama Dosen</th>
                                        <th>Set Wali Dosen</th>
                    </tr>
                    <?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {	
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
                    <tr valign="top" class="<?= $rowstyle ?>">
                        <td><?= $row['idpegawai']; ?></td>
                        <td><?= $row['namadosen']; ?></td>
                        <td align="center"><input type="checkbox" id="<?=$row['idpegawai']?>" <?=($row['isdosenwali']==-1)?'checked':''?> title="Wali Dosen" onclick="bukaSPP(this)" <?=!$c_edit?'disabled':''?>></td>
                    </tr>
                        <? } ?>
                </table>
        </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>
<script type="text/javascript">
function bukaSPP(elem){
	if(elem.checked){
		var posted = "f=setDosenWali&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			/*if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}*/
		});
	}else if(!elem.checked){
		var posted = "f=setSPP&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}
}
function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	//$(".DivSuccess").fadeOut(2000);
}
function fSearch() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("txtSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("tWaldos");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
</body>
</html>