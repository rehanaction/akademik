<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Cetak Sertifikat Seminar';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode = array('' => '-- Pilih Periode --') + mCombo::periode($conn);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => $l_periode);
	$a_input[] = array('kolom' => 'idseminar', 'label' => 'Seminar', 'type' => 'S', 'option' => array('' => '-- Pilih Periode terlebih dahulu --'), 'add' => 'style="width:300px"');
	$a_input[] = array('kolom' => 'nopeserta','label' => 'No Peserta');
	
	ob_start();
?>
<script type="text/javascript" src="scripts/jquery.ajax.js"></script>
<script type="text/javascript">
	$("#periode").change(function() {
		$("#idseminar").xhrSetOption("f=optseminar&q=" + $(this).val(), "Seminar");
	});
</script>
<?php
	$p_js = ob_get_contents();
	ob_end_clean();
	
	require_once($conf['view_dir'].'inc_repp.php');
?>