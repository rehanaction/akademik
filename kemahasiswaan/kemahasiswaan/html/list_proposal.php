<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('proposal'));
	require_once(Route::getModelPath('organisasi'));
	require_once(Route::getUIPath('combo'));

	// combo
	$a_periode = array('' => '-- Semua Periode --') + mCombo::periode($conn);
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE',$a_periode);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');

	$a_organisasi = array('' => '-- Semua Organisasi --') + mOrganisasi::getArray($conn);
	$r_organisasi = Modul::setRequest($_POST['organisasi'],'ORGANISASI',$a_organisasi);
	$l_organisasi = UI::createSelect('organisasi',$a_organisasi,$r_organisasi,'ControlStyle',true,'onchange="goSubmit()"');

	$r_tanggal = Modul::setRequest($_POST['tanggal'],'PROPOSAL.TANGGAL');
	$r_sdtanggal = Modul::setRequest($_POST['sdtanggal'],'PROPOSAL.SDTANGGAL');

	// properti halaman
	$p_title = 'Daftar Proposal';
	$p_tbwidth = 900;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	$p_calendar = true;

	$p_model = mProposal;

	// struktur view
	$a_periode = mCombo::periode($conn);

	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nosurat', 'label' => 'Nomor Surat');
	$a_kolom[] = array('kolom' => 'namaorganisasi', 'label' => 'Nama Organisasi');
	$a_kolom[] = array('kolom' => 'namaprogram', 'label' => 'Nama Kegiatan');
	$a_kolom[] = array('kolom' => 'tglpermohonan', 'label' => 'Tgl. Permohonan', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status');

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);

		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	Modul::refreshList();

	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);

	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('p.periode',$r_periode);
	if(!empty($r_organisasi)) $a_filter[] = $p_model::getListFilter('p.kodeorganisasi',$r_organisasi);
	if(!empty($r_tanggal)) $a_filter[] = $p_model::getListFilter('fromtanggal',$r_tanggal);
	if(!empty($r_sdtanggal)) $a_filter[] = $p_model::getListFilter('sdtanggal',$r_sdtanggal);
	if(Akademik::isMhs())
		$a_filter[] = $p_model::getListFilter('nimpengaju',Modul::getUserName());
	else {
		$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());
	}
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);

	// membuat filter
	$x_tanggal = UI::createTextBox('tanggal',$r_tanggal,'ControlStyle',10,8);
	$x_tanggal .= ' <img src="images/cal.png" id="tanggal_trg" style="cursor:pointer" title="Pilih tanggal mulai">';
	$x_tanggal .= ' &nbsp;s.d.&nbsp; '.UI::createTextBox('sdtanggal',$r_sdtanggal,'ControlStyle',10,8);
	$x_tanggal .= ' <img src="images/cal.png" id="sdtanggal_trg" style="cursor:pointer" title="Pilih tanggal selesai">';
	$x_tanggal .= ' &nbsp;<input type="button" value="Pilih" onclick="goSubmit()">';

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Nama Organisasi', 'combo' => $l_organisasi);
	$a_filtercombo[] = array('label' => 'Tgl. Permohonan', 'combo' => $x_tanggal);

	require_once(Route::getViewPath('inc_list'));
?>
<script type="text/javascript">
	$(document).ready(function() {
		Calendar.setup({
			inputField	: "tanggal",
			ifFormat	: "%d-%m-%Y",
			button		: "tanggal_trg",
			align		: "Br",
			singleClick	: true
		});

		Calendar.setup({
			inputField	: "sdtanggal",
			ifFormat	: "%d-%m-%Y",
			button		: "sdtanggal_trg",
			align		: "Br",
			singleClick	: true
		});
	});
</script>
