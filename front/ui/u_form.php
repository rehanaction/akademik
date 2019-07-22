<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class uForm {
		// mendapatkan label dari informasi field (nilai di luar input)
		function getLabel($data,$value) {
			$value = trim($value);
			
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
					$value = CStr::formatNumber($value,$dec);
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
			
			return $value;
		}
		
		// mendapatkan nilai dari informasi field (nilai di dalam input)
		function getValue($data,$value) {
			$value = trim($value);
			
			if($data['type'] == 'D')
				$value = CStr::formatDate($value);
			else if($data['type'][0] == 'N' and strval($value) != '') {
				list(,$dec) = explode(',',$data['type']);
				$dec = (int)$dec;
				
				if($data['type'][1] == 'P')
					$value = number_format($value,$dec,',','');
				else
					$value = CStr::formatNumber($value,$dec);
			}
			
			return $value;
		}
		
		// mendapatkan input dari informasi field
		function getInput($data,$value=null) {
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
			
			$rows = $data['rows'];
			$cols = $data['cols'];
			
			$option = $data['option'];
			$empty = $data['empty'];
			$add = $data['add'];
			
			switch($data['type'][0]) {
				case 'A':
					if($maxlength != '')
						$add .= ' onkeyup="return charNum(this,'.(int)$maxlength.')"';
					return UI::createTextArea($nameid,$value,'ControlStyle',$rows,$cols,true,$add);
				case 'C': return UI::createCheckBox($nameid,$option,$value,true,false,$add);
				case 'D':
					ob_flush();
					echo UI::createTextBox($nameid,$value,'ControlStyle',10,10)
?>
			<img src="images/cal.png" id="<?= $nameid ?>_trg" style="cursor:pointer;" title="Pilih <?= $data['label'] ?>">
			<script type="text/javascript">
			Calendar.setup({
				inputField     :    "<?= $nameid ?>",
				ifFormat       :    "%d-%m-%Y",
				button         :    "<?= $nameid ?>_trg",
				align          :    "Br",
				singleClick    :    true
			});
			</script>
<?php
					return ob_get_clean();
				case 'R': return UI::createRadio($nameid,$option,$value,true,false,$add);
				case 'S': return UI::createSelect($nameid,$option,$value,'ControlStyle',true,$add,$empty);
				case 'N':
					list(,$dec) = explode(',',$data['type']);
					$add .= ' onkeydown="return onlyNumber(event,this,'.((int)$dec ? 'true' : 'false').',true)"';
				default: return UI::createTextBox($nameid,$value,'ControlStyle',$maxlength,$size,true,$add);
			}
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
				
				if($t_data['type'] == 'D')
					$t_post = CStr::formatDate($t_post);
				else if($data['type'][0] == 'N') {
					list(,$dec) = explode(',',$data['type']);
					$t_post = CStr::cStrDec($t_post,(int)$dec);
				}
				
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
	}
?>