<?php
	// model periode akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriode extends mModel {
		const schema = 'akademik';
		const table = 'ms_periode';
		const order = 'periode desc';
		const key = 'periode';
		const label = 'periode akademik';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *, substr(bulanawal,1,4) as thnawal, substr(bulanawal,5,2) as blnawal,
					substr(bulanakhir,1,4) as thnakhir, substr(bulanakhir,5,2) as blnakhir
					from ".static::table();
			
			return $sql;
		}
		
		// mendapatkan array data
		function getArray($conn,$singkat=true) {
			if($singkat)
				$separator = '/';
			else
				$separator = ' - ';
			
			$a_semester = Akademik::semester($singkat);
			
			$sql = "select periode from ".static::table()." order by ".static::order;
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($singkat)
					$t_tahun = substr($row['periode'],2,2);
				else
					$t_tahun = substr($row['periode'],0,4);
				
				$data[$row['periode']] = $a_semester[substr($row['periode'],-1)].' '.$t_tahun.$separator.str_pad($t_tahun+1,2,'0',STR_PAD_LEFT);
			}
			
			return $data;
		}
		
		function getMaxPeriode($conn){
			$periode=$conn->GetOne("select max(periode) from ".static::table());
			
			return substr($periode,0,4);
		}
		
		/**
		 * Mendapatkan periode sebelumnya
		 * @param string $periode
		 * @return string
		 */
		function getPeriodeLalu($periode) {
			$tahun = substr($periode,0,4);
			$semester = substr($periode,-1);
			
			if($semester == '1') {
				$tahun = (int)$tahun-1;
				$semester = '2';
			}
			else if($semester == '2')
				$semester = '1';
			else
				$semester = (int)$semester-2;
			
			return $tahun.$semester;
		}
		
		function getDetailPeriode($conn, $periode){
			$sql = "select periode
						, tglawal
						, tglakhir
					from ".static::table()
					." where periode='$periode'"
					." order by ".static::order;
			$rs = $conn->Execute($sql);
			$data = array();
			while($row = $rs->FetchRow())
			{
				$data=array(
					"periode"	=>$row['periode'],
					"tglawal"	=>$row['tglawal'],
					"tglakhir"	=>$row['tglakhir']
				);
			}
			return $data;
		}

		//11-12-2018 Tambahan rehan untuk otomatis menambah semester di elearning

		function addCategory($data){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_create_categories';
			$restformat = 'json';
				$category = new stdClass();
				$category->name=$data['nama'];	
				$category->parent=$data['parent'];					
				$category->description='<p>'.$data['nama'].'</p>';
				$category->idnumber=$data['idnumber'];					
				$category->descriptionformat=1;									
				$categories = array($category);
				$params = array('categories' => $categories);
				/// REST CALL
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
				
				

		

		}
		function addCategory2($data){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_create_categories';
			$restformat = 'json';
				$params = array('categories' => $data);
				print_r($data);
				die();
				/// REST CALL
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
				
				

		

		}

		function getUCategoryMoodle($conn,$periode){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_get_categories';
			$restformat = 'json';
			$params = array('criteria'=>array(
					array(	
						'key'=>'idnumber',
						'value'=>$periode
						)
				)
		 	 );
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			$data = json_decode($resp, true);
			return $data;
			//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
			//print_r($resp);
			//self::UpdateSyncMoodle($conn,$data['userid']);
		}
		
	}
?>
