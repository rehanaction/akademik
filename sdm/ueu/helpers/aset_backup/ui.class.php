<?php
	// fungsi user interface
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class UI {
		// membuat textarea
		function createTextArea($nameid,$value='',$class='',$rows='',$cols='',$edit=true,$add='') {
			if(!empty($edit)) {
				$ta = '<textarea wrap="soft" name="'.$nameid.'" id="'.$nameid.'"';
				if($class != '') $ta .= ' class="'.$class.'"';
				if($rows != '') $ta .= ' rows="'.$rows.'"';
				if($cols != '') $ta .= ' cols="'.$cols.'"';
				if($add != '') $ta .= ' '.$add;
				$ta .= '>';
				if($value != '') $ta .= $value;
				$ta .= '</textarea>';
			}
			else if($value == '')
				$ta = '&nbsp;';
			else
				$ta = nl2br($value);
			
			return $ta;
		}
		
		// membuat textbox
		function createTextBox($nameid,$value='',$class='',$maxlength='',$size='',$edit=true,$add='') {
			$value = strval($value);
			
			if(!empty($edit)) {
				$tb = '<input type="text" name="'.$nameid.'" id="'.$nameid.'"';
				if($value != '') $tb .= ' value="'.$value.'"';
				if($class != '') $tb .= ' class="'.$class.'"';
				if($maxlength != '') $tb .= ' maxlength="'.$maxlength.'"';
				if($size != '') $tb .= ' size="'.$size.'"';
				if($add != '') $tb .= ' '.$add;
				$tb .= '>';
			}
			else if($value == '')
				$tb = '&nbsp;';
			else
				$tb = $value;
			
			return $tb;
		}
		
		// membuat option combo box
		function createOption($arrval,$value='',$emptyrow=false,$emptylabel='') {
			$option = '';
			
			if($emptyrow)
				$option .= '<option value="">'.$emptylabel.'</option>'."\n";
			foreach($arrval as $key => $val) {
				if($key[0] == '|') $key = '';
				
				if(!strcasecmp($value,$key) and !$hasselected) {
					$selected = true;
					$hasselected = true;
				}
				else
					$selected = false;
				
				$option .= '<option value="'.$key.'"'.($selected ? ' selected' : '').'>';
				$option .= $val.'</option>'."\n";
			}
			
			return $option;
		}
		
		// membuat combo box
		function createSelect($nameid,$arrval='',$value='',$class='',$edit=true,$add='',$emptyrow=false,$emptylabel='') {
			if(!empty($edit)) {
				$slc = '<select name="'.$nameid.'" id="'.$nameid.'"';
				if($class != '') $slc .= ' class="'.$class.'"';
				if($add != '') $slc .= ' '.$add;
				$slc .= ">\n";
				if(is_array($arrval))
					$slc .= UI::createOption($arrval,$value,$emptyrow,$emptylabel);
				$slc .= '</select>';
			}
			else {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						if(!strcasecmp($value,$key)) {
							$slc = $val;
							break;
						}
					}
					$slc = str_replace('&nbsp;','',$slc); // nbsp untuk tree dimusnahkan
				}
			}
			
			return $slc;
		}
		
		// membuat radio button
		function createRadio($nameid,$arrval='',$value='',$edit=true,$br=false,$add='') {
			$radio = '';
			
			if(!empty($edit)) {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						$radio .= '<input type="radio" name="'.$nameid.'" id="'.$nameid.'_'.$key.'" value="'.$key.'"'.(!strcasecmp($value,$key) ? ' checked' : '').' '.$add.'>';
						$radio .= '<label for="'.$name.'_'.$key.'" '.$add.'>'.$val.'</label>';
						$radio .= ($br ? '<br>' : ' ');
					}
				}
			}
			else {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						if(!strcasecmp($value,$key)) {
							$radio = $val;
							break;
						}
					}
				}
			}
			
			return $radio;
		}
		
		// membuat check box
		function createCheckBox($nameid,$arrval='',$value='',$edit=true,$br=false,$add='') {
			$check = '';
			
			if(!empty($edit)) {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						$check .= '<input type="checkbox" name="'.$nameid.'" id="'.$nameid.'_'.$key.'" value="'.$key.'"'.(!strcasecmp($value,$key) ? ' checked' : '').' '.$add.'>';
						if(!empty($val))
							$check .= ' <label for="'.$nameid.'_'.$key.'" '.$add.'>'.$val.'</label>';
						$check .= ($br ? '<br>' : ' ');
					}
				}
			}
			else {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						if(!strcasecmp($value,$key)) {
							if($val == '')
								$check = '<img src="images/check.png">';
							else
								$check = $val;
							break;
						}
					}
				}
			}
			
			return $check;
		}
		
		// membuat check box multi value true false
		function createCheckBoxMulti($arrval='',$arrcheck='',$edit=true,$br=false,$add='') {
			$check = '';
			
			if(!empty($edit)) {
				if(is_array($arrval)) {
					foreach($arrval as $key => $val) {
						$check .= '<input type="checkbox" name="'.$key.'" id="'.$key.'" value="1"'.(empty($arrcheck[$key]) ? '' : ' checked').' '.$add.'> ';
						$check .= '<label for="'.$key.'" '.$add.'>'.$val.'</label>';
						$check .= ($br ? '<br>' : ' ');
					}
				}
			}
			else {
				if(is_array($arrval)) {
					$check = array();
					foreach($arrval as $key => $val) {
						if(!empty($arrcheck[$key]))
							$check[] = $val;
					}
					$check = implode(', ',$check);
				}
			}
			
			return $check;
		}
		
		// fungsi membuat menu
		function createMenu($arrmenu,&$i) {
			if(empty($arrmenu[$i]))
				return '';
			
			$menu = $arrmenu[$i];
			$level = $menu['levelmenu'];
			$nextlevel = $arrmenu[$i+1]['levelmenu'];
			
			if(empty($menu['namafile']))
				$href = 'javascript:void(0)';
			else
				$href = 'index.php?page='.$menu['namafile'];
			
			$str = '<li><a href="'.$href.'">'.$menu['namamenu'].'</a>';
			if($nextlevel > $level) {
				if($level == 0)
					$class = 'subnav';
				else
					$class = 'leafnav';
				
				$str .= "\n".'<ul class="'.$class.'">'."\n";
				$str .= UI::createMenu($arrmenu,++$i);
				$str .= '</ul>'."\n";
			}
			else
				$str .= '</li>'."\n";
			
			$nextlevel = $arrmenu[$i+1]['levelmenu'];
			if($nextlevel < $level)
				return $str;
			else
				return $str.UI::createMenu($arrmenu,++$i);
		}
	}
?>