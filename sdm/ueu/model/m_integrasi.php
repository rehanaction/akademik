<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mIntegrasi extends mModel {
		const schema = 'sdm';
		
		function saveUnit($conn,$r_key){
			$sql = "select u.*,up.kodeunit as kodeunitparent 
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where u.idunit = $r_key";
					
			$row = $conn->GetRow($sql);
			
			//cek idunit di gate
			$cekgate = $conn->GetOne("select 1 from gate.ms_unit where idunit = $r_key");
			if(empty($cekgate))
				$isql .= "insert into gate.ms_unit (kodeunit,idunit) values ('".$row['kodeunit']."',".$row['idunit'].");";
			
			//cek idunit di keuangan
			$cekkeu = $conn->GetOne("select 1 from keu.ms_unit where idunit = $r_key");
			if(empty($cekkeu))
				$isql .= "insert into keu.ms_unit (kodeunit,idunit) values ('".$row['kodeunit']."',".$row['idunit'].");";
			
			//cek idunit di aset
			$cekaset = $conn->GetOne("select 1 from aset.ms_unit where idunit = $r_key");
			if(empty($cekaset))
				$isql .= "insert into aset.ms_unit (kodeunit,idunit) values ('".$row['kodeunit']."',".$row['idunit'].");";
			
			if(!empty($isql))
				$conn->Execute($isql);			
			
			return self::updateUnit($conn);
		}
		
		function updateUnit($conn){
			//update gate
			$usql .= "update gate.ms_unit set kodeunit=u.kodeunit,kodeunitparent=up.kodeunit,namaunit=u.namaunit,
					namasingkat=u.namasingkat,level=u.level,infoleft=u.infoleft,inforight=u.inforight,
					idunit=u.idunit,idunitparent=u.parentunit,isakad=case when u.isakademik = 'Y' then -1 else 0 end
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where gate.ms_unit.idunit = u.idunit;";
			
			//update keuangan
			$usql .= "update keu.ms_unit set kodeunit=u.kodeunit,namaunit=u.namaunit,
					namasingkat=u.namasingkat,level=u.level,infoleft=u.infoleft,inforight=u.inforight,
					idunit=u.idunit,idunitparent=u.parentunit
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where keu.ms_unit.idunit = u.idunit;";
			
			//update aset
			$usql .= "update aset.ms_unit set kodeunit=u.kodeunit,isaktif=u.isaktif,namaunit=u.namaunit,
					namasingkat=u.namasingkat,level=u.level,infoleft=u.infoleft,inforight=u.inforight,
					idunit=u.idunit,parentunit=u.parentunit,isakademik=u.isakademik,idpimpinan=u.nippimpinan
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where aset.ms_unit.idunit = u.idunit;";
			
			if(!empty($usql))
				$conn->Execute($usql);
			
			return self::saveStatus($conn);
		}
		
		//integrasi simpan unit perpus		
		function saveUnitPerpus($conn,$connperpus,$p_key){
			$sql = "select u.*,up.kodeunit as kodeunitparent 
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where u.idunit = $p_key";
					
			$row = $conn->GetRow($sql);
			
			$r_parent = $row['kodeunitparent'];
			$r_key = $connperpus->GetOne("select kdsatker from ms_satker where idunit = $p_key");
			
			$record = array();
			if(empty($r_key)){//insert
				$r_key = $row['kodeunit'];
				if(empty($r_parent)){
					$maxright = $connperpus->GetOne("select max(inforight) from ms_satker");
					
					$record['info_lft'] = $maxright + 1;
					$record['info_rgt'] = $maxright + 2;
					$record['level'] = '0';
				}else{
					$r_parentright = $connperpus->GetOne("select info_rgt from ms_satker where kdsatker = '$r_parent'");
				
					if(!empty($r_parentright)){
						$ok = $connperpus->Execute("update ms_satker set info_rgt = info_rgt+2 where info_rgt >= $r_parentright");
						if($ok)
							$ok = $connperpus->Execute("update ms_satker set info_lft = info_lft+2 where info_lft > $r_parentright");
						else
							break;
							
						if($ok) {
							$record['info_lft'] = $r_parentright;
							$record['info_rgt'] = $r_parentright + 1;
							$record['level'] = $connperpus->GetOne("select coalesce(level,0)+1 as level from ms_satker where kdsatker = '$r_parent'");
						}else
							break;
					}
				}
			}else{//update
				if(!empty($r_parent)) {
					$p_cek = $connperpus->GetOne("select parentsatker from ms_satker where kdsatker = '$r_key'");
					$right = $connperpus->GetOne("select info_rgt from ms_satker where kdsatker = '$r_key'");
					
					if($p_cek != $r_parent){					
						$ok = $connperpus->Execute("update ms_satker set info_rgt = info_rgt-2 where info_rgt >= $right");
						if($ok)
							$ok = $connperpus->Execute("update ms_satker set info_lft = info_lft-2 where info_lft > $right");
						else
							break;
						
						if($ok){
							$r_parentright = $connperpus->GetOne("select info_rgt from ms_satker where kdsatker = '$r_parent'");					
							$ok = $connperpus->Execute("update ms_satker set info_rgt = info_rgt+2 where info_rgt >= $r_parentright");				
						}else
							break;
							
						if($ok)
							$ok = $connperpus->Execute("update ms_satker set info_lft = info_lft+2 where info_lft > $r_parentright");
						else
							break;
						
						if($ok) {
							$record['info_lft'] = $r_parentright;
							$record['info_rgt'] = $r_parentright + 1;
							$record['level'] = $connperpus->GetOne("select coalesce(level,0)+1 as level from ms_satker where kdsatker = '$r_parent'");
						}else
							break;
					}
				}
			}
			
			$record['kdsatker'] = $r_key;
			$record['parentsatker'] = $r_parent;
			$record['namasatker'] = $row['namaunit'];
			$record['singkatan'] = $row['namasingkat'];
			$record['idunit'] = $p_key;
			$record['isakad'] = $row['isakademik'] == 'Y' ? -1 : 0;
			
			//cek idunit perpus
			$cekperpus = $connperpus->GetOne("select 1 from ms_satker where idunit = $p_key");
			if(empty($cekperpus))
				$err = Query::recInsert($connperpus,$record,'ms_satker');
			else
				$err = Query::recUpdate($connperpus,$record,'ms_satker',"idunit = $p_key");
			
			return $err;
		}
		
		//integrasi simpan unit cac		
		function saveUnitCAC($conn,$conncac,$p_key){
			$sql = "select u.*,up.kodeunit as kodeunitparent 
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where u.idunit = $p_key";
					
			$row = $conn->GetRow($sql);
			
			$r_parent = $row['kodeunitparent'];
			$r_key = $conncac->GetOne("select kodeunit from ms_unit where idunit = $p_key");
			
			$record = array();
			$record['level'] = '1';
			if(empty($r_key)){//insert
				$r_key = $row['kodeunit'];
				if(!empty($r_parent)){
					$record['level'] = $conncac->GetOne("select coalesce(level,0)+1 as level from ms_unit where kodeunit = '$r_parent'");
				}
			}else{//update
				if(!empty($r_parent)) {
					$p_cek = $conncac->GetOne("select parentunit from ms_unit where kodeunit = '$r_key'");
					
					if($p_cek != $r_parent){
						$record['level'] = $conncac->GetOne("select coalesce(level,0)+1 as level from ms_unit where kodeunit = '$r_parent'");
					}
				}
			}
			
			$record['kodeunit'] = $r_key;
			$record['parentunit'] = $r_parent;
			$record['namaunit'] = $row['namaunit'];
			$record['namasingkat'] = $row['namasingkat'];
			$record['jenjang'] = $row['kodeprogram'];
			$record['idunit'] = $p_key;
			
			//cek idunit cac
			$cekcac = $conncac->GetOne("select 1 from ms_unit where idunit = $p_key");
			if(empty($cekcac))
				$err = Query::recInsert($conncac,$record,'ms_unit');
			else
				$err = Query::recUpdate($conncac,$record,'ms_unit',"idunit = $p_key");
			
			return $err;
		}
		
		//hapus info unit
		function deleteUnit($conn,$r_key){
			//hapus gate
			$dsql .= "delete from gate.ms_unit where idunit = $r_key;";
			
			//hapus keuangan
			$dsql .= "delete from keu.ms_unit where idunit = $r_key;";
			
			//hapus aset
			$dsql .= "delete from aset.ms_unit where idunit = $r_key;";
			
			if(!empty($dsql))
				$conn->Execute($dsql);
			
			$err = $conn->ErrorNo();
			
			if(!$err)
				self::updateUnit($conn);
				
			return $err;
		}
		
		function deleteUnitPerpus($connperpus,$r_key){
			$right = $connperpus->GetOne("select info_rgt from ms_satker where idunit = '$r_key'");
			
			if(!empty($right)) {
				$ok = $connperpus->Execute("update ms_satker set info_rgt = info_rgt - 2 where info_rgt >= $right");
				if($ok) 
					$ok = $connperpus->Execute("update ms_satker set info_lft = info_lft - 2 where info_lft > $right");
				else
					break;
								
				if(!$ok)
					break;
			}
			
			//hapus perpus
			if($ok)
				$connperpus->Execute("delete from ms_satker where idunit = $r_key");
			$err = $connperpus->ErrorNo();
			
			return $err;
		}
		
		function deleteUnitCAC($conncac,$r_key){
			//hapus unit cac
			if($ok)
				$conncac->Execute("delete from ms_unit where idunit = $r_key");
			$err = $conncac->ErrorNo();
			
			return $err;
		}
		
		function saveRoleGate($conn,$r_key){		
			//select dulu dari kepegawaian
			$sql = "select *,".self::schema('sdm')."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap 
					from ".self::schema('sdm')."ms_pegawai 
					where idpegawai = '$r_key'";
					
			$record = $conn->GetRow($sql);
			
			$recgate = array();
			$recgate['idpegawai'] = $record['idpegawai'];
			$recgate['username'] = $record['nik'];
			$recgate['userdesc'] = $record['namalengkap'];
			$recgate['hints'] = $record['nik'];
			$recgate['email'] = $record['email'];
			$recgate['isactive'] = '1';
			
			//cek userid
			$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
			if(empty($userid)){
				$recgate['password'] = md5($record['nik']);
				$err = self::insertRecord($conn,$recgate,false,'sc_user','gate','userid',true,$userid);
			}else{ //update
				$err = self::updateRecord($conn,$recgate,$userid,false,'sc_user','userid','gate');
			}
			
			if(!$err){			
				//menambahkan default role pegawai
				$recrole = array();
				$recrole['koderole'] = 'P';
				$recrole['kodeunit'] = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = ".$record['idunit']."");

				//cek user role pegawai
				$unitrolepeg = $conn->GetOne("select kodeunit from ".self::schema('gate')."sc_userrole where userid = $userid and koderole = 'P'");
				if(empty($unitrolepeg)){
					$recrole['userid'] = $userid;
					$err = Query::recInsert($conn,$recrole,self::schema('gate').'sc_userrole');
				}else{
					$a_key = $userid.'|P|'.$unitrolepeg;
					$where = 'userid,koderole,kodeunit';
					$err = Query::recUpdate($conn,$recrole,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
				}
			}			
			
			return $err;
		}
		
		//menyimpan menjadi anggota perpus
		function saveAnggotaPerpus($conn,$connperpus,$r_key){		
			//select dulu dari kepegawaian
			$sql = "select p.*,".self::schema('sdm')."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					kab.namakabupaten as kota,kabktp.namakabupaten as asal,a.iskeluar,u.kodeunit
					from ".self::schema('sdm')."ms_pegawai p
					left join sdm.lv_kabupaten kab on kab.idkabupaten=substring(p.idkelurahan,1,4)
					left join sdm.lv_kabupaten kabktp on kabktp.idkabupaten=substring(p.idkelurahan,1,4)
					left join sdm.lv_statusaktif a on a.idstatusaktif=p.idstatusaktif
					left join sdm.ms_unit u on u.idunit=p.idunit
					where idpegawai = '$r_key'";
					
			$row = $conn->GetRow($sql);
			
			$recperpus = array();
			$recperpus['idpegawai'] = $row['idpegawai'];
			$recperpus['idanggota'] = $row['nik'];
			$recperpus['kdjenisanggota'] = $row['isdosen'] == -1 ? 'D' : 'T';
			$recperpus['namaanggota'] = $row['namalengkap'];
			$recperpus['statusanggota'] = $row['iskeluar'] == 'Y' ? '0' : '1';
			$recperpus['jk'] = $row['jeniskelamin'];
			$recperpus['alamat'] = $row['alamat'];
			$recperpus['email'] = $row['email'];
			$recperpus['telp'] = $row['telepon'];
			$recperpus['hp'] = $row['nohp'];
			$recperpus['kodepos'] = $row['kodepos'];
			$recperpus['kota'] = $row['kota'];
			$recperpus['idunit'] = $row['kodeunit'];
			$recperpus['alamat2'] = $row['alamatktp'];
			$recperpus['asal'] = $row['asal'];
			$recperpus['tgldaftar'] = $row['tglmasuk'];
			$recperpus['noika'] = $row['noktp'];
			
			//cek idanggota
			$idanggota = $connperpus->GetOne("select idanggota from ms_anggota where idanggota = '".$row['nik']."'");
			if(empty($idanggota) and !empty($row['nik'])){
				$recperpus['password'] = md5($row['nik']);
				$err = self::insertRecord($connperpus,$recperpus,false,'ms_anggota','public');
			}else{ //update
				$err = self::updateRecord($connperpus,$recperpus,$idanggota,false,'ms_anggota','idanggota','public');
			}
			
			return $err;
		}
		
		function deleteRoleGate($conn,$r_key){		
			$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
			
			if(!empty($userid)){
				$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($userid,'userid'));
				
				if(!$err)
					$err = Query::qDelete($conn,self::schema('gate').'sc_user',static::getCondition($userid,'userid'));
			}
			
			$err = $conn->ErrorNo();
			
			return $err;
		}
		
		function deleteAnggotaPerpus($connperpus,$r_key){
			$err = Query::qDelete($connperpus,'public.ms_anggota',static::getCondition($r_key,'idpegawai'));
							
			return $err;
		}
		
		function saveUnitRole($conn,$r_key){
			$err = self::saveRoleGate($conn,$r_key);
			
			if(!$err){
				$sql = "select idunit from ".static::table('ms_pegawai')." where idpegawai = $r_key";
				$unit = $conn->GetOne($sql);
								
				//memberikan kode unit role
				$kodeunit = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = $unit");

				//cek userid
				$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
				
				//update kodeunit selain role jab struktural
				$conn->Execute("update ".self::schema("gate")."sc_userrole"." set kodeunit = '$kodeunit' where userid = $userid and koderole <> 'PS'");				
				$err = $conn->ErrorNo();
					
				if($err)
					break;
			}
			
			return $err;
		}
		
		function savePejabatRole($conn,$r_key){
			list($err,$msg) = self::saveRoleGate($conn,$r_key);
			
			if(!$err){
				$sql = "select idjstruktural from ".static::table('ms_pegawai')." where idpegawai = $r_key";
				$struktural = $conn->GetOne($sql);
				
				if(!empty($struktural)){					
					//unit jabatan
					$unit = $conn->GetOne("select idunit from ".static::table('ms_struktural')." where idjstruktural = '$struktural'");
					
					//menambahkan default role pegawai
					$recrole = array();
					$recrole['koderole'] = 'PS';
					$recrole['kodeunit'] = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = $unit");
					
					//cek apakah ada pegawai yang dulu pernah menjabat
					$sql = "select idpegawai from ".static::table('pe_rwtstruktural')."
							where idpegawai <> $r_key and idjstruktural = '$struktural' and isvalid = 'Y'
							order by coalesce(isutama,'T') desc,tmtmulai desc limit 1";
					$idpegold = $conn->GetOne($sql);
					
					if(!empty($idpegold)){
						$olduserid = $conn->GetOne("select userid from ".self::schema("gate")."sc_userrole"." where idpegawai = $idpegold limit 1");
						$isExist = $conn->GetOne("select 1 from ".self::schema("gate")."sc_userrole"." where userid = $olduserid and koderole = '".$recrole['koderole']."' and kodeunit = '".$recrole['kodeunit']."'");
					}
						
					if(!empty($isExist)){
						$a_key = $olduserid.'|'.$recrole['koderole'].'|'.$recrole['kodeunit'];
						$where = 'userid,koderole,kodeunit';
						$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
					}
					
					if(!$err){
						//userid baru
						$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
						$recrole['userid'] = $userid;
						
						$isExist = $conn->GetOne("select 1 from ".self::schema("gate")."sc_userrole"." where userid = $userid and koderole = '".$recrole['koderole']."' and kodeunit = '".$recrole['kodeunit']."'");
						if(empty($isExist))
							$err = Query::recInsert($conn,$recrole,self::schema('gate').'sc_userrole');
						else{
							$a_key = $userid.'|PS|'.$recrole['kodeunit'];
							$where = 'userid,koderole,kodeunit';
							$err = Query::recUpdate($conn,$recrole,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
						}							
					}else
						$conn->RollbackTrans();
				}else{
					$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
					$kodeunit = $conn->GetOne("select kodeunit from ".self::schema("gate")."sc_userrole"." where koderole = 'PS' and userid = $userid");
					
					if(!empty($kodeunit))
						$isExist = $conn->GetOne("select 1 from ".self::schema("gate")."sc_userrole"." where koderole = 'PS' and userid = $userid and kodeunit = '$kodeunit'");
					
					if(!empty($isExist)){
						$a_key = $userid.'|PS|'.$kodeunit;
						$where = 'userid,koderole,kodeunit';
						$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
					}
				}
			}
			
			return $err;
		}
		
		/************************************************** INTEGRASI SINTESA *********************************************/
		
		// mengambil data semester
		function semester($singkat=false) {
			if($singkat)
				$data = array('1' => 'Gasal', '2' => 'Genap', '3' => 'Pendek');
			else
				$data = array('1' => 'Semester Gasal', '2' => 'Semester Genap', '3' => 'Semester Pendek');
			
			return $data;
		}
		
		//tahun periode
		function periode($conn){
			$sql = "select substring(periode,1,4) as periode,substring(periode,1,4) as namaperiode 
					from akademik.ms_periode"."
					group by substring(periode,1,4)
					order by substring(periode,1,4) desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//Daftar penarikan data mengajar
		function listQueryHistoryMengajar() {
			$sql = "select g.*,g.waktumulai||' - '||g.waktuselesai as waktumengajar,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,mk.namamk
					from ".static::table('pe_mengajarlog')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join akademik.ak_matakuliah mk on mk.kodemk = g.kodemk
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.kodeunit = g.kodeunit";
			
			return $sql;
		}		
		
		function getDataHistoryMengajar($conn,$r_key){
			list($tglkuliah,$perkuliahanke,$periode,$thnkurikulum,$kodeunit,$kodemk,$kelasmk) = explode('|',$r_key);
			
			$sql = "select g.*,substring(g.periode,1,4) as tahun,substring(g.periode,4,1) as semester,mk.namamk,
					p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('pe_mengajarlog')." g
					left join akademik.ak_matakuliah mk on mk.kodemk = g.kodemk
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.kodeunit = g.kodeunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = p.idpendidikan
					where g.tglkuliah = '$tglkuliah' and g.perkuliahanke = $perkuliahanke and g.periode = '$periode' and g.thnkurikulum = '$thnkurikulum' and 
					g.kodeunit = '$kodeunit' and g.kodemk = '$kodemk' and g.kelasmk = '$kelasmk'";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		// jenis kuliah
		function jenisKuliah() {
			$data = array('K' => 'Kuliah','P'=>'Praktikum','R'=>'Tutorial','Q' => 'Quiz','T' => 'UTS','U' => 'UAS','H' => 'HER');
			
			return $data;
		}
		
		//proses penarikan/ penguncian data mengajar
		function tarikDataMengajar($conn,$r_periode){			
			$sql = "select a.*,p.idpegawai,sdm.f_diffmenit(lpad(a.waktumulai::character varying,4,'0'),lpad(a.waktuselesai::character varying,4,'0')) as jmljam,up.kodeunit as fakultas
					from ".self::schema('akademik')."ak_kuliah a
					left join ".static::table('ms_pegawai')." p on p.nik = a.nipdosen
					left join ".static::table('ms_unit')." u on u.kodeunit = a.kodeunit
					left join ".static::table('ms_unit')." up on up.idunit = u.parentunit
					where a.periode = '$r_periode' and a.statusperkuliahan = 'S' and a.isvalid = -1";
			
			$rs = $conn->Execute($sql);
			
			$i=0;
			while($row = $rs->FetchRow()){
				$i++;
				
				$record = array();
				$record['periodegaji'] = $r_periode;
				$record['jmljam'] = (int)$row['jmljam']/60;
				$record = $row;
				
				$isexist = $conn->GetOne("select 1 from ".static::table('pe_mengajarlog')." where tglkuliah = '".$record['tglkuliah']."' and
							perkuliahanke = ".$record['perkuliahanke']." and periode = '".$record['periode']."' and thnkurikulum = '".$record['thnkurikulum']."' and 
							kodeunit = '".$record['kodeunit']."' and kodemk = '".$record['kodemk']."' and kelasmk = '".$record['kelasmk']."'");
				if(empty($isexist))
					list($err,$msg) = self::insertRecord($conn,$record,true,'pe_mengajarlog');
				else{
					$key = $record['tglkuliah'].'|'.$record['perkuliahanke'].'|'.$record['periode'].'|'.$record['thnkurikulum'].'|'.$record['kodeunit'].'|'.$record['kodemk'].'|'.$record['kelasmk'];
					$colkey = 'tglkuliah,perkuliahanke,periode,thnkurikulum,kodeunit,kodemk,kelasmk';
					list($err,$msg) = self::updateRecord($conn,$record,$key,true,'pe_mengajarlog',$colkey);
				}
			}
			
			if($i == 0){
				$err = 1;
				$msg = 'Tidak ada data mengajar dosen yang ditarik';
			}
			
			return array($err,$msg);
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				case 'tahun' :
					return "substring(periode,1,4)='$key'";
					break;
				case 'semester' :
					return "substring(periode,5,1)='$key'";
					break;
			}
		}
		
		/************************************************** END OF INTEGRASI AKADEMIK *********************************************/
	}
?>
