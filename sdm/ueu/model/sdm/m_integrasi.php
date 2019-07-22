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
			
			
			if(!empty($isql))
				$conn->Execute($isql);			
			
			return self::updateUnit($conn);
		}
		
		function updateUnit($conn){
			//update gate
			$usql .= "update gate.ms_unit set kodeunit=u.kodeunit,parentkodeunit=up.kodeunit,namaunit=u.namaunit,
					namasingkat=u.namasingkat,level=u.level,infoleft=u.infoleft,inforight=u.inforight,idunit=u.idunit
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." up on up.idunit = u.parentunit
					where gate.ms_unit.idunit = u.idunit;";
			
			
			if(!empty($usql))
				$conn->Execute($usql);
			
			$err = $conn->ErrorNo();
			
			return $err;
		}
		
		//hapus info unit
		function deleteUnit($conn,$r_key){
			//hapus gate
			$dsql .= "delete from gate.ms_unit where idunit = $r_key;";
			
			//hapus aset
			$dsql .= "delete from aset.ms_unit where idunit = $r_key;";
			
			if(!empty($dsql))
				$conn->Execute($dsql);
			
			$err = $conn->ErrorNo();
			
			if(!$err)
				self::updateUnit($conn);
				
			return $err;
		}		

		function saveUnitLain($conn,$r_key){
			$sql = "select kodeunit,idunit from mutu.ms_unit where idunit = $r_key";					
			$row = $conn->GetRow($sql);
			
			//cek idunit di gate
			$cekgate = $conn->GetOne("select 1 from gate.ms_unit where idunit = $r_key");
			if(empty($cekgate))
				$isql .= "insert into gate.ms_unit (kodeunit,idunit) values ('".$row['kodeunit']."',".$row['idunit'].");";
			
			if(!empty($isql))
				$conn->Execute($isql);	
			
			return self::updateUnitLain($conn);
		}

		function updateUnitLain($conn){
			//update gate
			$gsql .= "update gate.ms_unit set kodeunit=u.kodeunit,kodeunitparent=u.parentkodeunit,namaunit=u.namaunit,
					namasingkat=u.namasingkat,level=u.level,infoleft=u.infoleft,inforight=u.inforight,
					idunit=u.idunit,idunitparent=u.idunitparent,isakad=case when u.isakademik = 'Y' then -1 else 0 end,
					t_updateuser=u.t_username,t_updateip=u.t_ipaddress,t_updatetime=u.t_updatetime
					from mutu.ms_unit u
					where gate.ms_unit.idunit = u.idunit;";
			
			if(!empty($gsql))
				$conn->Execute($gsql);
			
			return self::saveStatus($conn);
		}
		
		//hapus info unit
		function deleteUnitLain($conn,$r_key){
			//hapus gate
			$dsql .= "delete from gate.ms_unit where idunit = $r_key;";
			
			if(!empty($dsql))
				$conn->Execute($dsql);
			
			$err = $conn->ErrorNo();
			
			if(!$err)
				self::updateUnitLain($conn);
				
			return $err;
		}	
		
		function saveRoleGate($conn,$r_key){		
			//select dulu dari kepegawaian
			$sql = "select *,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap 
					from sdm.ms_pegawai 
					where idpegawai = '$r_key'";
					
			$record = $conn->GetRow($sql);
			
			
			$recgate = array();
			$recgate['idpegawai'] = $record['idpegawai'];
			
			if(!empty($record['nik']))
				$username = $record['nik'];
			else if(!empty($record['nodosen']))
				$username = $record['nodosen'];
			else if(!empty($record['nip']))
				$username = $record['nip'];

			$recgate['username'] = $record['username'];
			$recgate['hints'] = $record['tgllahir'];
			$recgate['userdesc'] = $record['namalengkap'];
			$recgate['email'] = $record['email'];
			$recgate['isactive'] = '1';	
			$recgate['isuseldap'] = 'null';
			
			//pengecekan bila menggunakan LDAP			
			if(!empty($record['usernameldap'])){
				$recgate['username'] = $record['username'];
				$recgate['isuseldap'] = '1';
			}
		
			
			//cek userid
			$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
			
			if(empty($userid)){
				$recgate['password'] = $conn->GetOne("select ".self::schema('gate')."MD5('".$username."')");
				$err = Query::recInsert($conn,$recgate,self::schema('gate').'sc_user');
				$userid = $conn->Insert_ID();
			}else{ //update
				$err = Query::recUpdate($conn,$recgate,self::schema('gate').'sc_user',static::getCondition($userid,'userid'));
			}
			
			if(!$err){	
					
				//menambahkan default role pegawai
				$recrole = array();
				$recrole['koderole'] = 'Peg';
				$recrole['kodeunit'] = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = ".$record['idunit']."");
				
				//cek user role pegawai
				$unitrolepeg = $conn->GetOne("select kodeunit from ".self::schema('gate')."sc_userrole where userid = $userid and koderole = 'Peg'");
				
				if(empty($unitrolepeg)){
					$recrole['userid'] = $userid;
					$err = Query::recInsert($conn,$recrole,self::schema('gate').'sc_userrole');
				}else{
					$a_key = $userid.'|Peg|'.$unitrolepeg;
					$where = 'userid,koderole,kodeunit';
					//$where = array();
					//$where['userid'] = $userid;
					//$where['koderole']='Peg';
					//$where['kodeunit'] = $unitrolepeg;
					//print_r($where);
					$err = Query::recUpdate($conn,$recrole,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
					//$err=self::UpdateRole($conn,$recrole,$where);
					
					//print_r($err);
				}
				
				if($err){
					
					break;
				}
			}else{
				break;
			}
						
			return $err;
		}
		function UpdateRole($conn,$data,$where){
			
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$valuesArrayswhere = array();
			$i = 0;
			$ii = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					
						$valuesArrays[$i] = $key.'='.$values;
				
				}else{
					
					$valuesArrays[$i]= $key."='".$values."'";
	
				}
				$i++;
			}
			foreach($where as $key2=>$values2)
			{
				if(is_int($values2))
				{
					$valuesArrayswhere[$ii] = $key2.'='.$values2;
				}else{
					$valuesArrayswhere[$ii]= $key2."='".$values2."'";
				}
				$ii++;
			}
			$values = implode(',',$valuesArrays);
			$values2 = implode(' and ',$valuesArrayswhere);
			$sql = "update gate.sc_userrole set $values  where $values2";
		
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		
		function deleteRoleGate($conn,$r_key){		
			$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
			
			if(!empty($userid)){
				$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($userid,'userid'));
			
				if(!$err)
					$err = Query::qDelete($conn,self::schema('gate').'sc_user',static::getCondition($userid,'userid'));
				else
					break;
			}
				
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
				
				//select dulu apakah ada di sc_userrole
				$sql = "select * from ".self::schema("gate")."sc_userrole"." where userid = $userid and koderole <> 'Jab'";
				$rs = $conn->Execute($sql);
				
				//update kodeunit selain role jab struktural
				while($row = $rs->FetchRow()){
					$sql = "select 1 from ".self::schema("gate")."sc_userrole"." where userid = $userid and koderole = '".$row['koderole']."' and kodeunit = '$kodeunit'";
					$cek = $conn->GetOne($sql);
					if(empty($cek)){
						$conn->Execute("update ".self::schema("gate")."sc_userrole"." set kodeunit = '$kodeunit' where userid = $userid and koderole = '".$row['koderole']."' and kodeunit = '".$row['kodeunit']."'");	
						
						$err = $conn->ErrorNo();
						if($err)
							break;
					}
				}		
			}
						
			return $err;
		}
		
		function savePejabatRole($conn,$r_key){
			$err = self::saveRoleGate($conn,$r_key);
			
			if(!$err){
				$sql = "select idjstruktural from ".static::table('ms_pegawai')." where idpegawai = $r_key";
				$struktural = $conn->GetOne($sql);
				
				if(!empty($struktural)){					
					//unit jabatan
					$unit = $conn->GetOne("select idunit from ".static::table('ms_struktural')." where idjstruktural = '$struktural'");
					
					//menambahkan default role pegawai
					$recrole = array();
					$recrole['koderole'] = 'Jab';
					$recrole['kodeunit'] = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = $unit");
					
					//cek apakah ada pegawai yang dulu pernah menjabat
					$sql = "select top 1 idpegawai from ".static::table('pe_rwtstruktural')."
							where idpegawai <> $r_key and idjstruktural = '$struktural' and isvalid = 'Y' and isefektif='Y'
							order by coalesce(isutama,'T') desc,tmtmulai desc";
					$idpegold = $conn->GetOne($sql);
					
					if(!empty($idpegold)){
						$olduserid = $conn->GetOne("select top 1 userid from ".self::schema("gate")."sc_user"." where idpegawai = $idpegold");
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
							$a_key = $userid.'|Jab|'.$recrole['kodeunit'];
							$where = 'userid,koderole,kodeunit';
							$err = Query::recUpdate($conn,$recrole,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
						}							
					}else
						break;
				}else{
					$userid = $conn->GetOne("select userid from ".self::schema('gate')."sc_user where idpegawai = $r_key");
					$kodeunit = $conn->GetOne("select kodeunit from ".self::schema("gate")."sc_userrole"." where koderole = 'Jab' and userid = $userid");
					
					if(!empty($kodeunit))
						$isExist = $conn->GetOne("select 1 from ".self::schema("gate")."sc_userrole"." where koderole = 'Jab' and userid = $userid and kodeunit = '$kodeunit'");
					
					if(!empty($isExist)){
						$a_key = $userid.'|Jab|'.$kodeunit;
						$where = 'userid,koderole,kodeunit';
						$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
					}
				}
			}
			
			$err = $conn->ErrorNo();
			
			return $err;
		}
		
		function deletePejabatRole($conn,$idpeg,$nourutjs){	
			$struktural = $conn->GetOne("select idjstruktural from ".static::table('pe_rwtstruktural')." where nourutjs = '$nourutjs'");
			$idunit = $conn->GetOne("select idunit from ".static::table('ms_struktural')." where idjstruktural = '$struktural'");
			$kodeunit = $conn->GetOne("select kodeunit from ".self::schema('sdm')."ms_unit where idunit = $idunit");
			
			$userid = $conn->GetOne("select top 1 userid from ".self::schema("gate")."sc_user"." where idpegawai = $idpeg");
			$isExist = $conn->GetOne("select 1 from ".self::schema("gate")."sc_userrole"." where userid = $userid and koderole = 'Jab' and kodeunit = '$kodeunit'");
						
			if(!empty($isExist)){
				$a_key = $userid.'|Jab|'.$kodeunit;
				$where = 'userid,koderole,kodeunit';
				$err = Query::qDelete($conn,self::schema('gate').'sc_userrole',static::getCondition($a_key,$where));
			}
			
			$err = $conn->ErrorNo();
			
			return $err;
		}

		function saveKepalaUnit($conn,$connmutu,$idrwt){
			$r_subkey = $conn->GetOne("select idjstruktural from ".static::table('pe_rwtstruktural')." where nourutjs = '$idrwt'");

			//apakah jabatan kepala unit
			$sql = "select idunit from ".static::table('ms_struktural')." where idjstruktural = '$r_subkey' and ispimpinan = 'Y'";
			$unitkepala = $conn->GetOne($sql);

			if(!empty($unitkepala)){
				$kepalaunit = $conn->GetOne("select top 1 idpegawai from ".static::table('pe_rwtstruktural')." where idjstruktural = '$r_subkey' and isvalid = 'Y' and isaktif = 'Y' and isefektif='Y' order by coalesce(isutama,'T') desc,tmtmulai desc");

				if(!empty($kepalaunit)){
					$dataketuaunit = $conn->GetRow("select nik,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap from ".self::table('ms_pegawai')." where idpegawai = '$kepalaunit'");

					$conn->Execute("update ".self::table('ms_unit')." set nippimpinan = '$kepalaunit' where idunit = '$unitkepala'");
				}
				else
					$conn->Execute("update ".self::table('ms_unit')." set nippimpinan = null where idunit = '$unitkepala'");

				$err = $conn->ErrorNo();

				if(!$err){
					if(!empty($kepalaunit))
						$connmutu->Execute("update mutu.ms_unit set ketuaunit = '$kepalaunit',namaketuaunit = '".$dataketuaunit['namalengkap']."',nik = '".$dataketuaunit['nik']."' where idunit = '$unitkepala'");
					else
						$connmutu->Execute("update mutu.ms_unit set ketuaunit = null,,namaketuaunit = null, nik = null where idunit = '$unitkepala'");
				}
			}
		}
		
		/************************************************** INTEGRASI SINTESA *********************************************/
		
		function listQueryProfilDosen(){
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					namapendidikan, jabatanfungsional, nodosen, uh.namaunit as unithomebase, um.namaunit as unitmatkul, irpd, irkd
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunit=u.idunit
					left join ".static::table('ms_unit')." um on pd.idunit=um.idunit
					left join ".static::table('ms_unit')." uh on uh.idunit=p.idunitbase
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional";
			
			return $sql;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				case 'periode' :
	                if ($key != 'all')
	                    return "periode='$key'";
	                else
	                    return "(1=1)";
					break;
			}
		}
		
		//perlu diganti menyesuaikan dengan db real
		function getDataAjarSia($conn, $r_periode, $a_kodeunit){
			if(count($a_kodeunit)>0)
				$i_kodeunit = implode("','",$a_kodeunit);
			
			$sql = "select periode,nipdosen,kodeunit
					from akademik.ak_mengajar 
					where periode='$r_periode' and kodeunit in ('$i_kodeunit') and (nipdosen is not null and nipdosen <>'') 
					group by periode,nipdosen,kodeunit";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getPeriodeSia($conn){
			$sql = "select periode, akademik.f_namaperiode(periode) as namaperiode 
					from akademik.ms_periode 
					where akademik.f_namaperiode(periode) is not null
					order by periode desc";
			$rs = $conn->Execute($sql);
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriodeSia($connsia){
			$sql = "select periode from akademik.ms_periode order by periode desc limit 1";
			
			return $connsia->GetOne($sql);
		}
		
		//singkronisasi periode akademik
		function syncPeriodeAkad($conn,$connsia){
			$sql = "select rtrim(Course_ID) as periode, Course_Desc as namaperiode 
					from dbo.tblCourse";
			$rs = $connsia->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$record = array();
				$record['kodeperiodeakad'] = $row['periode'];
				$record['namaperiodeakad'] = $row['namaperiode'];
				
				$cek = $conn->GetOne("select 1 from ".static::table('ms_mapperiodeakad')." where kodeperiodeakad = '".$record['kodeperiodeakad']."'");
				if(empty($cek))
					$err = Query::recInsert($conn,$record,static::table('ms_mapperiodeakad'));				
				else
					$err = Query::recUpdate($conn,$record,static::table('ms_mapperiodeakad'),static::getCondition($record['kodeperiodeakad'],'kodeperiodeakad'));
				
				if($err)
					break;
			}
			
			return self::saveStatus($conn);
		}
		
		function listQueryMapUnit(){
			$sql = "select m.*,u.infoleft from ".static::table('pe_mapunit')." m
					left join ".static::table('ms_unit')." u on u.idunit = m.idunit";
			
			return $sql;
		}
		
		function getMapUnit($conn, $r_unit){
			$sql = "select kodeunitsia from ".static::table('pe_mapunit')." where idunit=$r_unit";
			$rs = $conn->Execute($sql);
			
			$a_unitakd = array();
			while($row = $rs->FetchRow()){
				$a_unitakd[] = $row['kodeunitsia'];
			}
			
			return $a_unitakd;
		}
		
		function getCompPegawai($conn){			
			$sql = "select idpegawai, idpendidikan, idpangkat, idjfungsional, idjenispegawai, nodosen
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where p.nodosen is not null";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['nodosen'][$row['nodosen']] = $row['nodosen'];
				$a_data['idpegawai'][$row['nodosen']] = $row['idpegawai'];
				$a_data['idpendidikan'][$row['nodosen']] = $row['idpendidikan'];
				$a_data['idpangkat'][$row['nodosen']] = $row['idpangkat'];
				$a_data['idjfungsional'][$row['nodosen']] = $row['idjfungsional'];
				$a_data['idjenispegawai'][$row['nodosen']] = $row['idjenispegawai'];
			}
			
			return $a_data;
		}
		
		function getMapGateSDM($conn, $r_unit){
			$sql = "select idunit from ".static::table('ms_unit')." where kodeunit='$r_unit'";
			
			return $conn->GetOne($sql);
		}
		
		function getMapSIA($conn){
			$sql = "select kodeunit, namaunit,level from gate.ms_unit order by infoleft";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodeunit']] = str_repeat($pref,$row['level']).$row['namaunit'];
			}
			
			return $data;
		}
		
		function getMapKEU($conn){
			$sql = "select kodeunit, namaunit,levelunit from keu.ms_unit order by info_left";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodeunit']] = str_repeat($pref,$row['levelunit']).$row['namaunit'];
			}
			
			return $data;
		}
		
		/************************************************** END OF INTEGRASI SINTESA *********************************************/
		
		
		/**************************************** INTEGRASI SIM AKADEMIK -> SDM.MS_PEGAWAI *******************************************/
		
		function saveDosenSyncAkad($conn,$connsia,$r_key,$aktif='AA'){
			//select dari sdm pegawai (mssql)
			$record = $conn->GetRow("select * from sdm.ms_pegawai p
									 left join sdm.pe_mapunit m on m.idunit=p.idunit
									 where idpegawai = '$r_key'");
				
			$recsia = array();
			$recsia['nik']=$record['nip'];
			$recsia['idpegawai'] = $record['nodosen'];
			$recsia['nodosen'] = $record['nodosen'];
			$recsia['idpegawairef']=$record['idpegawai'];
			$recsia['idunitlama']=$record['idunitold'];
			
			$recsia['namadepan']=$record['namadepan'];
			$recsia['namatengah']=$record['namatengah'];
			$recsia['namabelakang']=$record['namabelakang'];
			$recsia['jeniskelamin']=$record['jeniskelamin'];
			$recsia['tgllahir']=$record['tgllahir'];
			$recsia['tmplahir']=$record['tmplahir'];
			$recsia['nidn']=$record['nidn'];
			$recsia['idstatusaktif']=$aktif;
			$recsia['idtipepeg']=$record['idtipepegbaru'];
			$recsia['idjenispegawai']=$record['idjenispegbaru'];
			$recsia['noktp']=$record['noktp'];
			$recsia['tglpensiun']=$record['tglpensiun'];
			
			$univ = $connsia->GetOne("select kodeunit from gate.ms_unit where infoleft = 1");
			if(!empty($record['kodeunitsia'])){
				$recsia['idunit'] = $connsia->GetOne("select idunit from gate.ms_unit where kodeunit = '".$record['kodeunitsia']."'");
				if(empty($recsia['idunit']))
					$recsia['idunit'] = $univ;
			}
			else
				$recsia['idunit'] = $univ;
			
			$isExist = $connsia->GetOne("select 1 from sdm.ms_pegawai where idpegawairef=".$r_key."");	
			if(!$isExist){
				$err = Query::recInsert($connsia,$recsia,'sdm.ms_pegawai');
			}else{
				if($recsia['idstatusaktif'] == 'TA')
					unset($recsia['idpegawai']);
				
				$err = Query::recUpdate($connsia,$recsia,'sdm.ms_pegawai'," idpegawairef='".$r_key."'");
				
				if($recsia['idstatusaktif'] == 'TA' and !$err){
					$err = Query::qDelete($connsia,'sdm.ms_pegawai'," idpegawairef='$r_key'");
					if($err){
						$recsiaup = array();
						$recsiaup['idpegawairef'] = 'null';
						$err = Query::recUpdate($connsia,$recsiaup,'sdm.ms_pegawai'," idpegawairef='".$r_key."'");
					}
				}

			}
			
			return $err;
		}


		function saveNonDosenSyncAkad($conn,$connsia,$r_key,$aktif='AA'){
			//select dari sdm pegawai (mssql)
			$record = $conn->GetRow("select *,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
									 from sdm.ms_pegawai
									 where idpegawai = '$r_key'");
				
			$recsia = array();
			$recsia['nopegawai']=$record['nip'];
			$recsia['namapegawai'] = $record['namalengkap'];
			$recsia['noidentitas']=$record['noktp'];
			$recsia['alamat']=$record['alamat'];
			$recsia['nonpwp']=$record['npwp'];
			$recsia['notelephon']=$record['telepon'];
			$recsia['nohp']=$record['nohp'];
			$recsia['email']=$record['email'];
			$recsia['norekening']=$record['norekening'];
			$recsia['namarekening']=$record['anrekening'];
			$recsia['idtipepeg']=$record['idtipepeg'];
			$recsia['idjenispeg']=$record['idjenispegawai'];
			$recsia['statusaktif']=$record['idstatusaktif'];
			$recsia['idpegawai']=$record['idpegawai'];
			
			$isExist = $connsia->GetOne("select 1 from akademik.ms_pegawaipenunjang where idpegawai=".$r_key."");	
			if(!$isExist){
				$err = Query::recInsert($connsia,$recsia,'akademik.ms_pegawaipenunjang');
			}else{
				if($recsia['idstatusaktif'] == 'TA')
					unset($recsia['idpegawai']);
				
				$err = Query::recUpdate($connsia,$recsia,'akademik.ms_pegawaipenunjang'," idpegawai='".$r_key."'");
				
				if($recsia['idstatusaktif'] == 'TA' and !$err)
					$err = Query::qDelete($connsia,'akademik.ms_pegawaipenunjang'," idpegawai='$r_key'");
			}
			
			return $err;
		}
		
		//delete integrasi
		function deleteIntegrasi($connsia, $r_key){
			$recsia = array();
			$recsia['idstatusaktif'] = 'TA';
			$err = Query::recUpdate($connsia,$recsia,'sdm.ms_pegawai'," idpegawairef='".$r_key."'");
			
			if(!$err)
				$err = Query::qDelete($connsia,'sdm.ms_pegawai'," idpegawairef='$r_key'");
			
			return $err;
		}
		/**************************************** INTEGRASI SIM AKADEMIK -> SDM.MS_TIPEPEG, SDM.MS_JENISPEG *******************************************/
		
		function setDataCondition($key, $datacondition){
			if(is_array($datacondition)){
				foreach($datacondition as $keycondition => $result){
					if($keycondition == $key)
						$data = $result;
				}

				if(empty($data))
					$data = $datacondition['default'];
			}
			else
				$data = $datacondition;
			
			return $data;
		}
		
		function setDataIntegrasi($conn, $table, $key, $colkey, $addset = array(), $addcondition = array()){
			$sql = "select * from ".static::table($table)." where ".static::getCondition($key, $colkey);
			
			$a_data = array();
			$a_data = $conn->GetRow($sql);
			
			if(!empty($addset)){
				foreach($addset as $keyadd => $valueadd){
					if(!empty($addcondition))
						$a_data[$keyadd] = self::setDataCondition($valueadd, $addcondition[$keyadd]);
					else
						$a_data[$keyadd] = $valueadd;
				}
			}

			return $a_data;
		}
		
		function saveDataIntegrasi($connsia, $table, $key, $colkey, $dataset){
			if(! empty($dataset)){
				$isExist = $connsia->GetOne("select 1 from ".static::table($table)." where ".static::getCondition($key, $colkey));
				
				if(! $isExist)
					$err = Query::recInsert($connsia, $dataset, static::table($table));
				else
					$err = Query::recUpdate($connsia, $dataset, static::table($table), static::getCondition($key,$colkey));
			}
			
			return $err;
		}
		
		function deleteDataIntegrasi($connsia, $table, $key, $colkey){
			$err = Query::qDelete($connsia, static::table($table), static::getCondition($key,$colkey));
			
			return $err;
		}

		
	}
?>
