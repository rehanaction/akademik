<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorDOsen extends mModel {
		const schema = 'akademik';
		const table = 'ak_honordosen';
		const order = 'perkuliahanke, tglkuliah';
		const key = 'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok, kodejenisrate, nopengajuan';
		const label = 'Honor Dosen';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select k.tglkuliah , k.perkuliahanke , k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk , k.jeniskuliah , k.kelompok,
				s.tipeprogram as basis,kl.sistemkuliah,g.nopembayaran,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen,k.nipdosen,g.nipdosenrealisasi,
				j.namaunit as jurusan,mk.namamk,coalesce(g.skshonor,mk.sks) as sks,mk.skspraktikum,k.jeniskuliah,k.topikkuliah,k.tglkuliahrealisasi,
				k.waktumulairealisasi,k.waktuselesairealisasi,g.honordosen,g.nopengajuan,k.validhonorkuliah,k.validhonoronline, g.validhonor,k.isonline, g.isonline AS isonline2, g.kodejenisrate, g.keterangan
			from akademik.ak_honordosen g
				join akademik.ak_kuliah k using (tglkuliah, perkuliahanke, periode, thnkurikulum, kodeunit, kodemk, kelasmk, jeniskuliah, kelompok)
				join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
				left join akademik.ak_sistem s on s.sistemkuliah=kl.sistemkuliah
				join sdm.ms_pegawai p on p.idpegawai::text=g.nipdosenrealisasi
				join gate.ms_unit j on j.kodeunit=k.kodeunit";
		
			return $sql;
		}
		function getListFilter($col,$key) {
			
			
			switch($col) {
				case 'fakultas':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					$row = mUnit::getData($conn,'90');
					return "j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'];
				case 'honorunit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					$row = mUnit::getData($conn,$key);
					return "j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'];
				case 'unit':
					return "j.kodeunit='$key'";
				case 'periode' :
					return "k.periode='$key'";
				case 'sistemkuliah' :
					return "kl.sistemkuliah='$key'";
				case 'periodegaji' :
					return "g.periodegaji='$key'";
				case 'nopengajuan' :
					return "g.nopengajuan='$key'";
				case 'nopembayaran' :
					return "g.nopembayaran='$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}		
		function genGaji($conn,$conn_sdm,$unit,$periode,$periodegaji,$periodepengajuan, $sistemkuliah){
			require_once(Route::getMOdelPath('pegawai'));
	
			$koderatemanual=mPegawai::rateManual($conn_sdm);
			$koderatemanualPasca=mPegawai::rateManualPasca($conn_sdm);
			$param_rate=mPegawai::getParamRate($conn_sdm);
			
			$rateTatapMuka=array(
				'R01', //Regular
				'R03', //Sabtu/Malam
				'R04' //Minggu
			);
			
			$unitPasca =array(
				'2210010', //Magister Manajemen
				'2210020', //Magister Administrasi Publik
				'2210030', //Magister Akuntansi
				'2210040', //Magister Hukum
				'2210050', //Magister Administrasi Rumah Sakit
				'2210510', //Magister Ilmu Komunikasi
				'2210840' //Magister Ilmu Komputer
			);
			
			$ProgInternasional =array(
			//	'2212000' //Program Internasional
			//	'2212010' //Business Mandarin
				'I',	//Kelas Internasional - Kebon Jeruk
				'IC'	//Kelas Internasional - Citra Raya
			);
			
			$bulan=substr($periodegaji,4,2);
			$tahun=substr($periodegaji,0,4);
			$q_kuliah="select k.tglkuliah, k.perkuliahanke, k.periode, k.thnkurikulum, k.kodeunit, k.kodemk, k.kelasmk, k.jeniskuliah, k.kelompok,
						k.nipdosenrealisasi, k.tglkuliahrealisasi,k.jeniskuliah, k.isonline,mk.sks,mk.skspraktikum,mk.skstatapmuka,
						mk.sksprakteklapangan,kl.sistemkuliah
						from akademik.ak_kuliah k
						join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
						join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
						where (k.validhonorkuliah=0 or k.validhonorkuliah is null) and k.kodeunit='$unit' and k.periode='$periode' and extract(MONTH from k.tglkuliahrealisasi) = '$bulan'
						and extract(YEAR from k.tglkuliahrealisasi) = '$tahun' and k.statusperkuliahan='S' and k.nipdosenrealisasi is not null
						and kl.sistemkuliah='$sistemkuliah'";
			$datakuliah=$conn->GetArray($q_kuliah);
			$conn->BeginTrans();
			$ok=true;
			$insert=0;$update=0;

			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periodepengajuan);
			foreach($datakuliah as $row){
				if ( $row['isonline']==-1 && strlen($row['kelasmk'])==5 && $row['kelasmk']!='KJInt' ) //KJInt=Kebon Jeruk Internatinal
				{
				//continue; //  sistem online baru, rate-nya tidak hitung di sini
				}
				else 
				{
					$nipdosen=$row['nipdosenrealisasi'];
					$noharirealisasi=date('N',strtotime($row['tglkuliahrealisasi']));
			
					//hitung rate dosen sesuai parameter kepegawaian
					$idx=str_replace(" ","",$row['sistemkuliah'].'|'.$noharirealisasi.'|'.$row['jeniskuliah'].'|'.$row['isonline']);

					$arr_pegawai=mPegawai::getTarifDosen($conn_sdm,$nipdosen);
					$kodejenisrate=$param_rate[$idx];

					if ( in_array(trim($kodejenisrate),$rateTatapMuka) && in_array($row['kodeunit'],$unitPasca) )
						$kodejenisrate='R08';
					if ( in_array(trim($sistemkuliah),$ProgInternasional) )
						$kodejenisrate='R11';
					
					if( trim($kodejenisrate)=='R01' and !empty($koderatemanual) ){ //hardcode cek kode jenis untuk rate reguler
						$kodejenisrate=$koderatemanual;//kalo ada rate manual (R02) maka ambil jenis R02
						$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
						if(empty($tarif) or $tarif==0){ //jika pake R02 tapi nominalnya 0, maka kembalikan ke R01
							$kodejenisrate='R01';
							$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
						}
						if ( empty($tarif) ) $kodejenisrate=null;
					}
					elseif( trim($kodejenisrate)=='R08' and !empty($koderatemanualPasca) ){ //hardcode cek kode jenis untuk rate pasca
						$kodejenisrate=$koderatemanualPasca;//kalo ada rate manual (R09) maka ambil jenis R09
						$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
						if(empty($tarif) or $tarif==0){ //jika pake R09 tapi nominalnya 0, maka kembalikan ke R08
							$kodejenisrate='R08';
							$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
						}
						if ( empty($tarif) ) $kodejenisrate=null;
					}else{
						$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
					}
					
					$keterangan=$kodejenisrate. " ".$arr_pegawai[$kodejenisrate]['namajnsrate'];
					if ( empty($kodejenisrate) ){
						$kodejenisrate='-';
						$keterangan="-";
						$tarif=0;
					}
					
					//setting sks
					$sks=$row['sks'];
					$skstatapmuka=$row['skstatapmuka'];
					$skspraktikum=$row['skspraktikum'];
					if(empty($skstatapmuka) and empty($skspraktikum))
						$skstatapmuka=$sks;
					else if(empty($skstatapmuka) and !empty($skspraktikum))
						$skstatapmuka=$sks-$skspraktikum;
					else if(!empty($skstatapmuka) and empty($skspraktikum))
						$skspraktikum=$sks-$skstatapmuka;

					if(strtoupper($row['jeniskuliah'])=='P')
						$skshonor=$skspraktikum;
					else
						$skshonor=$skstatapmuka;
			
					$tot_tarif=round($tarif)*(int)$skshonor; //aku bulatkan langsung sebelum dihitung. permintaan p.budi 7 nov & ditgaskan lg tgl 21 nov 2014
			
					$record=array();
					$record['tglkuliah']=$row['tglkuliah'];
					$record['skshonor']=$skshonor;
					$record['perkuliahanke']=$row['perkuliahanke'];
					$record['periode']=$row['periode'];
					$record['thnkurikulum']=$row['thnkurikulum'];
					$record['kodeunit']=$row['kodeunit'];
					$record['kodemk']=$row['kodemk'];
					$record['kelasmk']=$row['kelasmk'];
					$record['jeniskuliah']=$row['jeniskuliah'];
					$record['kelompok']=$row['kelompok'];
					$record['periodegaji']=$periodegaji;
					$record['validhonor']=-1;
					$record['nopengajuan']=$r_nopengajuan;
					$record['honordosen']=$tot_tarif;
			
					$record['isonline']=0;
					$record['kodejenisrate']=$kodejenisrate;
					$record['keterangan']=$keterangan;
					$record['nipdosenrealisasi']=$nipdosen;
			
					$key=static::getKeyRow($row);
					$nopengajuan=$conn->GetOne("select nopengajuan from ".static::table()." where ".static::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok')." limit 1");
					if(empty($nopengajuan)){
						$kelas=$conn->Execute("update akademik.ak_kuliah set validhonorkuliah=-1 where ".mKuliah::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
						if($kelas){
							$err = Query::recInsert($conn,$record,static::table());
							$insert++;
							if($err) break;
						}
					}else{
						$kelas=$conn->Execute("update akademik.ak_kuliah set validhonorkuliah=-1 where ".mKuliah::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
						if($kelas){
							$err = Query::recInsert($conn,$record,static::table());
							$update++;
							if($err) break;
						}
					}
				}
			}
			
			$q_kuliah="
				select el.nipdosenrealisasi, el.jmlmahasiswa, el.jmltugasdinilai,
					k.tglkuliah, k.perkuliahanke, k.periode, k.thnkurikulum, k.kodeunit, k.kodemk, k.kelasmk, k.jeniskuliah, k.kelompok,
					kl.sistemkuliah
				from akademik.ak_kuliah k
					join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
					join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
					join elearning.status_kehadiran_dosen el using( perkuliahanke, periode, kodeunit, kodemk, kelasmk )
				where (k.validhonoronline=0 or k.validhonoronline is null)
				and k.kodeunit='$unit' and k.periode='$periode' and kl.sistemkuliah='$sistemkuliah'
				and extract(MONTH from el.tglakhirperkuliahan) = '$bulan' and extract(YEAR from el.tglakhirperkuliahan) = '$tahun'
				and isonline=-1 and length(kelasmk)=5
				";

			$datakuliah=$conn->GetArray($q_kuliah);

			foreach($datakuliah as $row)
			{
				
				$kodejenisrate='R07'; //namajnsrate='Online S1'
				if ( trim($kodejenisrate)=='R07' && in_array($row['kodeunit'],$unitPasca) )
					$kodejenisrate='R10'; //namajnsrate='Online S2'
					
				$nipdosen=$row['nipdosenrealisasi'];					
				$arr_pegawai=mPegawai::getTarifDosen($conn_sdm,$nipdosen);
				$tarif=$arr_pegawai[$kodejenisrate]['nominal'];
				if ( empty($tarif) ) $tarif=0;
				
				$tot_tarif=round($tarif)*$row['jmltugasdinilai'];

				$record=array();
				$record['tglkuliah']=$row['tglkuliah'];
				$record['perkuliahanke']=$row['perkuliahanke'];
				$record['periode']=$row['periode'];
				$record['thnkurikulum']=$row['thnkurikulum'];
				$record['kodeunit']=$row['kodeunit'];
				$record['kodemk']=$row['kodemk'];
				$record['kelasmk']=$row['kelasmk'];
				$record['jeniskuliah']=$row['jeniskuliah'];
				$record['kelompok']=$row['kelompok'];
				$record['periodegaji']=$periodegaji;
				$record['validhonor']=-1;
				$record['nopengajuan']=$r_nopengajuan;
				$record['honordosen']=$tot_tarif;
				
				$record['isonline']=-1;
				$record['kodejenisrate']=$kodejenisrate;
				$record['keterangan']=$kodejenisrate." ".$arr_pegawai[$kodejenisrate]['namajnsrate']. ". ".$row['jmltugasdinilai']." Mhs";
				$record['nipdosenrealisasi']=$nipdosen;
				
				$key=static::getKeyRow($row);
				$nopengajuan=$conn->GetOne("select nopengajuan from ".static::table()." where ".static::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok')." limit 1");
				if(empty($nopengajuan)){
					$kelas=$conn->Execute("update akademik.ak_kuliah set validhonoronline=-1 where ".mKuliah::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
					if($kelas){
						$err = Query::recInsert($conn,$record,static::table());
						$insert++;
						if($err) break;
					}
				}else{
					$kelas=$conn->Execute("update akademik.ak_kuliah set validhonoronline=-1 where ".mKuliah::getCondition($key,'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
					if($kelas){
						$err = Query::recInsert($conn,$record,static::table());
						$update++;
						if($err) break;
					}
				}
			}
		
			if($err) $ok=false;
			$conn->CommitTrans($ok);
			if($ok)
				return array(false,'Generate Honor Mengajar berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Generate Honor Mengajar Gagal');
		}
		function getNopengajuan($conn,$kodeunit,$periodepengajuan){
				$bulanpengajuan=substr($periodepengajuan,4,2);
				$tahunpengajuan=substr($periodepengajuan,0,4);
				$sql="select max(substr(nopengajuan,length(nopengajuan)-3,4)) from ".static::table()." 
				where substr(nopengajuan,length(nopengajuan)-8,4)='$tahunpengajuan'";
				$max=$conn->GetOne($sql);
				$urut=(int)$max+1;
				$nourut=$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,4,'0',STR_PAD_LEFT);
			
			return $nourut;
		}
		function listPeriodeGaji($conn,$periode,$kodeunit){
			$sql="select distinct(periodegaji) as periodegaji from ".static::table()." where periode='$periode' and kodeunit='$kodeunit'";
			$data=$conn->GetArray($sql);
			$periode=array();
			foreach($data as $row){
				$tahun=substr($row['periodegaji'],0,4);
				$bulan=substr($row['periodegaji'],4,2);
				$periode[$row['periodegaji']]=Date::indoMonth((int)$bulan).' '.$tahun;
			}
			return $periode; 
		}
		function listPeriodeGajiFak($conn,$periode,$kodeunit){
			
			$sql="select distinct(g.periodegaji) as periodegaji from ".static::table()." g ";
			
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit j on j.kodeunit=g.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			}
			$sql.=" where periode='$periode'";
			$data=$conn->GetArray($sql);
			$periode=array();
			foreach($data as $row){
				$tahun=substr($row['periodegaji'],0,4);
				$bulan=substr($row['periodegaji'],4,2);
				$periode[$row['periodegaji']]=Date::indoMonth((int)$bulan).' '.$tahun;
			}
			return $periode; 
		}
		function listNopengajuan($conn,$periode,$kodeunit,$periodegaji,$showunit=false){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql="select distinct g.nopengajuan as kode,";
			
			if($showunit)
				$sql.=" g.nopengajuan||' '||j.namaunit as nomor";
			else
				$sql.=" g.nopengajuan as nomor";
				
			$sql.=" from ".static::table()." g";
			$sql.=" join gate.ms_unit j on j.kodeunit=g.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			$sql.=" where periode='$periode' and periodegaji='$periodegaji'";
			
			return Query::arrQuery($conn,$sql);
		}
		function listNoPembayaran($conn,$periode,$kodeunit,$periodegaji){
			$sql="select distinct g.nopembayaran as kode,g.nopembayaran as nomor from 
				".static::table()." g";
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit j on j.kodeunit=g.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			}
			$sql.=" where periode='$periode' and periodegaji='$periodegaji'";
			
			return Query::arrQuery($conn,$sql);
		}
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		function convertPeriodeBayar($nopengajuan){
			$arr=explode('/',$nopengajuan);
			$tahun=$arr[2];
			$bulan=$arr[1];
			
			return Date::indoMonth($bulan).' '.$tahun;
		}
		
		
		function delPerNomor($conn,$nopengajuan){
			require_once(Route::getModelPath('kuliah'));
			$err = false;
			
			$qhonor="select tglkuliah , perkuliahanke , periode , thnkurikulum , kodeunit , kodemk , kelasmk , jeniskuliah , kelompok 
				from ".static::table()." where nopengajuan='$nopengajuan'";
			$a_honor=$conn->GetArray($qhonor);
			
			$conn->BeginTrans();
			foreach($a_honor as $row){
				$key=mKuliah::getKeyRow($row);
				$record=array();
				$record['validhonorkuliah']=0;
				$record['validhonoronline']=0;
				//list($p_posterr,$p_postmsg) = mKuliah::updateRecord($conn,$record,$key,true);
				//update jurnal
				$err = Query::recUpdate($conn,$record,static::table('ak_kuliah'),mKuliah::getCondition($key));
				if($err) break;
			}
			if(!$err) {
				//dleete honor
				$err = Query::qDelete($conn,static::table()," nopengajuan='$nopengajuan'",false);
				if($err) break;
			}
			$ok = Query::isOK($err);
		
			$conn->CommitTrans($ok);
			if($ok)
				return array(false,'Penghapusan data berhasil');
			else
				return array(true,'Penghapusan data gagal');
		}
		
		function setNoPembayaran($conn,$nopengajuan){
			$a_nopengajuan=explode("/",$nopengajuan[0]);
			$bulan=$a_nopengajuan[1];
			$tahun=$a_nopengajuan[2];
			$sql="select max(substr(nopembayaran,length(nopembayaran)-2,3)) from ".static::table()." 
				where substr(nopembayaran,length(nopembayaran)-8,4)='$tahun'";
			$max=$conn->GetOne($sql);
			$urut=(int)$max+1;
			$nopembayaran='BB'.$bulan.'/'.$tahun.'/'.str_pad($urut,4,'0',STR_PAD_LEFT);
			$record=array();
			$record['nopembayaran']=$nopembayaran;
			$err = Query::recUpdate($conn,$record,static::table(),"(nopembayaran='' or nopembayaran is null) and nopengajuan in ('".implode("','",$nopengajuan)."') ");
			
			if(!$err)
				return array(false,'Setting Pembayaran honor berhasil');
			else
				return array(true,'Setting Pembayaran honor gagal');
		}
		
		function getPenerimaHonor($conn,$a_nopengajuan){
			$in_nopengajuan="'".implode("','",$a_nopengajuan)."'";
			$sql="select distinct k.nipdosenrealisasi from ".static::table()." h 
				join ".static::table('ak_kuliah')." k using (tglkuliah, perkuliahanke, periode, thnkurikulum, kodeunit, kodemk, kelasmk, jeniskuliah, kelompok)
				where h.nopengajuan in ($in_nopengajuan) and k.nipdosenrealisasi='7082'";
				
			return Query::arrQuery($conn,$sql);;
		}
	
	}
?>
