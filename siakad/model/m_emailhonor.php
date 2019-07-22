<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	global $conf;
	require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
	require_once(Route::getModelPath('email'));
	
	class mEmailHonor extends mEmail {
		const schema = 'akademik';
		
		function kirimHonor($conn,$conn_sdm,$r_nip,$report){
			
			ob_start();
			$_REQUEST['nipdosen']=$r_nip;
			include(Route::getViewPath($report));
			$content = ob_get_contents();
			ob_end_clean();
			
			$tujuan=$conn_sdm->GetOne("select coalesce(email,emailpribadi) from sdm.ms_pegawai where nodosen='$r_nip'");
			//$tujuan = 'dayat.developer@gmail.com';
			$subject='Slip Rekap Honor';
			list($err,$msg)=static::sendMail($tujuan,$subject,$content);
			//echo $content;
			return array($err,$msg);
		}
		
		function kirimHonorMengajar($conn,$conn_sdm,$r_nip){
			
			$report='rep_rekapslipgaji';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$r_nip,$report);
			
			return array($err,$msg);
		}
		
		function kirimHonorSoal($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonorsoal';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		
		function kirimHonorKoreksi($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonorkoreksi';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		
		function kirimHonorPengawas($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonorpengawas';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorDpa($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonordpa';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorPembMagang($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonormagang';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorBimbMagang($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonormagang2';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorPembTa($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonorta';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorPenguji($conn,$conn_sdm,$nip){
			
			$report='rep_rekapsliphonorpenguji';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
		function kirimHonorTransPenguji($conn,$conn_sdm,$nip){
			global $conf;
			$report='rep_rekapsliphonortranspenguji';
			list($err,$msg)=static::kirimHonor($conn,$conn_sdm,$nip,$report);
			
			return array($err,$msg);
			
		}
	}
?>
