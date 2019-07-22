<?php
	// model laporan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLaporanHonor {
		
	function getHonorTransport($conn,$periode,$periodegaji,$nip=''){
		$sql="select h.nipdosen,h.honor,h.tglmengajar,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportdosen h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji'
			";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	function getRekapHonorTransport($conn,$periode,$periodegaji,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportdosen h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		$sql.=" group by h.nipdosen,namadosen order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanTransport($conn,$periode,$periodegaji){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportdosen h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji'
			group by h.nipdosen,namadosen";
			
		$sql.=" order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getHonorSoal($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$nopengajuan,$sistemkuliah,$nip=''){
		
		
		$sql="select k.kodemk,k.namamk,kl.kelasmk,j,tglujian,h.jeniskuliah,h.honor,h.nipdosen,h.nopengajuan,h.jenisujian,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_naskahujian h
			join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
			join akademik.ak_kurikulum k on h.thnkurikulum=k.thnkurikulum and h.kodeunit=k.kodeunit and h.kodemk=k.kodemk
			join akademik.ak_kelas kl on h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.periode=kl.periode and h.kodemk=kl.kodemk and h.kelasmk=kl.kelasmk";
		if(!empty($kodeunit)){	
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=h.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jenisujian))
			$sql.=" and h.jenisujian='$jenisujian'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah'";
				
		$sql.=" order by h.nipdosen,kl.kodemk,kl.kelasmk";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorSoal($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$nopengajuan,$sistemkuliah,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_naskahujian h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			join akademik.ak_kelas kl on h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.periode=kl.periode and h.kodemk=kl.kodemk and h.kelasmk=kl.kelasmk
			where h.isvalid=-1";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jenisujian))
			$sql.=" and h.jenisujian='$jenisujian'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah'";
			
		$sql.="	group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran";
			
		$sql.=" order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanSoal($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan,h.jenisujian
				from honorakademik.hn_naskahujian h
				join gate.ms_unit u on u.kodeunit=h.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan,h.jenisujian";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_naskahujian h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorKoreksi($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$jeniskuliah,$nopengajuan,$sistemkuliah,$nip=''){
		
		
		$sql="select k.kodemk,k.namamk,kl.kelasmk,j,tglujian,h.jeniskuliah,h.honor,h.nipdosen,h.nopengajuan,h.jenisujian,h.jumlahpeserta,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_koreksiujian h
			join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
			join akademik.ak_kurikulum k on h.thnkurikulum=k.thnkurikulum and h.kodeunit=k.kodeunit and h.kodemk=k.kodemk
			join akademik.ak_kelas kl on h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.periode=kl.periode and h.kodemk=kl.kodemk and h.kelasmk=kl.kelasmk";
			if(!empty($kodeunit)){	
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit u on u.kodeunit=h.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
			}
		$sql.="	join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1";
		
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jenisujian))
			$sql.=" and h.jenisujian='$jenisujian'";
		if(!empty($jeniskuliah))
			$sql.=" and h.jeniskuliah='$jeniskuliah'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		
			
		$sql.=" order by h.nipdosen,kl.kodemk,kl.kelasmk";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorKoreksi($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$jeniskuliah,$nopengajuan,$sistemkuliah,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_koreksiujian h
			join akademik.ak_kelas kl on h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.periode=kl.periode and h.kodemk=kl.kodemk and h.kelasmk=kl.kelasmk
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jeniskuliah))
			$sql.=" and h.jeniskuliah='$jeniskuliah'";
		if(!empty($jenisujian))
			$sql.=" and h.jenisujian='$jenisujian'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah' ";
		
			
		$sql.="	group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran";
			
		$sql.=" order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanKoreksi($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan,h.jenisujian,kl.sistemkuliah
				from honorakademik.hn_koreksiujian h
				join akademik.ak_kelas kl on h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.periode=kl.periode and h.kodemk=kl.kodemk and h.kelasmk=kl.kelasmk
				join gate.ms_unit u on u.kodeunit=h.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan,h.jenisujian,kl.sistemkuliah";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_koreksiujian h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorPengawas($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$nopengajuan,$nip=''){
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$kodeunit);
		
		$sql="select k.kodemk,k.namamk,k.sks,kl.kelasmk,j,tglujian,j.jeniskuliah,h.honor,h.nipdosen,j.jenisujian,h.nopengajuan,
			p.userdesc As namadosen
			from honorakademik.hn_pengawasujian h
			join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
			join akademik.ak_kurikulum k on j.thnkurikulum=k.thnkurikulum and j.kodeunit=k.kodeunit and j.kodemk=k.kodemk
			join akademik.ak_kelas kl on j.thnkurikulum=kl.thnkurikulum and j.kodeunit=kl.kodeunit and j.periode=kl.periode and j.kodemk=kl.kodemk and j.kelasmk=kl.kelasmk";
		
		if(!empty($kodeunit)){	
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit u on u.kodeunit=j.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
			}
		$sql.="	join gate.sc_user p on p.username::text=h.nipdosen::text where h.isvalid=-1";
		
		if(!empty($periode))
			$sql.=" and j.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jenisujian))
			$sql.=" and j.jenisujian='$jenisujian'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen,kl.kodemk,kl.kelasmk";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorPengawas($conn,$kodeunit,$periode,$periodegaji,$jenisujian,$nopengajuan,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			p.userdesc As namadosen
			from honorakademik.hn_pengawasujian h
			join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
			join gate.sc_user p on p.username::text=h.nipdosen::text
			where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and j.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($jenisujian))
			$sql.=" and j.jenisujian='$jenisujian'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.="	group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran";
			
		$sql.=" order by h.nipdosen";	
		
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	function getPemindahBukuanPengawas($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan,j.jenisujian
				from honorakademik.hn_pengawasujian h
				join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
				join gate.ms_unit u on u.kodeunit=j.kodeunit
				where h.isvalid=-1 and j.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan,j.jenisujian";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,
			p.userdesc As namadosen
			from honorakademik.hn_pengawasujian h
			join akademik.ak_jadwalujian j on h.idjadwalujian=j.idjadwalujian
			join gate.sc_user p on p.username::text=h.nipdosen::text
			where h.isvalid=-1 and j.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorDpa($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		
		
		$sql="select h.honor,h.nipdosen,m.nim,m.nama,h.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_dpa h
			join akademik.ak_perwalian pr on pr.nim=h.nim and pr.periode=h.periode
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorDpa($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_dpa h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			join akademik.ms_mahasiswa m on m.nim=h.nim
			where h.isvalid=-1
			";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah'";	
		$sql.=" group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanDpa($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_dpa h
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_dpa h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorPembMagang($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$nip=''){
		
		$sql="select h.honor,h.nipdosen,m.nim,m.nama,h.tglujianmagang,h.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingmagang h
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1 ";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorPembMagang($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
		akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingmagang h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1";
		
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";	
		$sql.=" group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanPembMagang($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_pembimbingmagang h
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingmagang h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorBimbinganMagang($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$nip=''){
		
		
		$sql="select h.honor,h.nipdosen,m.nim,m.nama,h.jumlahbimbingan,h.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_bimbinganmagang h
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1 ";
		
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorBimbinganMagang($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_bimbinganmagang h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1";
			
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";	
			
		$sql.=" group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanBimbinganMagang($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_bimbinganmagang h
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_bimbinganmagang h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorPembTa($conn,$kodeunit,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		
		$sql="select h.honor,h.nipdosen,m.nim,m.nama,h.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingskripsi h
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1 ";
		
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan'";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorPembTa($conn,$kodeunit,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingskripsi h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			join akademik.ms_mahasiswa m on m.nim=h.nim
			where h.isvalid=-1 ";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan'";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah'";
				
		$sql.=" group by h.nipdosen,namadosen,h.nopengajuan,h.nopembayaran order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahbukuanPembTa($conn,$periodegaji,$nopembayaran,$kodeunit){
		require_once(Route::getModelPath('unit'));			
		$row = mUnit::getData($conn,$kodeunit);
		
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_pembimbingskripsi h
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
				where h.isvalid=-1 and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pembimbingskripsi h
			join akademik.ms_mahasiswa m on m.nim=h.nim 
			join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorPenguji($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$jenispenguji,$nip=''){
		
		
		$sql="select h.honor,h.nipdosen,m.nim,m.nama,h.tglujian,h.jenispenguji,h.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pengujiujian h
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
			$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan'";
		if(!empty($jenispenguji))
			$sql.=" and h.jenispenguji='$jenispenguji'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";	
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorPenguji($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$jenispenguji,$nip=''){
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pengujiujian h
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1";
		
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan'";
		if(!empty($jenispenguji))
			$sql.=" and h.jenispenguji='$jenispenguji'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";	
				
		$sql.=" group by h.nopengajuan,h.nopembayaran,h.nipdosen,namadosen order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanPenguji($conn,$periode,$periodegaji,$nopembayaran,$kodeunit){
		require_once(Route::getModelPath('unit'));			
		$row = mUnit::getData($conn,$kodeunit);
		
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_pengujiujian h
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(h.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_pengujiujian h
			join akademik.ms_mahasiswa m on m.nim=h.nim 
			join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorTransportPenguji($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$jenispenguji,$sistemkuliah,$nip=''){
		
		$sql="select t.honor,h.nipdosen,m.nim,m.nama,h.tglujian,h.jenispenguji,t.nopengajuan,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportpenguji t
			join honorakademik.hn_pengujiujian h on h.idhonorpenguji=t.idhonorpenguji
			join akademik.ms_mahasiswa m on m.nim=h.nim";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join sdm.ms_pegawai p on p.idpegawai=h.nipdosen where t.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and t.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and t.nopengajuan='$nopengajuan'";
		if(!empty($jenispenguji))
			$sql.=" and h.jenispenguji='$jenispenguji'";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";	
			
		$sql.=" order by h.nipdosen,m.nim";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipdosen']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorTransportPenguji($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$jenispenguji,$sistemkuliah,$nip=''){
		$sql="select h.nipdosen,sum(t.honor) as jumlahhonor,t.nopengajuan,t.nopembayaran,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportpenguji t
			join honorakademik.hn_pengujiujian h on h.idhonorpenguji=t.idhonorpenguji
			join akademik.ms_mahasiswa m on m.nim=h.nim
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and t.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and t.nopengajuan='$nopengajuan'";
		if(!empty($jenispenguji))
			$sql.=" and h.jenispenguji='$jenispenguji'";
		if(!empty($sistemkuliah))
			$sql.=" and m.sistemkuliah='$sistemkuliah'";
		if(!empty($nip))
			$sql.=" and h.nipdosen='$nip'";		
		$sql.=" group by h.nipdosen,namadosen,t.nopengajuan,t.nopembayaran order by h.nipdosen";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanTransportPenguji($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,t.nopengajuan
				from honorakademik.hn_transportpenguji t
				join honorakademik.hn_pengujiujian h on h.idhonorpenguji=t.idhonorpenguji
				join akademik.ms_mahasiswa m on m.nim=h.nim 
				join gate.ms_unit u on u.kodeunit=m.kodeunit
				where t.isvalid=-1 and h.periode='$periode' and t.periodegaji='$periodegaji' and t.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,t.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipdosen,sum(t.honor) as jumlahhonor,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
			from honorakademik.hn_transportpenguji t
			join honorakademik.hn_pengujiujian h on h.idhonorpenguji=t.idhonorpenguji
			join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
			where t.isvalid=-1 and h.periode='$periode' and t.periodegaji='$periodegaji' and t.nopembayaran='$nopembayaran'
			group by h.nipdosen,namadosen
			order by h.nipdosen";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
	
	function getHonorAsisten($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		
		
		$sql="select k.tglkuliah , k.perkuliahanke , k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk , 
			k.jeniskuliah , k.kelompok, k.waktumulai,k.waktumulairealisasi,k.waktuselesai,k.waktuselesairealisasi, 
			s.namasistem||'-'||s.tipeprogram as basis,kl.sistemkuliah,k.statusperkuliahan,
			p.namapegawai as namaasisten,p.norekening,h.nipasisten, mk.namamk,mk.sks,h.skshonor,mk.skspraktikum,
			mk.skstatapmuka,
			mk.sksprakteklapangan,k.jeniskuliah,k.topikkuliah,k.tglkuliahrealisasi, k.waktumulairealisasi,k.waktuselesairealisasi,
			h.honor,h.nopengajuan,h.nopembayaran,h.isvalid,k.isonline 
			from honorakademik.hn_asisten h
			join akademik.ak_kuliah k using (tglkuliah, perkuliahanke, periode, thnkurikulum, kodeunit, kodemk, kelasmk, jeniskuliah, kelompok) 
			join akademik.ak_matakuliah mk using (thnkurikulum , kodemk ) 
			join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk ) 
			left join akademik.ak_sistem s on s.sistemkuliah=kl.sistemkuliah";
		if(!empty($kodeunit)){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql.=" join gate.ms_unit u on u.kodeunit=kl.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight'];
		}
		$sql.=" join akademik.ms_pegawaipenunjang p on h.nipasisten = p.nopegawai where h.isvalid=-1";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah' ";
		if(!empty($nip))
			$sql.=" and h.nipasisten='$nip'";
			
		$sql.=" order by k.nipasisten,k.kelasmk,k.perkuliahanke";
				
		$a_data=$conn->GetArray($sql);
		$a_gaji=array();
		foreach($a_data as $row)
			$a_gaji[$row['nipasisten']][]=$row;
			
		return $a_gaji;
	}
	
	function getRekapHonorAsisten($conn,$kodeunit,$periode,$periodegaji,$nopengajuan,$sistemkuliah,$nip=''){
		$sql="select h.nipasisten,sum(h.honor) as jumlahhonor,h.nopengajuan,h.nopembayaran,
			p.namapegawai as namaasisten
			from honorakademik.hn_asisten h
			join akademik.ak_kelas kl on h.periode=kl.periode and h.thnkurikulum=kl.thnkurikulum and h.kodeunit=kl.kodeunit and h.kodemk=kl.kodemk  and h.kelasmk=kl.kelasmk 
			join akademik.ms_pegawaipenunjang p on h.nipasisten = p.nopegawai
			where h.isvalid=-1
			";
		if(!empty($nip))
			$sql.=" and h.nipasisten='$nip'";
		if(!empty($periode))
			$sql.=" and h.periode='$periode'";
		if(!empty($periodegaji))
			$sql.=" and h.periodegaji='$periodegaji'";
		if(!empty($nopengajuan))
			$sql.=" and h.nopengajuan='$nopengajuan' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah'";	
		$sql.=" group by h.nipasisten,h.nopengajuan,h.nopembayaran,p.namapegawai order by h.nipasisten ";
				
		$a_data=$conn->GetArray($sql);
		
			
		return $a_data;
	}
	
	function getPemindahBukuanAsisten($conn,$periode,$periodegaji,$nopembayaran){
		$sqlinv="select u.kodeunit,u.namaunit,h.nopengajuan
				from honorakademik.hn_asisten h
				join gate.ms_unit u on u.kodeunit=h.kodeunit
				where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
				group by u.kodeunit,u.namaunit,h.nopengajuan";
		$inv=$conn->GetArray($sqlinv);
		
		$sql="select h.nipasisten,sum(h.honor) as jumlahhonor,p.namapegawai as namaasisten
			from honorakademik.hn_asisten h
			join akademik.ms_pegawaipenunjang p on h.nipasisten = p.nopegawai
			where h.isvalid=-1 and h.periode='$periode' and h.periodegaji='$periodegaji' and h.nopembayaran='$nopembayaran'
			group by h.nipasisten,namapegawai
			order by h.nipasisten";		
		$data=$conn->GetArray($sql);
		
		$a_data=array('inv'=>$inv,'data'=>$data);
		
		return $a_data;	
	}
}
?>
