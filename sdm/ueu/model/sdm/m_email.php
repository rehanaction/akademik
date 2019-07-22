<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
	require_once(Route::getModelPath('cuti'));
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getModelPath('model'));
	
	class mEmail extends mModel {
		const schema = 'sdm';
		
		// email dari atasan ke pegawai
		function confirmCuti($conn, $r_key) {
			global $conf;
			//mendapatkan data mail
			$sql = "select c.*, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama,p.nik,
					u.namaunit, p.email,p.idpegawaiatasan, m.jeniscuti
					from ".self::table('pe_rwtcuti')." c 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=c.idpegawai 
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_cuti')." m on m.idjeniscuti = c.idjeniscuti
					where c.nourutcuti=$r_key";
			$data = $conn->GetRow($sql);
			
			//namaatasan
			$atas = $conn->GetRow("select top 1 sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					st.jabatanstruktural
					from ".self::table('pe_rwtstruktural')." s
					left join ".self::table('ms_pegawai')." p on s.idpegawai = p.idpegawai
					left join ".self::table('ms_struktural')." st on st.idjstruktural = s.idjstruktural
					where s.idpegawai = '".$data['idpegawaiatasan']."' and s.isvalid = 'Y' and s.tmtmulai <= GETDATE() and COALESCE(s.tmtselesai,GETDATE()) >= GETDATE()
					order by coalesce(s.isutama, 'T') desc, s.tmtmulai desc");
			
			//select dari pe_rwtcutidetail
			$a_cutidet = mCuti::getCutiDetail($conn, $r_key);
			
			//status cuti
			$a_status = mCuti::statusSetujuiCuti();
			$msg = $a_status[$data['statususulan']];
			$color = array('S' => 'green','T' => 'red');
			
			//cuti yang sudah diambil untuk jenis cuti dan tahun bersangkutan
			$ambilcuti = mCuti::getAmbilCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			$sisacuti = mCuti::getSisaCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);

			$p_tbwidth = 1000;
			
			$isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.=' <tr>';
			$isi.='  <td></td>';
			$isi.='	 <td><font size="+2"><strong>'.$conf['univ_name'].'</strong></font></td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>'.$conf['univ_address'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>Telp. '.$conf['univ_telp'].', Fax : '.$conf['univ_fax'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>'.$conf['univ_email'].'</td>';
			$isi.=' </tr>';
			$isi.='</table>';
			
			$isi.='<hr style="width:"'.$p_tbwidth.'" align="left"><br>';
			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">Dengan Hormat,</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">Permohonan cuti Bapak/ Ibu</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">&nbsp;</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td width="250">Tanggal Permohonan</td>';
			$isi.='	 <td>: '.Cstr::formatDate($data['tglpengajuan']).'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Tahun</td>';
			$isi.='	 <td>: '.$data['tahun'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>NIK</td>';
			$isi.='	 <td>: '.$data['nik'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Nama Lengkap</td>';
			$isi.='	 <td>: '.$data['nama'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Unit Kerja</td>';
			$isi.='	 <td>: '.$data['namaunit'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Jenis Cuti</td>';
			$isi.='	 <td>: '.$data['jeniscuti'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td valign="top">Tanggal</td>';
			$isi.='	 <td>: ';
				$isi.='<table border="1" style="border-collapse:collapse;" width="300">';
				$isi.=' <tr>';
				$isi.='  <td align="center">Tanggal Mulai</td>';
				$isi.='  <td align="center">Tanggal Selesai</td>';
				$isi.='  <td align="center">Lama Cuti</td>';
				$isi.=' </tr>';
				
				if(count($a_cutidet)>0){
					foreach($a_cutidet as $rowd){
						$isi.=' <tr>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglmulai']).'</td>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglselesai']).'</td>';
						$isi.='  <td align="center">'.$rowd['lamacuti'].' hari</td>';
						$isi.=' </tr>';
					}
				}
				
				$isi.='</table>';
			$isi.='  </td>';
			$isi.=' </tr>';	
			$isi.=' <tr>';
			$isi.='	 <td>Lama Cuti</td>';
			$isi.='	 <td>: '.$data['lamacuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Untuk Keperluan</td>';
			$isi.='	 <td>: '.$data['alasancuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Keterangan Cuti</td>';
			$isi.='	 <td>: Anda sudah mengambil cuti '.$ambilcuti.' hari '.($sisacuti != '' ? ', sisa '.$sisacuti.' hari ' : '').'untuk periode ini</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';

			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.='	<tr>';
			$isi.='	 <td>Telah <font color="'.$color[$data['statususulan']].'"><b>'.strtolower($msg).'</b></font> pelaksanaannya pada tanggal '.Cstr::formatDate($data['tgldisetujui']).'</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>'.$atas['jabatanstruktural'].'</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>'.$atas['namalengkap'].'</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';
			
			$tujuan = $data['email'];//dikirimkan ke email pegawai
			$subject = 'Konfirmasi Pengajuan Cuti '.$data['jeniscuti'].' Oleh : '.$data['nik'].' - '.$data['nama'];
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();  // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->SMTPSecure = "ssl";
					$mail->Port       = 465;
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->Subject = $subject;
					$mail->Body = $isi;			
					$mail->Send();
					
					return true;
				} catch (phpmailerException $e) {
					return false;
				} catch (Exception $e) {
					return false;
				}		
			}		
		}
		
		//dikirimkan ke atasan/ pimpinan unit
		function requestCuti($conn, $r_key) {
			global $conf;
			//mendapatkan data mail
			$sql = "select c.*, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama,p.nik,
					s.namaunit, p.email,p.emailatasan, m.jeniscuti
					from ".self::table('pe_rwtcuti')." c 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=c.idpegawai 
					left join ".self::table('ms_unit')." s on s.idunit=p.idunit
					left join ".self::table('ms_cuti')." m on m.idjeniscuti = c.idjeniscuti
					where c.nourutcuti=$r_key";
			$data = $conn->GetRow($sql);
			
			//select dari pe_rwtcutidetail
			$a_cutidet = mCuti::getCutiDetail($conn, $r_key);
				
			//cuti yang sudah diambil dengan jenis cuti dan tahun yang bersangkutan
			$ambilcuti = mCuti::getAmbilCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			$sisacuti = mCuti::getSisaCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			
			$p_tbwidth = 1000;
			
			$isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td><font size="+2"><strong>'.$conf['univ_name'].'</strong></font></td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>'.$conf['univ_address'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>Telp. '.$conf['univ_telp'].', Fax : '.$conf['univ_fax'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td></td>';
			$isi.='	 <td>'.$conf['univ_email'].'</td>';
			$isi.=' </tr>';
			$isi.='</table>';
			
			$isi.='<hr style="width:"'.$p_tbwidth.'" align="left"><br>';
			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">Mohon keputusan atas pengajuan cuti pegawai/ pejabat di jajaran unit Bapak/ Ibu :</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">&nbsp;</td>';
			$isi.=' <tr>';
			$isi.=' <tr>';
			$isi.='	 <td width="250">Tanggal Permohonan</td>';
			$isi.='	 <td>: '.Cstr::formatDate($data['tglpengajuan']).'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Tahun</td>';
			$isi.='	 <td>: '.$data['tahun'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>NIK</td>';
			$isi.='	 <td>: '.$data['nik'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Nama Lengkap</td>';
			$isi.='	 <td>: '.$data['nama'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Unit Kerja</td>';
			$isi.='	 <td>: '.$data['namaunit'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Jenis Cuti</td>';
			$isi.='	 <td>: '.$data['jeniscuti'].'</td>';
			$isi.=' </tr>';	
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td valign="top">Tanggal</td>';
			$isi.='	 <td>: ';
				$isi.='<table border="1" style="border-collapse:collapse;" width="300">';
				$isi.=' <tr>';
				$isi.='  <td align="center">Tanggal Mulai</td>';
				$isi.='  <td align="center">Tanggal Selesai</td>';
				$isi.='  <td align="center">Lama Cuti</td>';
				$isi.=' </tr>';
				
				if(count($a_cutidet)>0){
					foreach($a_cutidet as $rowd){
						$isi.=' <tr>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglmulai']).'</td>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglselesai']).'</td>';
						$isi.='  <td align="center">'.$rowd['lamacuti'].' hari</td>';
						$isi.=' </tr>';
					}
				}
				
			$isi.='</table>';
			$isi.='  </td>';
			$isi.=' </tr>';	
			$isi.=' <tr>';
			$isi.='	 <td>Lama Cuti</td>';
			$isi.='	 <td>: '.$data['lamacuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Untuk Keperluan</td>';
			$isi.='	 <td>: '.$data['alasancuti'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Keterangan Cuti</td>';
			$isi.='	 <td>: Anda sudah mengambil cuti '.$ambilcuti.' hari '.($sisacuti != '' ? ', sisa '.$sisacuti.' hari ' : '').'untuk periode ini</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';
			
			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.='	<tr>';
			$isi.='	 <td>';
			$isi.='	 Silahkan melakukan melalui web : <a href="http://simueu.esaunggul.ac.id">simueu.esaunggul.ac.id</a>';
			$isi.='	 </td>';
			$isi.='</tr>';
			$isi.='</table>';
			
			$tujuan = $data['emailatasan'];//email atasan
			$subject = 'Pengajuan Cuti '.$data['jeniscuti'].' atas nama '.$data['nama'];
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->SMTPSecure = "ssl";
					$mail->Port       = 465;
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->Subject = $subject;
					$mail->Body = $isi;			
					$mail->Send();
						
					return true;
				} catch (phpmailerException $e) {
					return false;
				} catch (Exception $e) {
					return false;
				}
			}
		}
		
		function sendSlipGaji($conn,$r_key){
			//mengirimkan slip gaji ke email pegawai
			global $conf;
			
			list($r_periode,$idpegawai) = explode('|', $r_key);
						
			$sql = "select g.*,gp.namaperiode,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,p.email,
					s.jabatanstruktural,pd.namapendidikan,f.jabatanfungsional as fungsional,substring(gh.masakerja,1,2)+' thn. ' + substring(gh.masakerja,3,2)+' bln.' as mkgaji,
					l.upahlembur,gh.idtipepeg,gh.idjenispegawai
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ga_historydatagaji')." gh on gh.gajiperiode = g.periodegaji and gh.idpeg = g.idpegawai
					left join ".static::table('ga_periodegaji')." gp on gp.periodegaji=g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ga_upahlembur')." l on l.idpegawai=gh.idpeg and l.periodegaji=gh.gajiperiode
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('ms_unit')." u on u.idunit=gh.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan=p.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional = gh.fungsional
					where g.periodegaji = '$r_periode' and g.idpegawai = $idpegawai and g.isfinish = 'Y'";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[] = $row;
			}
			
			$a_tunj = mGaji::getTunjTetapSlipEmail($conn,$r_periode);
			$a_jtunj = mGaji::getTunjTetapGaji($conn);
			$a_jtunjdet = mGaji::getTunjTetapGajiDet($conn);
			$a_tunja = mGaji::getTunjAwalSlipEmail($conn,$r_periode);
			$a_jtunjawal = mGaji::getTunjTetapAwal($conn);
			$a_jtunjawaldet = mGaji::getTunjTetapAwalDet($conn);
			$a_ttunj = mGaji::getTunjPenyesuaianSlipEmail($conn,$r_periode);
			$a_jttunj = mGaji::getTunjPenyesuaian($conn);
			$a_pot = mGaji::getPotonganSlipEmail($conn,$r_periode);
			$a_jpot = mGaji::getJnsPotongan($conn);
	
			$a_tunjstrukturallain = mGaji::getTunjTetapStrukLainEmail($conn,$r_periode);
			$a_struktural = mGaji::getInfoStruktural($conn);
			
			//mengirimkan laporan potongan kehadiran karyawan ke email pegawai
			$tglgaji = $conn->GetRow("select tglawalhit,tglakhirhit,tglawalpotongan,tglakhirpotongan from ".static::table('ga_periodegaji')." where periodegaji = '$r_periode'");
			$r_tglmulai = $tglgaji['tglawalhit'];
			$r_tglselesai = $tglgaji['tglakhirhit'];
			
			$r_tglmulaipot = $tglgaji['tglawalpotongan'];
			$r_tglselesaipot = $tglgaji['tglakhirpotongan'];
			
			$a_detaildata = mGaji::getLapPotonganPresensi($conn,'',$r_tglmulaipot,$r_tglselesaipot,'');
			$a_datapeg = $a_detaildata['pegawai'];
			$a_potkehadirantransport = mGaji::getPotKehadiranTransport($conn,$r_tglmulaipot,$r_tglselesaipot,$r_sql);
			
			$p_tbwidth = 800;
			
			if(count($a_data)>0){
				foreach($a_data as $row){
					$isi.='<center>';
					$isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
					$isi.=' <tr>';
					$isi.='	 <td align="center"><font size="+2"><strong>'.$conf['univ_name'].'</strong></font></td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='	 <td align="center">'.$conf['univ_address'].'</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='	 <td align="center">Telp. '.$conf['univ_telp'].', Fax : '.$conf['univ_fax'].'</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='	 <td align="center">'.$conf['univ_email'].'</td>';
					$isi.=' </tr>';
					$isi.='</table>';
					$isi.='</center>';
					$isi.='</br>';
					
					$isi.='<table width="'.$p_tbwidth.'" style="border:1px solid" cellpadding="4" cellspacing="0">';
					$isi.=' <tr>';
					$isi.=' <td>Periode</td>';
					$isi.=' <td width="30">:</td>';
					$isi.=' <td colspan="4"><b>'.$row['namaperiode'].'</b></td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='	 <td>Nama</td>';
					$isi.='	 <td>:</td>';
					$isi.='  <td colspan="4"><b>'.$row['namapegawai'].'</b></td>';
					$isi.='	</tr>';
					$isi.='	<tr>';
					$isi.='	 <td>Jabatan</td>';
					$isi.='  <td>:</td>';
					$isi.='  <td colspan="4">'.$row['jabatanstruktural'].'</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='  <td>Pendidikan</td>';
					$isi.='   <td>:</td>';
					$isi.='   <td colspan="4">'.$row['namapendidikan'].'</td>';
					$isi.=' </tr>';
					
					if($row['idtipepeg'] == 'D' or $row['idtipepeg'] == 'AD'){
						$isi.=' <tr>';
						$isi.='  <td>Fungsional</td>';
						$isi.='  <td>:</td>';
						$isi.='  <td colspan="4">'.$row['fungsional'].'</td>';
						$isi.=' </tr>';
					}
					
					$isi.=' <tr>';
					$isi.='  <td>Masa Kerja</td>';
					$isi.='  <td>:</td>';
					$isi.='  <td colspan="4">'.$row['mkgaji'].'</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='  <td colspan="6">&nbsp;</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='  <td>Gaji Pokok</td>';
					$isi.='  <td>: Rp.</td>';
					$isi.='  <td align="right" style="padding-right:30px">'.CStr::formatNumber($row['gapok']).'</td>';
					$isi.='  <td width="50%"colspan="3">&nbsp;</td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='  <td colspan="3">Tunjangan</td>';
					$isi.='  <td colspan="3">Potongan</td>';
					$isi.=' </tr>';
					$isi.=' <tr style="border:none">';
					$isi.='  <td colspan="3" valign="top" style="border:none" width="50%">';
					$isi.='   <table width="95%" cellpadding="4" cellspacing="0">';
					
					if(count($a_jtunj)>0){
						$tunjangantetap = !empty($row['gapok']) ? $row['gapok'] : 0;
						foreach($a_jtunj as $ikey=>$val){
							if($ikey == $a_jtunjdet[$row['idjenispegawai']][$ikey]){
								$tunjangantetap += $a_tunj[$ikey][$row['idpegawai']];
									$isi.='<tr>';
									$isi.='<td width="50%">- '.$val.'</td>';
									$isi.='<td width="10%">: Rp.</td>';
									$isi.='<td width="35%" align="right">'.CStr::formatNumber($a_tunj[$ikey][$row['idpegawai']]).'</td>';
									$isi.='</tr>';
									
									
							}
								
							$tunjstrukturallain = $a_tunjstrukturallain[$ikey][$row['idpegawai']];				
							if(count($tunjstrukturallain)>0){
								foreach($tunjstrukturallain as $keystruk=>$valstruk){
									$tunjangantetap += $valstruk;
									$isi.='<tr>';
									$isi.='<td width="50%">- Tunjangan Struktural Lain ('.$a_struktural[$keystruk].')</td>';
									$isi.='<td width="10%">: Rp.</td>';
									$isi.='<td width="35%" align="right">'.CStr::formatNumber($valstruk).'</td>';
									$isi.='</tr>';
								}
							}
						}
					}
					
					$isi.='<tr>';
					$isi.='<td colspan="2">&nbsp;</td>';
					$isi.='<td valign="bottom" align="right">________________ +</td>';
					$isi.='</tr>';
					
							$sistem = $tunjangantetap;
							
					$isi.='<tr style="font-weight:bold">';
					$isi.='<td width="50%" align="right">SISTEM</td>';
					$isi.='<td width="10%">: Rp.</td>';
					$isi.='<td width="35%" align="right">'.CStr::formatNumber($sistem).'</td>';
					$isi.='</tr>';					
					$isi.='  </table>';
					$isi.='  <br>';
					
					$isi.='  <table width="95%" cellpadding="4" cellspacing="0">';
					
					if(count($a_jttunj)>0){
						$tunjangantdtetap=0;
						foreach($a_jttunj as $ikey=>$val){
							$tunjangantdtetap += $a_ttunj[$ikey][$row['idpegawai']];
							$isi.='<tr>';
							$isi.=' <td width="50%">- '.$val.'</td>';
							$isi.=' <td width="10%">: Rp.</td>';
							$isi.='	<td width="35%" align="right">'.CStr::formatNumber($a_ttunj[$ikey][$row['idpegawai']]).'</td>';
							$isi.='</tr>';
						}
					}
					
					if($row['idtipepeg']=='A'){
						$isi.='<tr>';
							$isi.='<td width="50%">- Lembur</td>';
							$isi.='<td width="10%">: Rp.</td>';
							$isi.='<td width="35%" align="right">'.CStr::formatNumber($row['upahlembur']).'</td>';
						$isi.='</tr>';
					}
					
					$isi.='<tr>';
						$isi.='<td colspan="2">&nbsp;</td>';
						$isi.='<td valign="bottom" align="right">________________ +</td>';
					$isi.='</tr>';
					
						$bruto = $sistem + $tunjangantdtetap;
						if($row['idtipepeg']=='A')
							$bruto += $row['upahlembur'];
							
					$isi.='<tr style="font-weight:bold">';
						$isi.='<td width="50%" align="right">BRUTO</td>';
						$isi.='<td width="10%">: Rp.</td>';
						$isi.='<td width="35%" align="right">'.CStr::formatNumber($bruto).'</td>';
					$isi.='</tr>';
					$isi.='</table>';
										
					$isi.=' </td>';
					$isi.='	<td colspan="3" valign="top" style="border:none" width="50%">';
					$isi.='	 <table width="97%" cellpadding="4" cellspacing="0">';
					
					if(count($a_jpot)>0){
						$potongan = 0;
						foreach($a_jpot as $ikey=>$val){
							$potongan += $a_pot[$ikey][$row['idpegawai']];
								$isi.='<tr>';
								$isi.=' <td width="50%">- '.$val.'</td>';
								$isi.=' <td width="10%">: Rp.</td>';
								$isi.='	<td width="37%" align="right">'.CStr::formatNumber($a_pot[$ikey][$row['idpegawai']]).'</td>';
								$isi.='</tr>';
						}
					}
					$isi.='    <tr>';
					$isi.='     <td width="50%">- PPh Ps. 21</td>';
					$isi.='	    <td width="10%">: Rp.</td>';
					$isi.='	    <td width="37%" align="right">'.CStr::formatNumber($row['pph']).'</td>';
					$isi.='    </tr>';
					$isi.='    <tr>';
					$isi.='		<td colspan="2">&nbsp;</td>';
					$isi.='		<td valign="bottom" align="right">________________ +</td>';
					$isi.='	   </tr>';
							$totpotongan = $potongan + $row['pph'];
					$isi.='	   <tr>';
					$isi.='		<td width="50%">Total Potongan</td>';
					$isi.='		<td width="10%">: Rp.</td>';
					$isi.='		<td width="37%" align="right">'.CStr::formatNumber($totpotongan).'</td>';
					$isi.='	   </tr>';
					$isi.='	   <tr>';
					$isi.='		<td colspan="3">&nbsp;</td>';
					$isi.='	   </tr>';
							$netto = $bruto - $totpotongan;
					$isi.='	   <tr style="font-weight:bold">';
					$isi.='	    <td width="50%" align="right">NETTO</td>';
					$isi.='		<td width="12%">: Rp.</td>';
					$isi.='		<td width="35%" align="right">'.CStr::formatNumber($netto).'</td>';
					$isi.='	   </tr>';
					$isi.='	   <tr>';
					$isi.='		<td colspan="3">&nbsp;</td>';
					$isi.='	   </tr>';
					$isi.='	   <tr>';
					$isi.='		<td width="50%">Pengembalian PPh Ps. 21</td>';
					$isi.='		<td width="10%">: Rp.</td>';
					$isi.='		<td width="37%" align="right">'.CStr::formatNumber($row['pph']).'</td>';
					$isi.='	    </tr>';
					$isi.='	    <tr>';
					$isi.='		 <td colspan="2">&nbsp;</td>';
					$isi.='		 <td valign="bottom" align="right">________________ +</td>';
					$isi.='		</tr>';
							$gajiditerima = $netto + $row['pph'];
					$isi.='		<tr style="font-weight:bold">';
					$isi.='		 <td width="50%" align="right">Gaji Diterima</td>';
					$isi.='		 <td width="12%">: Rp.</td>';
					$isi.='		 <td width="35%" align="right">'.CStr::formatNumber($gajiditerima).'</td>';
					$isi.='		</tr>';
					$isi.='	   </table>';
					$isi.='	  </td>';
					$isi.=' </tr>';					
					$isi.=' <tr>';
					$isi.='	 <td colspan="6">&nbsp;</td>';
					$isi.=' </tr>';
					
					if(count($a_jtunjawaldet)>0){
						foreach($a_jtunjawaldet as $jpeg){
							if($jpeg == $row['idjenispegawai']){
							
					$isi.=' <tr>';
					$isi.='  <td><b>Tunjangan sudah dibayarkan</b></td>';
					$isi.='  <td colspan="5">&nbsp;</td>';
					$isi.=' </tr>';
					
					if(count($a_jtunjawal)>0){
						$gajiawal = 0;
						foreach($a_jtunjawal as $ikey=>$val){
							$gajiawal += $a_tunja[$ikey][$row['idpegawai']];
							$isi.='<tr>';
							$isi.=' <td width="25%">- '.$val.'</td>';
							$isi.=' <td width="5%">: Rp.</td>';
							$isi.=' <td width="20%" align="right" style="padding-right:30px">'.CStr::formatNumber($a_tunja[$ikey][$row['idpegawai']]).'</td>';
							$isi.=' <td colspan="3">&nbsp;</td>';
							$isi.='</tr>';
						}
					}
					
					$isi.='<tr>';
					$isi.=' <td colspan="2">&nbsp;</td>';
					$isi.=' <td valign="bottom" align="center">________________ +</td>';
					$isi.=' <td colspan="3">&nbsp;</td>';
					$isi.='</tr>';
					$isi.='<tr>';
					$isi.=' <td width="25%"><b>Total</b></td>';
					$isi.='	<td width="5%">: Rp.</td>';
					$isi.=' <td width="20%" align="right" width="20%" style="padding-right:30px">'.CStr::formatNumber($gajiawal).'</td>';
					$isi.=' <td colspan="3">&nbsp;</td>';
					$isi.='</tr>';
					}}}
					
					$isi.='</table>';
					/*
					$isi.='</br></br></br>';
					
					$p_tbwidthnew = $p_tbwidth+200;
					
					$isi.='<table cellpadding="4" cellspacing="0" width="'.$p_tbwidthnew.'">';
					$isi.=' <tr>';
					$isi.='	 <td align="center"><font size="+1"><strong>Laporan Potongan Kehadiran Karyawan</strong></font></td>';
					$isi.=' </tr>';
					$isi.=' <tr>';
					$isi.='	 <td align="center"><strong>Periode '.CStr::formatDateInd($r_tglmulaipot).' s/d '.CStr::formatDateInd($r_tglselesaipot).'</strong></td>';
					$isi.=' </tr>';
					$isi.='</table>';
					$isi.='</br>';
					
					$isi.='<table width="'.$p_tbwidthnew.'" style="border-collapse:collapse;" border="1" cellpadding="4" cellspacing="0">';			
					$isi.='	<tr bgcolor = "gray">';
					$isi.='		<th rowspan = "2" width="90"><b style = "color:#FFFFFF">TGL. ABSENSI</b></th>';
					$isi.='		<th colspan = "2"><b style = "color:#FFFFFF">JAM KERJA</b></th>';
					$isi.='		<th rowspan = "2"><b style = "color:#FFFFFF">JAM MASUK</b></th>';
					$isi.='		<th rowspan = "2"><b style = "color:#FFFFFF">JAM KELUAR</b></th>';
					$isi.='		<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT DATANG</b></th>';
					$isi.='		<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT PULANG</b></th>';
					$isi.='		<th rowspan = "2"><b style = "color:#FFFFFF">ALASAN</b></th>';
					$isi.='		<th rowspan = "2"><b style = "color:#FFFFFF">JML JAM KERJA</b></th>';
					$isi.='		<th colspan = "3"><b style = "color:#FFFFFF">TRANSPORT</b></th>';
					$isi.='		<th colspan = "3"><b style = "color:#FFFFFF">KEHADIRAN</b></th>';
					$isi.='		<th rowspan = "2"><b style = "color:#FFFFFF">TOTAL POTONGAN</b></th>';
					$isi.='	</tr>';
					$isi.='	<tr bgcolor ="gray">';				
					$isi.='		<th><b style = "color:#FFFFFF">MASUK</b></th>';							
					$isi.='		<th><b style = "color:#FFFFFF">PULANG</b></th>';
					$isi.='		<th><b style = "color:#FFFFFF">CEPAT</b></th>';							
					$isi.='		<th><b style = "color:#FFFFFF">TELAT</b></th>';
					$isi.='		<th><b style = "color:#FFFFFF">CEPAT</b></th>';							
					$isi.='		<th><b style = "color:#FFFFFF">LEMBUR</b></th>';
					$isi.='		<th><b style = "color:#FFFFFF">NILAI TRANS. (Rp)</b></th>';							
					$isi.='		<th><b style = "color:#FFFFFF">POT TRANS. (%)</b></th>';
					$isi.='		<th><b style = "color:#FFFFFF">POT TRANS. (Rp)</b></th>';						
					$isi.='		<th><b style = "color:#FFFFFF">NILAI HADIR. (Rp)</b></th>';
					$isi.='		<th><b style = "color:#FFFFFF">POT HADIR. (%)</b></th>';						
					$isi.='		<th><b style = "color:#FFFFFF">POT HADIR. (Rp)</b></th>';
					$isi.='	</tr>';
					
					if (count($a_datapeg[$row['idpegawai']]) > 0) {
						$totalpot = 0;
						foreach($a_datapeg[$row['idpegawai']] as $r):
							$jdatangc='';$mdatangc='';
							if($r['menitdatang'] < 0){
								if(abs($r['menitdatang']) > 60){
									$jdatangc = floor(abs($r['menitdatang'])/60) .' jam ';
									$mdatangc = abs($r['menitdatang'])%60 .' menit';
								}else{
									$mdatangc = abs($r['menitdatang']).' menit';
								}
							}
							
							$jdatangt='';$mdatangt='';
							if($r['menitdatang'] > 0){
								if($r['menitdatang'] > 60){
									$jdatangt = floor($r['menitdatang']/60) .' jam ';
									$mdatangt = $r['menitdatang']%60 .' menit';
								}else{
									$mdatangt = $r['menitdatang'].' menit';
								}
							}
							
							$jpulangc='';$mpulangc='';
							if($r['menitpulang'] < 0){
								if(abs($r['menitpulang']) > 60){
									$jpulangc = floor(abs($r['menitpulang'])/60) .' jam ';
									$mpulangc = abs($r['menitpulang'])%60 .' menit';
								}else{
									$mpulangc = abs($r['menitpulang']).' menit';
								}
							}
							
							$jpulangt='';$mpulangt='';
							if($r['menitpulang'] > 0){
								if($r['menitpulang'] > 60){
									$jpulangt = floor($r['menitpulang']/60) .' jam ';
									$mpulangt = $r['menitpulang']%60 .' menit';
								}else{
									$mpulangt = $r['menitpulang'].' menit';
								}
							}
							
							$stgl = strtotime($r['tglpresensi']);
							$elemen=date("w",$stgl);
							
							$procpottransport = $a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['procpottransporttelat']+$a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['procpottransportpd'];
							$procpotkehadiran = $a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['procpotkehadirantelat']+$a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['procpotkehadiranpd'];
					
							$totalpot = $a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['pottransport']+$a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['potkehadiran'];
							
						$isi.='<tr>';
						$isi.='	<td>'.CStr::formatDateTimeInd($r['tglpresensi'],false).'</td>';
						$isi.='	<td align="center">'.$r['sjamdatang2'].'</td>';
						$isi.='	<td align="center">'.$r['sjampulang2'].'</td>';
						$isi.='	<td align="center">'.$r['jamdatang2'].'</td>';
						$isi.='	<td align="center">'.$r['jampulang2'].'</td>';
						$isi.='	<td align="center">'.$jdatangc.$mdatangc.'</td>';
						$isi.='	<td align="center">'.$jdatangt.$mdatangt.'</td>';
						$isi.='	<td align="center">'.$jpulangc.$mpulangc.'</td>';
						$isi.='	<td align="center">'.$jpulangt.$mpulangt.'</td>';
						$isi.='	<td>'.$r['keterangan'].(($r['kodeabsensi'] != 'H' and $r['kodeabsensi'] != 'HL' and $r['kodeabsensi'] != 'B') ? ' ('.$r['absensi'].')' : '').'</td>';
						$isi.='	<td align="right" width="50">'.round($r['jamkerja'],2).'</td>';
						$isi.='	<td align="right">'.CStr::formatNumber($a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['tarifpottransport']).'</td>';
						$isi.='	<td align="right">'.$procpottransport.'</td>';
						$isi.='	<td align="right">'.CStr::formatNumber($a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['pottransport']).'</td>';
						$isi.='	<td align="right">'.CStr::formatNumber($a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['tarifpotkehadiran']).'</td>';
						$isi.='	<td align="right">'.$procpotkehadiran.'</td>';
						$isi.='	<td align="right">'.CStr::formatNumber($a_potkehadirantransport[$row['idpegawai']][$r['tglpresensi']]['potkehadiran']).'</td>';
						$isi.='	<td align="right">'.CStr::formatNumber($totalpot).'</td>';
						$isi.='</tr>';
						$total += $totalpot ; 
						endforeach;
						
						$isi.='<tr>';
						$isi.='	<td colspan="17" align="center"><b>Total</b>';
						$isi.='	<td align="right">'.CStr::formatNumber($total).'&nbsp;</td>';
						$isi.='</tr>';
					}else{
						$isi.='<tr>';
						$isi.='	<td align="center" colspan="18"><strong>Data tidak ditemukan</strong></td>';
						$isi.='</tr>';	
					}
					$isi.='</table>';
					$isi.='</br></br></br>';
					*/
					
					$tujuan = $row['email']; //email ke pegawai
					$subject = 'Slip Gaji dan Laporan Potongan Periode '.$row['namaperiode'].' atas nama '.$row['namapegawai'];
					
					if(!empty($tujuan)){
						$mail = new PHPMailer;
						$mail->IsSMTP();    // send via SMTP
						try {
							$mail->IsHTML(true);
							$mail->SMTPAuth = true;
							$mail->Host     = $conf['smtp_host']; // SMTP servers
							$mail->Username = $conf['smtp_user'];
							$mail->Password = $conf['smtp_pass'];
							$mail->SMTPSecure = "ssl";
							$mail->Port       = 465;
							$mail->AddAddress($tujuan);
							$mail->From = $conf['smtp_email'];
							$mail->FromName = $conf['smtp_admin'];
							$mail->Subject = $subject;
							$mail->Body = $isi;			
							$mail->Send();
								
							return true;
						} catch (phpmailerException $e) {
							return false;
						} catch (Exception $e) {
							return false;
						}
					}
				}
			}			
		}
		
		function berhasilRekrutmen($conn,$nopendaftar){
			global $conf;

			//email pendaftar
			$row = $conn->GetRow("select email,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namapelamar from sdm.re_calon where nopendaftar = '$nopendaftar'");	

			//isiemail
			$body = $conn->GetOne("select isiemail from sdm.re_settingemail where jenisemail = '2'");
			$body = str_replace('[[namapelamar]]', $row['namapelamar'], $body);

            $tujuan = $row['email'];
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['portalsmtp_host']; // SMTP servers
					$mail->Username = $conf['portalsmtp_user'];
					$mail->Password = $conf['portalsmtp_pass'];
					$mail->SMTPSecure = "ssl";
					$mail->Port       = 465;
					//$mail->ClearAddresses();
					$mail->From = $conf['portalsmtp_email'];
					$mail->FromName = $conf['portalsmtp_admin'];
					$mail->AddAddress($tujuan);
					$mail->Subject = '[Selamat Sukses] Pemberitahuan Hasil Seleksi Pegawai';
					$mail->Body = $body;			
					$mail->Send();
					
					return true;
				} catch (phpmailerException $e) {
					return false;
				} catch (Exception $e) {
					return false;
				}
			}
		}
		
		function gagalRekrutmen($conn,$nopendaftar){
			global $conf;

			//email pendaftar
			$row = $conn->GetRow("select email,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namapelamar from sdm.re_calon where nopendaftar = '$nopendaftar'");	

			//isiemail
			$body = $conn->GetOne("select isiemail from sdm.re_settingemail where jenisemail = '3'");
			$body = str_replace('[[namapelamar]]', $row['namapelamar'], $body);

            $tujuan = $row['email'];
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['portalsmtp_host']; // SMTP servers
					$mail->Username = $conf['portalsmtp_user'];
					$mail->Password = $conf['portalsmtp_pass'];
					$mail->SMTPSecure = "ssl";
					$mail->Port       = 465;
					//$mail->ClearAddresses();
					$mail->From = $conf['portalsmtp_email'];
					$mail->FromName = $conf['portalsmtp_admin'];
					$mail->AddAddress($tujuan);
					$mail->Subject = 'Pemberitahuan Hasil Seleksi Pegawai';
					$mail->Body = $body;			
					$mail->Send();
					
					return true;
				} catch (phpmailerException $e) {
					return false;
				} catch (Exception $e) {
					return false;
				}
			}
		}
		
		function tesMail(){
			global $conf;
			$tujuan = 'zainarif.syah@gmail.com';	
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->SMTPSecure = "ssl";
					$mail->Port       = 465;
					//$mail->ClearAddresses();
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->AddAddress($tujuan);
					$mail->Subject = 'tes Email';
					$mail->Body = 'asdfasdfasdf';			
					if($mail->Send()) {
					  echo "Message sent!";
					} else {
					  echo "Mailer Error: " . $mail->ErrorInfo;
					}
						
					return true;
				} catch (phpmailerException $e) {
					return false;
				} catch (Exception $e) {
					return false;
				}
			}
		}
	}
?>
