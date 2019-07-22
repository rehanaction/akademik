<?
require_once(Route::getModelPath('pendaftarbeasiswa'));
require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));

$a_input = array();
$a_input[] = array('kolom' => 'jumlahanakkeluarga', 'label' => 'Jumlah anak dalam keluarga', 'type' => 'N','maxlength' => 5, 'size' => '5px');
$a_input[] = array('kolom' => 'anakke', 'label' => 'Anda anak ke', 'type' => 'N','maxlength' => 5, 'size' => 5);


$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];


//data post edit
$r_post = array();
$r_post['jumlahanakkeluarga'] = $q[4];
$r_post['anakke'] = $q[5];


if($r_act == 'saveedit')
{
	$conn->debug=true;
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
<table class="table table-bordered table-striped">
	<tr>
		<td class="LeftColumnBG" width="220">Jumlah anak dalam keluarga</td>
		<td class="RightColumnBG"><?= UI::createTextBox('jumlahanakkeluarga',$row['jumlahanakkeluarga'],'',4,50,false); ?></td>
	</tr>	
	<tr>
		<td class="LeftColumnBG">Anda anak ke</td>
		<td class="RightColumnBG"><?= UI::createTextBox('anakke',$row['jumlahanakkeluarga'],'',4,50,false); ?></td>
	</tr>	
</table>
