<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class uForm {
		// mendapatkan label dari informasi field (nilai di luar input)
		function getLabel($data,$value) {
			$nameid = $data['nameid'];
			if(empty($nameid))
				$nameid = $data['kolom'];
			
			if(isset($value))
				$value = trim($value);
			else if(isset($data['default']))
				$value = $data['default'];
			
			if($data['type'] == 'D')
				$value = CStr::formatDateInd($value,false);
			else if($data['type'] == 'DT')
				$value = CStr::formatDateTimeInd($value,false);
			else if($data['type'][0] == 'N' and strval($value) != '') {
				list(,$dec) = explode(',',$data['type']);
				$dec = (int)$dec;
				
				if($data['type'][1] == 'P')
					$value = number_format($value,$dec,',','');
				else
					$value = CStr::formatNumber($value,$dec,true);
			}
			else if($data['type'][0] == 'U' and $value != '') {
				if($data['type'][1] == 'I')
					$value = '<img id="'.$data['uptype'].'">';
				else
					$value = '<u class="ULink" onclick="goDownload(\''.$data['uptype'].'\')">'.$value.'</u>';
				
				$value .= '<div class="Break"></div>';
				//$value .= '<u class="ULink" onclick="goDeleteFile(\''.$nameid.'\')">Hapus file</u>'; //dibuang aja. kalo mau ya upload ulang :P
			}
			else if(!empty($data['option'])) {
				$value = $data['option'][$value];
				
				if(isset($value) and $data['type'] == 'C')
					$value = '<img src="images/check.png">';
				else
					$value = trim(str_replace('&nbsp;',' ',$value));
			}
			
			if(!empty($data['format'])) {
				$format = $data['format'];
				eval('$value = '.$format."('".$value."');");
			}
			if(!empty($data['infoview']))
				$value .= ' &nbsp; &nbsp; <em>'.$data['infoview'].'</em>';
			
			return $value;
		}
		
		// mendapatkan nilai dari informasi field (nilai di dalam input)
		function getValue($data,$value) {
			if(isset($value))
				$value = trim($value);
			else if(isset($data['default']))
				$value = $data['default'];
			
			if(!empty($data['format'])) {
				if(is_array($data['format'])) {
					if(method_exists($data['format'][0],$data['format'][1]))
						$value = call_user_func($data['format'],$value);
				}
				else {
					if(function_exists($data['format']))
						$value = call_user_func($data['format'],$value);
				}
			}
			else if($data['type'] == 'D') {
				$value = CStr::formatDate($value);
			}
			else if($data['type'][0] == 'N' and strval($value) != '') {
				list(,$dec) = explode(',',$data['type']);
				$dec = (int)$dec;
				
				if($data['type'][1] == 'P')
					$value = number_format($value,$dec,',','');
				else
					$value = CStr::formatNumber($value,$dec,true);
			}
			
			
			
			return $value;
		}
		
		// mendapatkan input dari informasi field
		function getInput($data,$value=null) {
			if($data['readonly'])
				return self::getLabel($data,$value);
			
			if(isset($data['request']))
				$data['default'] = Modul::getRequest($data['request']);
			
			if(!isset($value) and isset($data['default']))
				$value = $data['default'];
			if(isset($value))
				$value = self::getValue($data,$value);
			
			$nameid = $data['nameid'];
			if(empty($nameid))
				$nameid = $data['kolom'];
			
			$size = $data['size'];
			$maxlength = $data['maxlength'];
			if(empty($maxlength))
				$maxlength = $size;
			
			$class = $data['class'];
			if(empty($class))
				$class = 'ControlStyle';
			
			$rows = (int)$data['rows'];
			$cols = (int)$data['cols'];
			
			$option = $data['option'];
			$empty = $data['empty'];
			$add = $data['add'];
			
			switch($data['type'][0]) {
				case 'A':
					if($maxlength != '')
						$add .= ' onkeyup="return charNum(this,'.(int)$maxlength.')"';
					$input = UI::createTextArea($nameid,$value,$class,$rows,$cols,true,$add);
					break;
				case 'C':
					$input = UI::createCheckBox($nameid,$option,$value,true,false,$add);
					break;
				case 'D':
					ob_flush();
					echo UI::createTextBox($nameid,$value,$class,10,10,true,$add)
				?>
					<?php /* <img src="images/cal.png" id="<?= $nameid ?>_trg" style="cursor:pointer;" title="Pilih <?= $data['label'] ?>">  */?>
					<script type="text/javascript">
					Calendar.setup({
						inputField     :    "<?= $nameid ?>",
						ifFormat       :    "%d-%m-%Y",
						button         :    "<?= $nameid ?>",
						align          :    "Br",
						singleClick    :    true
					});
					</script>
				<?php
					$input = ob_get_contents();
					ob_clean();
					break;
				case 'M':
					$input = UI::createTextArea($nameid,$value,$class,$rows,$cols,true,$add);
					break;
				case 'R':
					$input = UI::createRadio($nameid,$option,$value,true,false,$add);
					break;
				case 'S':
					$input = UI::createSelect($nameid,$option,$value,$class,true,$add,($empty ? true : false),($empty === true ? '' : $empty));
					break;
				case 'U':
					$input = '<input type="file" name="'.$nameid.'" id="'.$nameid.'" size="'.$size.'" class="'.$class.'" '.$add.'>';
					break;
				case 'N':
					list(,$dec) = explode(',',$data['type']);
					$add .= ' onkeydown="return onlyNumber(event,this,'.((int)$dec ? 'true' : 'false').','.($data['type'][1] == 'P' ? 'false' : 'true').')"';
				case 'E':
					$a_tgl = array();
					for($i=1;$i<=31;$i++)
						$a_tgl[$i] = $i;
					
					$a_bln = Date::arrayMonth();
					
					$a_thn = array();
					for($i=date('Y');$i>=1970;$i--)
						$a_thn[$i] = $i;
						
					list($thn,$bln,$tgl) = explode('-',$value);
					
					$input = UI::createSelect($nameid.'-tgl',$a_tgl,(int)$tgl,$class,true,$add['tgl']);
					$input .= UI::createSelect($nameid.'-bln',$a_bln,(int)$bln,$class,true,$add['bln']);
					$input .= UI::createSelect($nameid.'-thn',$a_thn,(int)$thn,$class,true,$add['thn']);
					break;
				default:
					$input = UI::createTextBox($nameid,$value,$class,$maxlength,$size,true,$add);
			}
			
			if(!empty($data['infoedit']))
				$input .= ' &nbsp; &nbsp; <em>'.$data['infoedit'].'</em>';
			
			return $input;
		}
		
		// mendapatkan record dari informasi field
		function getPostRecord($data,$apost,$pref='') {
			$post = array();
			$record = array();
			
			foreach($data as $t_data) {
				if($t_data['readonly'])
					continue;
				
				$kolom = CStr::getLastPart($t_data['kolom']);
				$nameid = $t_data['nameid'];
				if(empty($nameid))
					$nameid = $kolom;
				$nameid = $pref.$nameid;
				
				if(!empty($t_data['value']))
					$t_post = $t_data['value'];
				else
					$t_post = $apost[$nameid];
				
				if($t_data['type'][0] == 'N') {
					list(,$dec) = explode(',',$data['type']);
					$t_post = CStr::cStrDec($t_post,(int)$dec);
				}
				else if($t_data['type'][0] == 'E') {
					// cek rentang tanggal
					$cek = checkdate($apost[$nameid.'-bln'],$apost[$nameid.'-tgl'],$apost[$nameid.'-thn']);
					if(empty($cek))
						$t_post = null;
					else
						$t_post = $apost[$nameid.'-thn'].'-'.str_pad($apost[$nameid.'-bln'],2,'0',STR_PAD_LEFT).'-'.str_pad($apost[$nameid.'-tgl'],2,'0',STR_PAD_LEFT);
				}
				else if($t_data['type'] == 'D')
					$t_post = CStr::formatDate($t_post);
				
				$post[$nameid] = $t_post;
				$record[$kolom] = CStr::cStrNull($t_post);
			}
			
			return array($post,$record);
		}
		
		
		// mendapatkan record insert inplace dari informasi field
		function getInsertRecord($data,$apost) {
			return self::getPostRecord($data,$apost,'i_');
		}
		
		// mendapatkan record update inplace dari informasi field
		function getUpdateRecord($data,$apost) {
			return self::getPostRecord($data,$apost,'u_');
		}
		
		// validasi record
		function validateRecord($data,$record) {
			$msg = array();
			foreach($data as $t_data) {
				$t_kolom = $t_data['kolom'];
				$t_record = $record[$t_kolom];
				$t_label = '<strong>'.$t_data['label'].'</strong>';
				
				// not null
				if(!empty($t_data['notnull']) and (!isset($t_record) or $t_record == 'null')) {
					$msg[] = $t_label.' harus diisi';
					continue;
				}
				
				// combo tanggal
				if($t_data['type'] == 'E') {
					list($thn,$bln,$tgl) = explode('-',$t_record);
					
					$cek = checkdate($bln,$tgl,$thn);
					if(empty($cek)) {
						$msg[] = 'Penanggalan '.$t_label.' tidak valid';
						continue;
					}
				}
			}
			
			return array((empty($msg) ? false : true),$msg);
		}
		
		/*******************
		 * FUNGSI AKADEMIK *
		 ******************/
		
		// upload foto
		function getImageInput() {
			// popup
			$input = '	<div id="popFoto" class="menubar" style="position:absolute; display:none;" >
							<table width="100" class="menu-body">
								<tr class="menu-button" onMouseMove="this.className=\'hover\'" onMouseOut="this.className=\'\'">
									<td onClick="setUpload()">Upload Foto</td>
								</tr>
								<tr class="menu-button" onMouseMove="this.className=\'hover\'" onMouseOut="this.className=\'\'">
									<td onClick="goHapusFoto()">Hapus Foto</td>
								</tr>
							</table>
						</div>';
			
			// input file
			$data = array();
			$data['type'] = 'U';
			$data['nameid'] = 'foto';
			$data['size'] = 10;
			$data['add'] = 'onchange="chooseFile()"';
			
			$input .= '<span style="display:none">'.uForm::getInput($data).'</span>';
			
			// iframe
			$input .= '<iframe name="upload_iframe" style="display:none"></iframe>';
			
			// dark and light
			$input .= '<div id="imgfoto_dark" class="Darken" style="display:none"></div>';
			$input .= '	<div id="imgfoto_light" align="center" class="Lighten" style="display:none">
							<img src="images/loading.gif">
						</div>';
			
			return $input;
		}
		
		// foto mahasiswa
		function getPathImageMahasiswa($conn,$key,$show=false) {
			// ambil periode masuknya
			$debug = $conn->debug;
			$conn->debug = false;
			
			$jalur = Pendaftaran::getJalurCamaba($conn,$key);
			if(empty($jalur))
				$jalur = '0000';
			
			$conn->debug = $debug;
			
			global $conf;
			
			$dir = $conf['upload_dir'].'fotocamaba/';
			
			// buat direktori
			if(!$show and !file_exists($dir.$jalur))
				mkdir($dir.$jalur,0775,true);
			
			$file = $dir.$jalur.'/'.$key.'.jpg';
			
			if(!$show or file_exists($file)) {
				$img = $file;
				if($show)
					$img .= '?r='.mt_rand(10000,99999);
			}
			else
				$img = $dir.'default.jpg';
			
			return $img;
		}
		
		// foto mahasiswa
		function getPathImageTemp($conn,$key,$show=false) {
			global $conf;
			if($key == 'default')
				$dir = $conf['upload_dir'].'fotocamaba/';
			else
				$dir = $conf['upload_dir'].'fotocamaba/temp/';
			
			// buat direktori
			if(!$show and !file_exists($dir))
				mkdir($dir,0755,true);
			
			$file = $dir.'/'.$key.'.jpg';
			
			if(!$show or file_exists($file)) {
				$img = $file;
				if($show)
					$img .= '?r='.mt_rand(10000,99999);
			}
			else
				$img = $dir.'default.jpg';
			
			return $img;
		}
		
		function getImageMahasiswa($conn,$key,$edit=true) {
			if($edit) {
				$img = '<img id="imgfoto" border="1" src="'.self::getPathImageMahasiswa($conn,$key,true).'" style="cursor:pointer">';
				$img .= self::getImageInput();
			}
			else{
				$img = '<img id="imgfoto" border="1" src="'.self::getPathImageMahasiswa($conn,$key,true).'">';
			}
			return $img;
		}
		
		function getImageMahasiswa3x4($conn,$key) {			
			$img = '<img id="imgfoto" border="1" src="'.self::getPathImageMahasiswa($conn,$key,true).'" style="cursor:pointer;width:2.6cm;height:3.6cm">';				
			return $img;
		}
		
		function getImageTemp($conn,$key,$edit=true) {
			if($edit) {
				$img = '<img id="imgfoto" border="1" src="'.self::getPathImageTemp($conn,$key,true).'" style="cursor:pointer">';
				$img .= self::getImageInput();
			}
			else{
				$img = '<img id="imgfoto" border="1" src="'.self::getPathImageTemp($conn,$key,true).'">';
			}
			
			return $img;
		}
		
		function reloadImageMahasiswa($conn,$key,$alert=false) {
?>
			<html>
				<body>
					<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
					<script type="text/javascript" src="scripts/common.js"></script>
					
					<script type="text/javascript">
						<? if($alert !== false) { ?>
						alert("<?= $alert ?>");
						<? } ?>
						
						var img = parent.$("#imgfoto");
						
						img.attr("src","<?= self::getPathImageMahasiswa($conn,$key,true) ?>");
						img.hideWait();
						
						parent.initRefresh();
					</script>
				</body>
			</html>
<?php
		}
		
		function reloadImageTemp($conn,$key,$alert=false){
?>
			<html>
				<body>
					<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
					<script type="text/javascript" src="scripts/common.js"></script>
					
					<script type="text/javascript">
						<? if($alert !== false) { ?>
						alert("<?= $alert ?>");
						<? } ?>
						
						var img = parent.$("#imgfoto");
						
						img.attr("src","<?= self::getPathImageTemp($conn,$key,true) ?>");
						img.hideWait();
						
						parent.$('#namafoto').val() = '<?= $key?>';
						parent.initRefresh();
					</script>
				</body>
			</html>
<?php
		}
	}
?>
