<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('sistemkuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
//	$conn->debug=true;
    // variabel request
    
    // properti halaman
	$p_title = 'Data Kunjungan';
	$p_tbwidth = 1100;
	$p_aktivitas = 'BIODATA';
    $p_detailpage = Route::getDetailPage();
    $r_key = Modul::getUserName();
    $p_model = mPendaftar;
	$nama = $_POST['filtervalue'];
	$idpegawai = $p_model::getIdPegawaiLogin($conn,$r_key);
	if(pendaftaran::isAdmin() || pendaftaran::isAdmMkt()){
		$a_data = $p_model::viewDatabymarketing($conn,'');
	}else{
		$a_data = $p_model::viewDatabymarketing($conn,$idpegawai);
	}
   
    $r_act = $_POST['act'];
    if($r_act=='detail' && !empty($r_act)){
       
        $record = array();
        $wheredata = explode("|",$_POST['key']);
       
        $record['tgldistribusi'] = $wheredata[0];
        $record['idpegawai'] = $wheredata[1];
        $a_data2 = $p_model::viewDatabymarketingDetail($conn,$record);

  }else if($r_act=='updatestatus' && !empty($r_act)){
	  // print_r($_POST);
	  // die();
		$record = array();
		$wheredata=array();
		$wheredata['id'] = $_POST['key']; 
		$record['statusfollowup'] ='1';
		$record['tglfollowup'] =date("Y-m-d");
		$record['keterangan'] = $_POST['ket'];
		
		$ok = $p_model::UpdateDataKunjungan($conn,$record,$wheredata);
		if($ok){
			$p_posterr = false;
			$p_postmsg ="Data Telah Di Follow Up";
		}
	}else if($r_act=='updatependaftar' && !empty($r_act)){
		$record = array();
		$wheredata=array();
		$wheredata['id'] = $_POST['key']; 
		$pendaftar = explode("-",$_POST['filtervalue'][$_POST['key']]);
		$record['nopendaftar'] =Cstr::removeSpecial($pendaftar[0]);
		$cekhm = $p_model::cekNoPendaftarHumas($conn,Cstr::removeSpecial($pendaftar[0]));

		if(empty($cekhm)){
				$ok = $p_model::UpdateDataKunjungan($conn,$record,$wheredata);
		}else{
			$p_posterr = true;
			$p_postmsg ="Nomor Pendaftaran Telah Terdaftar";
		}
		if($ok){
			$p_posterr = false;
			$p_postmsg ="Status Berhasil Di rubah";
		}
	}

	$a_input = array();
	$a_input[] = array('label' => 'Mahasiswa', 'nameid' => 'nim', 'type' => 'X', 'text' =>'mahasiswa','param'=>'acmahasiswa','add'=>' size="30"');


?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="scripts/DataTable/datatables.min.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/modal2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="scripts/foredit.js"></script>
	
	<script type="text/javascript" src="scripts/forpager.js"></script>
	 <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:1200px">
		<div class="SideItem" id="SideItem" style="width:1200px">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
				if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				
				<?	} ?>
				<?php require_once('inc_listfilter.php'); ?>
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
						</div>
					</header>
				</center>
				<br>
    <?php if(empty($a_data2)) { ?>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
                	<table id='table-header' width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<thead>
					<tr>

                            <th>Tanggal Data Masuk</th>
                            <th>Nama Marketing</th>
                            <th>Data Calon Mahasiswa</th>
                            <th>Calon Mahasiswa Di Hubungi </th>
							<th>Pendaftar</th>
							<th>Pendaftar NIM</th>
                            <th>Data Detail </th>
					</tr>
				</thead>

				<tbody>
                    <?php if(!empty($a_data)){
                        
                            foreach($a_data as $values){
                        
                        ?>


                        <tr>
                                <td><?= $values['tgldistribusi']; ?></td>
                                <td><?= $values['nama']; ?></td>
                                <td align="center"><?= $values['totaltarget']; ?></td>
                                <td align="center"><?= $values['realisasi']; ?></td>
								<td align="center"><?= $values['pendaftar']; ?></td>
								<td align="center"><?= $values['pendaftarnim']; ?></td>
                                <td align="center"><img id="<?= $values['tgldistribusi']."|".$values['idpegawai'] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>





						</tr>
						






					 <?php } ?>
					 
					 </tbody>
					 <tfoot>
					<tr>

                            <th>Tanggal Data Masuk</th>
                            <th>Nama Marketing</th>
                            <th>Data Calon Mahasiswa</th>
                            <th>Calon Mahasiswa Di Hubungi </th>
							<th>Pendaftar</th>
							<th>Pendaftar NIM</th>
                            <th>Data Detail </th>
					</tr>
							</tfoot>
					 
                
                     <?php   }else{ ?>

                    <tr>

                        <td colspan='5' align="center">Data Kosong</td>
                    </tr>
                    <?php  } ?>


                    
     </table>

                   
                        <?php }else{ ?>


                            <table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>

                            <th>Nama</th>
                            <th>No. Handphone</th>
                            <th>Kelas</th>
                            <th>Asal Sekolah</th>
							<th>keterangan </th>
                            <th>Follow up </th>
							<th>Pendaftar</th>
                    </tr>
                    <?php if(!empty($a_data2)){
                        
                        foreach($a_data2 as $values){
                    
                    ?>

<tr>
                                <td><?= $values['nama']; ?></td>
                                <td align="center"><?= $values['nohp']; ?></td>
                                <td align="center"><?= $values['kelas']; ?></td>
                                <td align="center"><?= $values['asalsekolah']; ?></td>
								<td align="center"><?= $values['keterangan']; ?></td>
                        <?php if(empty($values['nopendaftar'])){ ?>      
						<td align="center">

							<img id="<?= $values['id']."|".$values['nama']."|".$values['nohp']."|".$values['asalsekolah'] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goFollowUp(this)" style="cursor:pointer">
						</td>
						<?php } ?>


						<?php 
						if($values['statusfollowup']!='0' && empty($values['nopendaftar'])){ ?>
							<td>
							
								<input type="text" name="filtervalue[<?= $values['id'] ?>]"  maxlength='9' class="ControlAuto" id="filtervalue"  value="<?= $nama; ?>"/>
								<img id="<?= $values['id']."|".$values['nama']."|".$values['nohp']."|".$values['asalsekolah'] ?>" title="Simpan Data" src="images/disk.png" onclick="goPendaftar(this)" style="cursor:pointer">
							</td>	
						
							<?php	
							
							}elseif(!empty($values['nopendaftar'])){
								
								echo "<td></td><td>".$values['nopendaftar']."</td>";
							}
						


						?>


						

						





                        </tr>






                     <?php }
                
                
                        }else{ ?>

                    <tr>

                        <td colspan='6' align="center">Data Kosong</td>
                    </tr>
                    <?php  } ?>


                    
     </table>

                            <?php } ?>
                            <input type="hidden" name="act" id="act">
							<input type="hidden" name="ket" id="ket">
				    		<input type="hidden" name="key" id="key">
                    </form>
		</div>
	</div>
</div>

<!-- Modal Update Status -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header">

        <h3 class="modal-title">Follow Up Data Kunjungan</h3>
        
      </div>
      <div class="modal-body">
	  <form method="post" id="form" class="form-horizontal">
	  <input type="hidden" value="" name="id"/>
	  <div class="form-body">
	  		<div class="form-group">
              <label class="control-label col-md-3">Nama</label>
              <div class="col-md-9">
                <?php // echo form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control',  'placeholder' => 'username','required']); ?>
                <input name="nama" readonly="true" class="form-control" type="text" required>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Asal Sekolah</label>
              <div class="col-md-9">
                <?php // echo form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control',  'placeholder' => 'username','required']); ?>
                <input name="asalsekolah" readonly="true" class="form-control" type="text" required>
              </div>
            </div>	
			<div class="form-group">
              <label class="control-label col-md-3">No. Handphone</label>
              <div class="col-md-9">
                <?php // echo form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control',  'placeholder' => 'username','required']); ?>
                <input name="nohp" readonly="true" class="form-control" type="text" required>
              </div>
            </div>

			<div class="form-group">
              <label class="control-label col-md-3">Keterangan</label>
              <div class="col-md-9">
                <?php // echo form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control',  'placeholder' => 'username','required']); ?>
                <input name="keterangan" id='keterangan' class="form-control" type="text" required>
              </div>
            </div>						
	  								
	  		
			<!--<div class="form-group">				
			<label class="control-label col-md-3">Screenshoot Follow Up:</label>
			<div class="col-md-9">
   			 	<input type="file" name="fileToUpload" id="fileToUpload">
			</div>
			</div>-->

	  </div>


	  </form>
	  	
      </div>
      <div class="modal-footer">
	   <button type="button" id="btnSave" onclick="GoSave()" class="btn btn-primary">Follow Up</button>
        
      </div>

    </div>

  </div>
</div>
<!-- -->
<script type="text/javascript" src="scripts/jquery-3.3.1.js"></script>
<script type="text/javascript" src="scripts/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="scripts/modal.js"></script>
<script type="text/javascript" src="scripts/modalmanager.js"></script>
<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#table-header').DataTable( {
       
    } );
} );
$(document).ready(function() {
	initEdit(true);
	$("#filtervalue").xautox({strpost: "f=acpendaftar", targetid: "nopendaftar"});
	
});

function goFollowUp(elem)
{
	var str = elem.id;
	var res = str.split("|");
	$('[name="key"]').val(res[0]);
	$('[name="nama"]').val(res[1]);
	$('[name="asalsekolah"]').val(res[3]);
	$('[name="nohp"]').val(res[2]);
	//alert(res[0]);
	$('#myModal').modal('show');
}

function GoSave(){
	$('#ket').val($('[name="keterangan"]').val());
	$('#act').val('updatestatus');
	goSubmit();
}

function goPendaftar(elem)
{
	var str = elem.id;
	var res = str.split("|");
	$('[name="key"]').val(res[0]);
	$('#act').val('updatependaftar');
	goSubmit();
}

function goDetail(elem){
	
		$("#key").val(elem.id);
		$('#act').val('detail');
       // alert(elem.id);
		goSubmit();
}

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle contact
	$("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});



</script>

     </body>
</html>