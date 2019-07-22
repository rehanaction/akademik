<?
require_once(Route::getModelPath('pendaftarbeasiswa'));
require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));

$a_input = array();
$a_input[] = array('kolom' => 'potensidiri', 'label' => 'Jumlah anak dalam keluarga', 'type' => 'N','maxlength' => 5, 'size' => '5px');


$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];


//data post edit
$r_post = array();
$r_post['potensidiri'] = $q[3];


if($r_act == 'saveedit')
{
	//$conn->debug=true;
	//$check = mPendidikanBeasiswa::getData($conn,$r_subkey);

	//var_dump($check);die;
	
	$record2 = array();
	foreach($r_post as $i=>$v){
		$record2[$i] = $v;
	}
	list($p_posterr,$p_postmsg) = mPengajuanBeasiswaPd::updateRecord($conn,$record2,$r_subkey,true);	

}

$row = mPendaftarBeasiswa::getDataAnak($conn,$r_key);
?>
<?php if($r_act == 'editdetail') { ?>

<table class="table table-bordered table-striped">
	<tr>
		<td colspan="3" class="DataBG">
			Data Potensi Diri
			<div class="pull-right">	
				<button id="<?=$r_subkey?>" act="saveedit" class="btn btn-success" type="button" name="saveedit" onclick="saveDetailPotensi(this)"> 
					Simpan
				</button>	
				<button id="<?=$r_subkey?>" act="bataledit" class="btn btn-warning" type="button" name="bataledit" onclick="batalDetailPotensi(this)"> 
					Batal Edit
				</button>	
			</div>
		</td>
	</tr>	
	<tr>
		<td class="LeftColumnBG" width="220">Tuliskan Visi Misi Anda</td>
		<td class="RightColumnBG"><?= UI::createTextArea('potensidiri',$row['potensidiri'],'',4,50,true); ?></td>
	</tr>	
</table>
<?PHP }  else { ?>
<table class="table table-bordered table-striped">
	<tr>
		<td colspan="3" class="DataBG">
			Data Potensi Diri
			<div class="pull-right">	
				<button id="<?=$r_subkey?>" act="editdetail" class="btn btn-primary" type="button" name="editdetail" onclick="editDetailPotensi(this)"> 
					Edit
				</button>
			</div>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220">Tuliskan Visi Misi Anda</td>
		<td class="RightColumnBG"><?= UI::createTextArea('potensidiri',$row['potensidiri'],'',4,50,false); ?></td>
	</tr>	
</table>
<?php } ?>
<script>
function editDetailPotensi(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act

	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpotensi", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-potensi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function saveDetailPotensi(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#potensidiri").val();
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpotensi", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-potensi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function batalDetailPotensi(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpotensi", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-potensi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
