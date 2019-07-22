<?php 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// $a_auth = Modul::getFileAuth();
	
	
	//Hapus data sc_user yg rolenya mahasiswa
	// $data_mhs = $conn->Execute("select * from gate.sc_user su left join gate.sc_userrole ur on ur.userid=su.userid where ur.koderole='M'");
	// $loop=0;while($row = $data_mhs->FetchRow()){
		// $err = $conn->Execute("delete from gate.sc_userrole where userid='".$row['userid']."'");
		// $err2 = $conn->Execute("delete from gate.sc_user where userid='".$row['userid']."'");
		// $loop++;
	// }
	// echo "Berhasil dihapus: ".$loop;
	
	
	$data_mhs = $conn->Execute("select nama,nim,tgllahir,kodeunit from akademik.ms_mahasiswa order by nim");
	$loop=0;
	while($row = $data_mhs->FetchRow()){
		$record = array();
		$record['username'] = $row['nim'];
		$record['userdesc'] = $row['nama'];
		if($row['tgllahir'] == null){
			$record['password'] = md5('00000000');
			$record['hints'] = '00000000';
		}else{
			$record['password'] = md5(date('Ymd',strtotime($row['tgllahir'])));
			$record['hints'] = date('Ymd',strtotime($row['tgllahir']));
		}
		$record['email'] = $row['nama'];
		$record['isactive'] = 1;
		$err = Query::recInsert($conn,$record,'gate.sc_user');
		
		$rs_userid = $conn->GetOne("select userid from gate.sc_user where username='".$record['username']."'");
		$record2 = array();
		$record2['userid'] = $rs_userid;
		$record2['kodeunit'] =$row['kodeunit'];
		$record2['koderole'] ='M';
		$err = Query::recInsert($conn,$record2,'gate.sc_userrole');
		$loop++;
	}
	
	echo "Berhasil ditambahkan: ".$loop;
?>