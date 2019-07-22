<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
	    function aktif(){
	        return array('1' => 'Aktif', '0' => 'Non Aktif');
	    }
	    
		function cabang($conn) {
			$sql = "select idcabang, namacabang from aset.ms_cabang order by idcabang";
			return Query::arrQuery($conn,$sql);
		}
		
		function coa($conn,$isleaf=true) {
			$sql = "select idcoa, idcoa+' - '+namacoa as coa from aset.ms_coa ";
			if($isleaf)
			    $sql .= "where level = 5 ";
			$sql .= "order by idcoa";
			return Query::arrQuery($conn,$sql);
		}
		
		function dasarharga($conn) {
			$sql = "select iddasarharga, dasarharga from aset.ms_dasarharga order by iddasarharga";
			return Query::arrQuery($conn,$sql);
		}
		
		function gedung($conn,$idcabang='') {
			$sql = "select idgedung, namagedung from aset.ms_gedung ";
			if(!empty($idcabang))
    			$sql .= "where idcabang = '$idcabang' ";
			$sql .= "order by idgedung";
			return Query::arrQuery($conn,$sql);
		}
		
		function barang($conn){
			$sql = "select idbarang1, idbarang1+' - '+namabarang namabarang from aset.ms_barang1 where level > 3 and isaktif = 1";
			return Query::arrQuery($conn,$sql);
		}

		function brgstock($conn){
			$sql = "select s.idbarang1, b.namabarang as namabarang 
			    from aset.as_stockhp s 
			    join aset.ms_barang1 b on s.idbarang1 = b.idbarang1
			    where s.jmlstock > 0 order by b.namabarang";
			return Query::arrQuery($conn,$sql);
		}
		
		function barangkib(){
		    return array('201' => 'KIB Tanah', '301' => 'KIB Bangunan', '300' => 'KIB Alat Teknis', '401' => 'KIB Kendaraan');
		}
		
		function lantai(){
	        return array('1' => 'Lantai 1',
	                    '2' => 'Lantai 2',
	                    '3' => 'Lantai 3',
	                    '4' => 'Lantai 4',
	                    '5' => 'Lantai 5',
	                    '6' => 'Lantai 6',
	                    '7' => 'Lantai 7',
	                    '8' => 'Lantai 8',
	                    '9' => 'Lantai 9',
	                    '10' => 'Lantai 10'
	                    );
        }
        
		function levelbarang($all=true){
		    if($all) 
		        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5','6' => 'Level 6');
            else 
		        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5');
		}
		
		function levelcoa(){
	        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5');
        }
        
		function lokasi($conn,$idunit=''){
		    $lokasi = array();
		    
			$sql = "select idlokasi, idlokasi+' - '+namalokasi 
			    from aset.ms_lokasi ";
		    if($idunit != '')
		        $sql .= "where idunit = '$idunit' ";
		    $sql .= "order by idlokasi";
			$lokasi = Query::arrQuery($conn,$sql);
			
			if(count($lokasi) == 0)
			    $lokasi = array('' => '-- Lokasi tidak ditemukan --');
		    
		    return $lokasi;
		}

		function lokasibrg($conn,$idunit=''){
		    $lokasi = array();
		    
			$sql = "select s.idlokasi, s.idlokasi+' - '+l.namalokasi 
			    from aset.as_seri s join aset.ms_lokasi l on l.idlokasi = s.idlokasi ";
		    if($idunit != '')
		        $sql .= "where s.idunit = '$idunit' ";
		    $sql .= "group by s.idlokasi,l.namalokasi order by idlokasi";
			$lokasi = Query::arrQuery($conn,$sql);
			
			if(count($lokasi) == 0)
			    $lokasi = array('' => '-- Lokasi tidak ditemukan --');
		    
		    return $lokasi;
		}
		
		function jenislokasi($conn) {
			$sql = "select idjenislokasi, jenislokasi from aset.ms_jenislokasi order by jenislokasi";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispenghapusan($conn) {
			$sql = "select idjenispenghapusan, jenispenghapusan from aset.ms_jenispenghapusan order by idjenispenghapusan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispenyusutan($conn) {
			$sql = "select idjenispenyusutan, jenispenyusutan from aset.ms_jenispenyusutan order by idjenispenyusutan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisperolehan($conn) {
			$sql = "select idjenisperolehan, jenisperolehan from aset.ms_jenisperolehan order by idjenisperolehan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisperolehanhp($conn) {
			$sql = "select idjenistranshp, jenistranshp from aset.ms_jenistranshp where tok = 'T' order by idjenistranshp";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispengeluaranhp($conn) {
			$sql = "select idjenistranshp, jenistranshp from aset.ms_jenistranshp where tok = 'K' order by idjenistranshp";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisrawat($conn) {
			$sql = "select idjenisrawat, jenisrawat from aset.ms_jenisrawat order by idjenisrawat";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenissupplier($conn) {
			$sql = "select idjenissupplier, jenissupplier from aset.ms_jenissupplier order by idjenissupplier";
			return Query::arrQuery($conn,$sql);
		}
		
		function kondisi($conn) {
			$sql = "select idkondisi, kondisi from aset.ms_kondisi order by idkondisi";
			return Query::arrQuery($conn,$sql);
		}
		
		function pemakai($conn,$idunit='') {
			$sql = "select s.idpegawai, p.namalengkap 
			    from aset.as_seri s left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai ";
		    if($idunit != '')
		        $sql .= "where s.idunit = '$idunit' ";
			$sql .= "group by s.idpegawai,p.namalengkap order by p.namalengkap";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuan($conn) {
			$sql = "select idsatuan, satuan from aset.ms_satuan order by satuan";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuanTujuan($conn,$key) {
		    if(!empty($key)){
			    $sql = "select idsatuan, satuan 
			        from aset.ms_satuan 
			        where idsatuan not in (select idasal from aset.ms_konversi where idbarang1 = '$key')
			        order by idsatuan";
			    return Query::arrQuery($conn,$sql);
		    }else return array();
		}
		
		function status($conn) {
			$sql = "select idstatus, status from aset.ms_status order by idstatus";
			return Query::arrQuery($conn,$sql);
		}
				
		function statusopnamehp(){
		    return array('D' => 'Draft', 'S' => 'Sah');
		}
		
		function statusopname(){
		    return array('D' => 'Draft', 'S' => 'Sah');
		}
		
		function statusproses(){
		    return array('A' => 'Diajukan', 'V' => 'Verified', 'P' => 'Proses' , 'T' => 'Ditolak' , 'S' => 'Disetujui');
		}
		
		function sumberdana($conn) {
			$sql = "select idsumberdana, sumberdana from aset.ms_sumberdana order by idsumberdana";
			return Query::arrQuery($conn,$sql);
		}
		
		function supplier($conn) {
			$sql = "select idsupplier, namasupplier from aset.ms_supplier order by idsupplier";
			return Query::arrQuery($conn,$sql);
		}

		function merk($conn) {
			$sql = "select merk from aset.ms_merk order by merk";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuanperiode() {
			return array('Jam' => 'Jam', 'Hari' => 'Hari', 'Minggu' => 'Minggu', 'Bulan' => 'Bulan', 'Tahun' => 'Tahun', 'Km' => 'Km');
		}
		
		// periode
		function tahun() {
			$data = array();
			for($i=date('Y')+1;$i>=date('Y')-10;$i--)
				$data[$i] = $i;
			
			return $data;
		}
		
		function bulan($full=true){
		    $bulan = array();
		    if($full){
			    $bulan['01'] = 'Januari';
			    $bulan['02'] = 'Pebruari';
			    $bulan['03'] = 'Maret';
			    $bulan['04'] = 'April';
			    $bulan['05'] = 'Mei';
			    $bulan['06'] = 'Juni';
			    $bulan['07'] = 'Juli';
			    $bulan['08'] = 'Agustus';
			    $bulan['09'] = 'September';
			    $bulan['10'] = 'Oktober';
			    $bulan['11'] = 'Nopember';
			    $bulan['12'] = 'Desember';
		    }else{
				$bulan['01'] = 'Jan';
				$bulan['02'] = 'Peb';
				$bulan['03'] = 'Mar';
				$bulan['04'] = 'Apr';
				$bulan['05'] = 'Mei';
				$bulan['06'] = 'Jun';
				$bulan['07'] = 'Jul';
				$bulan['08'] = 'Agu';
				$bulan['09'] = 'Sep';
				$bulan['10'] = 'Okt';
				$bulan['11'] = 'Nop';
				$bulan['12'] = 'Des';		    
		    }
			
			return $bulan;
		}
		
		function unitArray($conn) {
			$sql = "select idunit, kodeunit+' - '+namaunit from aset.ms_unit order by infoleft";
			return Query::arrQuery($conn,$sql);
		}
		
		function unitData($conn,$idunit){
		    return $conn->GetRow("select idunit,kodeunit,namaunit from aset.ms_unit where idunit = '$idunit'");
		}
		
		function unitByLeft($conn,$infoleft){
		    return $conn->GetRow("select idunit,kodeunit,namaunit from aset.ms_unit where infoleft = '$infoleft'");
		}
		
		function unit($conn,$dot=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select idunit, kodeunit, namaunit, level from aset.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			$minlevel = 10;
			while($row = $rs->FetchRow()) {
			    $level = (int)$row['level'];
                if($level < $minlevel)
    			    $minlevel = $level;
                
                $unit[$row['idunit']]['kodeunit'] = $row['kodeunit'];
                $unit[$row['idunit']]['namaunit'] = $row['namaunit'];
                $unit[$row['idunit']]['level'] = (int)$row['level'];
			}

			foreach($unit as $idunit => $row){
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				//$data[$idunit] = str_repeat($pref,(int)$row['level']-$minlevel).$row['kodeunit'].' - '.$row['namaunit'];
				$data[$idunit] = str_repeat($pref,(int)$row['level']-$minlevel).$row['namaunit'];
			}
			
			return $data;
		}
		
		function unitSave($conn,$dot=true,$all=false) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, level,idunit from aset.ms_unit ";
			if(!$all)
				$sql .=	"where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."' ";

			$sql .= "order by infoleft";

			$rs = $conn->Execute($sql);
			
			$data = array();
			$minlevel = 10;
			while($row = $rs->FetchRow()) {
				$level = (int)$row['level'];
                if($level < $minlevel)
    			    $minlevel = $level;
                
                $unit[$row['idunit']]['kodeunit'] = $row['kodeunit'];
                $unit[$row['idunit']]['namaunit'] = $row['namaunit'];
                $unit[$row['idunit']]['level'] = (int)$row['level'];
			}

			foreach($unit as $idunit => $row){
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$idunit] = str_repeat($pref,(int)$row['level']-$minlevel).$row['kodeunit'].' - '.$row['namaunit'];
			}
			
			return $data;
		}
		
		function coaSave($conn,$dot=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select idcoa, namacoa, level from aset.ms_coa order by idcoa, level";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['idcoa']] = str_repeat($pref,$row['level']).$row['namacoa'];
			}
			
			return $data;
		}
		
		function isverify(){
		    return array('0' => 'Belum Diverifikasi', '1' => 'Sudah Diverifikasi');
		}

		function level(){
		    return array('6' => 'Level 6','5' => 'Level 5', '4' => 'Level 4', '3' => 'Level 3', '2' => 'Level 2', '1' => 'Level 1');
		}

		function listMobil($conn){
			$sql = "select s.idbarang1, s.idbarang1+' - '+k.nopol+' - '+k.merk+' - '+k.tipe as mobil
				from aset.as_seri s
				left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1
				left join aset.as_kibkendaraan k on k.idseri = s.idseri
				where s.idbarang1 like '302010%' ";
			return Query::arrQuery($conn,$sql);
		}
	}
?>
