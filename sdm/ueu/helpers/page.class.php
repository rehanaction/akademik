<?php
	// fungsi pembantu modul
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Page {
		// mendapatkan data kolom dengan index kolom db
		function getColumnKey($kolom) {
			$data = array();
			foreach($kolom as $datakolom) {
				$key = $datakolom['kolom'];
				$data[$key] = $datakolom;
			}
			
			return $data;
		}
		
		// mendapatkan field recordset berdasarkan data kolom
		function getColumnRow($kolom,$row) {
			static $no = 0;
			
			$data = array();
			foreach($kolom as $datakolom) {
				if($datakolom['kolom'] == ':no') {
					$value = ++$no.'.';
				}
				else {
					if(empty($datakolom['alias']))
						$field = CStr::getLastPart($datakolom['kolom']);
					else
						$field = $datakolom['alias'];
					
					$value = $row[$field];
				}
				
				$data[] = $value;
			}
			
			return $data;
		}
		
		// mendapatkan edit field berdasarkan data kolom
		function getColumnEdit($kolom,$pref='',$add='',$row='') {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$data = array();
			foreach($kolom as $datakolom) {
				if($datakolom['kolom'] == ':no') {
					global $no;
					
					if($pref == 'i_')
						$value = '*';
					else
						$value = ++$no.'.';
					
					$data[] = array('input' => $value);
				}
				else {
					$field = CStr::getLastPart($datakolom['kolom']);
					
					$datakolom['nameid'] = $pref.$field;
					
					/* if($datakolom['type'] == 'D')
						$value = (empty($row['real_'.$field]) ? $row[$field] : $row['real_'.$field]); */
					if(isset($row['real_'.$field]))
						$value = $row['real_'.$field];
					else
						$value = $row[$field];
					
					$input = uForm::getInput($datakolom,$value);
					
					$data[] = array('id' => $pref.$field, 'input' => $input, 'notnull' => $datakolom['notnull']);
				}
			}
			
			return $data;
		}

		
		// mendapatkan label data
		function getDataLabel($row,$field) {
			if(!is_array($field)) {
				if(($pos = strpos($field,',')) === false)
					$field = array($field);
				else
					$field = explode(',',$field);
			}
			
			foreach($field as $t_field) {
				$t_field = trim($t_field);
				$t_found = false;
				
				foreach($row as $t_row) {
					if($t_row['id'] == $t_field) {
						if(!empty($t_row['label']))
							$t_found = true;
						break;
					}
				}
				
				if($t_found)
					break;
			}
			
			if(!$t_found)
				return false;
			
			$label = $t_row['label'];
			
			if($t_row['notnull'])
				$label .= ' <span id="edit" style="display:none">*</span>';
			
			return $label;
		}
		
		// mendapatkan input data
		function getDataInput($row,$field,$separator=' ',$def='') {
			if(!is_array($field)) {
				if(($pos = strpos($field,',')) === false)
					$field = array($field);
				else
					$field = explode(',',$field);
			}
			
			$t_value = array();
			$t_input = array();
			foreach($field as $t_field) {
				$t_field = trim($t_field);
				
				foreach($row as $t_row)
					if($t_row['id'] == $t_field)
						break;
				if($t_row['id'] != $t_field)
					continue;
				
				$t_value[] = $t_row['value'];
				$t_input[] = $t_row['input'];

				if(!empty($def)) $t_value[] = $def;
			}
			
			if(empty($t_value))
				return false;
			
			return self::getDataInputWrap(implode($separator,$t_value),implode($separator,$t_input),($t_row['hidden']) ? false : true);
		}
		
		function getDataInputWrap($show,$edit,$isshow=true) {
            $data = '';
            
            if($isshow)
			    $data .= '<span id="show">'.$show.'</span>';
			
			$data .= '<span id="edit" style="display:none">'.$edit.'</span>';
			
			return $data;
		}
		
		// mendapatkan input data
		function getDataInputInc($row,$field) {
			if(!is_array($field)) {
				if(($pos = strpos($field,',')) === false)
					$field = array($field);
				else
					$field = explode(',',$field);
			}
			
			$t_value = array();
			$t_input = array();
			foreach($field as $t_field) {
				$t_field = trim($t_field);
				
				foreach($row as $t_row)
					if($t_row['id'] == $t_field)
						break;
				if($t_row['id'] != $t_field)
					continue;
				
				$t_value[] = $t_row['value'];
				$t_input[] = $t_row['input'];
			}
			
			if(empty($t_value))
				return false;
			
			$data = implode($separator,$t_input);
			
			return $data;
		}
		
		// mendapatkan nilai data
		function getDataValue($row,$field) {
			foreach($row as $t_row)
				if($t_row['id'] == $field)
					break;
			
			if($t_row['id'] == $field)
				return $t_row['realvalue'];
			else
				return false;
		}
		
		// mendapatkan tr data
		function getDataTR($row,$field,$separator=' ') {
			$tr = '<tr>'."\n";
			$tr .= '<td class="LeftColumnBG" width="120" style="white-space:nowrap">'."\n";
			$tr .= Page::getDataLabel($row,$field);
			$tr .= '</td>'."\n";
			$tr .= '<td class="RightColumnBG">'."\n";
			$tr .= Page::getDataInput($row,$field,$separator);
			$tr .= '</td>'."\n";
			$tr .= '</tr>';
			
			return $tr;
		}
		
		// mendapatkan data detail
		function getDetailTable($row,$kolom,$field,$label,$no=true,$c_edit=true,$c_delete=true) {
			ob_flush();
			
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$rowd = $row[$field];
			$key = $kolom[$field]['key'];
			$kolom = $kolom[$field]['data'];
			$colspan = count($kolom)+2;
			
			if(!is_array($key))
				$key = explode(',',$key);
?>
			<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				<tr>
					<td colspan="<?= $colspan ?>" class="DataBG"><?= $label ?></td>
				</tr>
				<tr>
				<?	if($no) { ?>
					<th align="center" class="HeaderBG" width="30">No</th>
				<?	}
					foreach($kolom as $datakolom) { ?>
					<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
				<?	} ?>
					<th align="center" class="HeaderBG" width="30" id="edit" style="display:none">Aksi</th>
				</tr>
				<?	$i = 0;
					foreach($rowd as $row) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
						
						$t_keyrow = array();
						foreach($key as $t_key)
							$t_keyrow[] = $row[trim($t_key)];
						
						$t_key = implode('|',$t_keyrow);
				?>
				<tr valign="top" class="<?= $rowstyle ?>">
				<?	if($no) { ?>
					<td><?= $i ?></td>
				<?	}
					foreach($kolom as $datakolom) { ?>
					<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?= uForm::getLabel($datakolom,$row[$datakolom['kolom']]) ?></td>
				<?	} ?>
					<td id="edit" align="center" style="display:none">
						<? if ($c_delete) {?>
						<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('<?= $field ?>',this)" style="cursor:pointer">
						<? } ?>
					</td>
				</tr>
				<?	}
					if($i == 0) { ?>
				<tr>
					<td align="center" colspan="<?= $colspan ?>">Data kosong</td>
				</tr>
				<?	} ?>
				
				<? if ($c_edit) {?>
				<tr valign="top" class="LeftColumnBG" id="edit" style="display:none">
				<?	if($no) { ?>
					<td>*</td>
				<?	}
					foreach($kolom as $datakolom) {
						$datakolom['nameid'] = $field.'_'.CStr::cEmChg($datakolom['nameid'],$datakolom['kolom']);
				?>
					<td><?= uForm::getInput($datakolom) ?></td>
				<?	} ?>
					<td align="center">
						<img title="Tambah Data" src="images/disk.png" onclick="goInsertDetail('<?= $field ?>')" style="cursor:pointer">
					</td>
				</tr>
				<? } ?>
			</table>
<?php
			return ob_get_clean();
		}
		
		// mendapatkan filter, untuk pager
		function getFilter($kolom) {
			$data = array();
			$cfilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTER'];
			
			if(!empty($cfilter)) {
				foreach($cfilter as $t_filter) {
					list($t_col,$t_str) = explode(':',$t_filter);
					$t_str = CStr::removeSpecial($t_str,false);
					
					foreach($kolom as $t_datakolom)
						if (!empty($t_datakolom['filter'])){
							if($t_col == $t_datakolom['filter'])
								$data[] = array('kolom' => $t_datakolom['kolom'], 'label' => $t_datakolom['label'], 'str' => $t_str);
						}else{
							if($t_col == $t_datakolom['kolom'])
								$data[] = array('kolom' => $t_col, 'label' => $t_datakolom['label'], 'str' => $t_str);
						}
				}
			}
			return $data;
		}
		
		function getFilterTree() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERTREE'];
		}
		
		// mengambil halaman terakhir
		function getLastPage() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['LASTPAGE'];
		}
		
		function getTheLastPage() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['THELASTPAGE'];
		}
		
		// mengambil waktu kueri
		function getListTime() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['LISTTIME'];
		}
		
		// mengambil jumlah data
		function getRowNum() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['ROWNUM'];
		}
		
		// menyimpan filter
		function setFilter($filter,$filterdef='') {
			$cfilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTER'];
			$ofilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERTREE'];
			$afilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERALL'];
			$asfilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERALLSTR'];
			
			if($filter != '') {
				if(strpos($filter,"'") === false)
					$filter = CStr::removeSpecial($filter);
				else
					$filter = $filter;
				
				if(is_numeric($filter))
					unset($cfilter[$filter]);
				else if(substr($filter,0,5) == 'tree|') {
					if($filter == 'tree|') {
						$filter = '';
						$rfilter = array();
						$ofilter = array();
					}
					else {
						if(substr($filter,0,7) == 'tree|t|') {
							$filter = substr($filter,7);
							if(empty($ofilter))
								$ofilter = array();
						}
						else {
							$filter = substr($filter,5);
							$ofilter = array(); // reset filter
						}
						
						$rfilter = explode('|',$filter);
						
						foreach($rfilter as $t_filter) {
							if(($t_idx = array_search($t_filter,$ofilter)) === false)
								$ofilter[] = $t_filter;
							else
								unset($ofilter[$t_idx]);
						}
					}
					
					$_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERTREE'] = $ofilter;
				}
				else if(substr($filter,0,4) == 'all|') {
					if($filter == 'all|') {
						$t_str = '';
						$filter = '';
						$rfilter = array();
						$afilter = array();
						$asfilter = array();
					}
					else {
						$filter = substr($filter,4);
						$rfilter = explode('|',$filter);
						
						$t_str = $rfilter[0];
						$rfilter = array_slice($rfilter,1);
						
						$afilter = array();
						$asfilter = $t_str;
						foreach($rfilter as $t_filter) {
							if($t_filter[0] == ':')
								continue;
							
							$afilter[] = $t_filter.':'.$t_str;
						}
					}
					
					$_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERALL'] = $afilter;
					$_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTERALLSTR'] = $asfilter;
				}
				else
					$cfilter[] = $filter;
				
				if(!empty($cfilter))				
					sort($cfilter);
				
				$_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTER'] = $cfilter;
			}
			
			$sfilter = array();
			if(!empty($cfilter)) {
				foreach($cfilter as $t_filter) {
					list($t_col,$t_str) = explode(':',$t_filter);
					$t_str = CStr::removeSpecial($t_str,false);
					$sfilter[] = Query::colFilter($t_col,$t_str);
				}
			}
			
			// filter tree
			if(!empty($ofilter)) {
				$osfilter = array();
				foreach($ofilter as $t_filter) {
					list($t_col,$t_str) = explode(':',$t_filter);
					$osfilter[$t_col][] = $t_str;
				}
				
				$ofilter = array();
				foreach($osfilter as $t_col => $t_filter) {
					$opfilter = array();
					foreach($t_filter as $t_idx => $t_val) {
						if(!empty($filterdef[$t_col.':'.$t_val])) {
							$opfilter[] = $filterdef[$t_col.':'.$t_val];
							unset($t_filter[$t_idx]);
						}
					}
					
					if(!empty($filterdef[$t_col]))
						$t_col = $filterdef[$t_col];
					if(!empty($t_filter))
						$opfilter[] = $t_col." in ('".implode("','",$t_filter)."')";
					if(!empty($opfilter))
						$ofilter[] = implode(' or ',$opfilter);
				}
				
				$sfilter[] = '('.implode(' and ',$ofilter).')';
			}
			
			// filter all
			if(!empty($afilter)) {
				$asfilter = array();
				foreach($afilter as $t_filter) {
					list($t_col,$t_str) = explode(':',$t_filter);
					$asfilter[] = Query::colFilter($t_col,$t_str);
				}
				$sfilter[] = '('.implode(' or ',$asfilter).')';
			}
			
			return $sfilter;
		}
		
		// menyimpan halaman terakhir
		function setLastPage($page) {
			$_SESSION[SITE_ID]['EX'][Route::thisPage()]['LASTPAGE'] = $page;
		}
		
		function setTheLastPage($page) {
			$_SESSION[SITE_ID]['EX'][Route::thisPage()]['THELASTPAGE'] = $page;
		}
		
		// menyimpan waktu kueri
		function setListTime($time) {
			$_SESSION[SITE_ID]['EX'][Route::thisPage()]['LISTTIME'] = $time;
		}
		
		// menyimpan jumlah data
		function setRowNum($num) {
			$_SESSION[SITE_ID]['EX'][Route::thisPage()]['ROWNUM'] = $num;
		}
		
		// menyimpan page
		function setPage($page) {
			if(!empty($page)) {
				$page = (int)$page;
				$_SESSION[SITE_ID]['EX'][Route::thisPage()]['PAGE'] = $page;
			}
			else
				$page = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['PAGE'];
			
			if(empty($page))
				$page = 1;
			
			return $page;
		}
		
		// menyimpan row, untuk pager
		function setRow($row) {
			if(!empty($row)) {
				$row = (int)$row;
				$_SESSION[SITE_ID]['EX'][Route::thisPage()]['ROW'] = $row;
			}
			else
				$row = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['ROW'];
			
			if(empty($row))
				$row = 10;
			
			return $row;
		}
		
		// menyimpan sort
		function setSort($sort) {
			if(!empty($sort)) {
				$sort = CStr::removeSpecial($sort);
				
				if(!empty($_SESSION[SITE_ID]['EX'][Route::thisPage()]['SORT'])) {
					list($t_col,$t_dir) = explode(' ',$_SESSION[SITE_ID]['EX'][Route::thisPage()]['SORT']);
					
					if($sort == $t_col) {
						if($t_dir == 'desc')
							$t_dir = 'asc';
						else
							$t_dir = 'desc';
						
						$sort = $t_col.' '.$t_dir;
					}
				}
				
				$_SESSION[SITE_ID]['EX'][Route::thisPage()]['SORT'] = $sort;
			}
			else
				$sort = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['SORT'];
			
			return $sort;
		}
		
		// membuat foto
		function createFoto($src,$dest,$xw=0,$xh=0) {
			if(($rsize = getimagesize($src)) === false)
				return -1; // bukan image
			
			$rw = $rsize[0]; $rh = $rsize[1];
			if($rw > $rh or ($rw == $rh and $xw < $xh)) { // lebih kecil max width atau sama
				$nw = $xw;
				$nh = round(($nw*$rh)/$rw);
			}
			else if($rw < $rh or ($rw == $rh and $xw > $xh)) { // lebih kecil max height atau sama
				$nh = $xh;
				$nw = round(($nh*$rw)/$rh);
			}
			else { // semua parameter max ukuran 0, disamakan
				$nw = $xw;
				$nh = $xh;
			}
			
			switch($rsize[2]) {
				case IMAGETYPE_GIF: $rimg = imagecreatefromgif($src); break;
				case IMAGETYPE_JPEG: $rimg = imagecreatefromjpeg($src); break;
				case IMAGETYPE_PNG: $rimg = imagecreatefrompng($src); break;
				default: return -2; // format image tidak dikenali
			}
			
			$nimg = imagecreatetruecolor($nw,$nh);
			imagecopyresized($nimg, $rimg, 0, 0, 0, 0, $nw, $nh, $rw, $rh);
			$return = imagejpeg($nimg,$dest);
			
			imagedestroy($rimg);
			imagedestroy($nimg);
			
			if($return === true)
				return 1;
			else
				return -3; // tidak bisa menulis image tujuan
		}
		
		function saveWkPDF($file) {
			global $conf;
			
			require_once($conf['includes_dir'].'phpwkhtmltopdf/WkHtmlToPdf.php');
			
			$html = ob_get_contents();
			ob_end_clean();
			
			$temp = 'pdf_'.session_id().'.html';
			file_put_contents($conf['temp_dir'].$temp,$html);
		
			$pdf = new WkHtmlToPdf(array(
				// 'no-outline',		// Make Chrome not complain
				/* 'margin-top'    => 0,
				'margin-right'  => 0,
				'margin-bottom' => 0,
				'margin-left'   => 0, */
			));
			
			$pdf->setPageOptions(array(
				'disable-smart-shrinking',
				// 'user-style-sheet' => 'pdf.css',
			));
			
			$pdf->addPage($conf['temp_dir'].$temp);
			$pdf->send($file);
			
			@unlink($temp);
		}
		
		function downFile($fullPath){
			if (is_readable($fullPath)){
				$handle = fopen($fullPath,'rb');
				$contents = fread($handle,filesize($fullPath));
				fclose($handle);
				
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$ext = finfo_file($finfo,$fullPath);
				finfo_close($finfo);
				
				ob_clean();
				
				header("Content-Type: $ext");
				header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"');
				
				echo $contents;
			}else
				$p_postmsg = 'Maaf, File tidak ditemukan';
			
			return $p_postmsg;
		}
	}
?>
