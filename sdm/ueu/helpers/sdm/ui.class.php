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
				$option .= '<option value="'.$key.'"'.(!strcasecmp($value,$key) ? ' selected' : '').'>';
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
						$radio .= '<label for="'.$nameid.'_'.$key.'" '.$add.'>'.$val.'</label>';
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
		
		function tdKegKalender($arrkeg,$tgl=false,$wlabel=200,$click=false,$onclick='',$chosen=false) {
			if(empty($arrkeg)) {
				$arrkeg['bgcolor'] = '';
			}
			
			if(!is_array($arrkeg[key($arrkeg)])) {
				$tempkeg = $arrkeg;
				unset($arrkeg);
				$arrkeg[0] = $tempkeg;
			};
			$bgcolor = '';
			$subbgcolor = '';
			$subbg = array();
			$substyle = '';
			foreach($arrkeg as $keg) {
				if($tgl !== false) {
					$nohari = date('N',strtotime($tgl));
					$notgl = (int)substr($tgl,-2);
					
					if(empty($notgl))
						$bgcolor = '#999';
					else if(!empty($keg['bgcolor']))
						$bgcolor = $keg['bgcolor'];
					else if($nohari == 6)
						$bgcolor = '#7FCC7A';
					else if($nohari == 7)
						$bgcolor = '#F00';
					
					if(!empty($keg['jamdatang'])){
						$waktu = substr($keg['jamdatang'],0,2).':'.substr($keg['jamdatang'],2,4).' s/d '.substr($keg['jampulang'],0,2).':'.substr($keg['jampulang'],2,4);
						$label = '<font size="-2"><strong><em>'.$waktu.'</em></strong></font>';
					}else
						$label = '';
				}
				
				 if(!empty($keg['bgcolorsub']))
					$subbgcolor = $keg['bgcolorsub'];
				
				if(!empty($subbgcolor) and $bgcolor != $subbgcolor) {
					$substyle = 'border:1px solid #999;';
					$subborder = true;
				}
			}
				
			if(!empty($bgcolor))
				$bgcolor = ' bgcolor="'.$bgcolor.'"';
			if(!empty($subbgcolor))
				$subbgcolor = ' bgcolor="'.$subbgcolor.'"';
			
			$subbg = implode(',',$subbg);
			if(!empty($subbg))
				$substyle .= 'background-image:'.$subbg;
			if(!empty($substyle))
				$substyle = ' style="'.$substyle.'"';
			
			$add = '';
			if($click !== false) {
				$add = ' style="cursor:pointer;';
				if($chosen)
					$add .= 'border:2px solid #004A5B';
				$add .= '"';
				
				if($click == 1)
					$add .= ' title="Klik untuk memilih jadwal"';
				else if($click == 2)
					$add .= ' title="Klik untuk memilih tanggal"';
				
				if(!empty($onclick))
					$add .= ' onclick="'.$onclick.'"';
			}
	?>
			<td align="center"<?= $bgcolor ?><?= empty($notgl) ? '&nbsp;' : $add; ?>>
				<table cellpadding="4" cellspacing="0"<?= $subbgcolor ?><?= $substyle ?>>
					<tr><td align="center" width="<?= $subborder ? 30 : 100 ?>" height="<?= $subborder ? 24 : 26 ?>">
					<?= empty($notgl) ? '&nbsp;' : $notgl ?><br />
					<?= empty($notgl) ? '&nbsp;' : $label; ?>
					</td>
					</tr>
				</table>
			</td>
			<? if(!empty($wlabel)) { ?>
			<td width="<?= $wlabel ?>"<?= $tgl === false ? '' : ' align="center"' ?>><?= $label ?></td>
			<? } ?>
	<?
		}
	}
?>
