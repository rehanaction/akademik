<?php
	// fungsi pembantu modul
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Page {
		static $no = 0;
		
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
			global $no;
			
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
				$field = CStr::getLastPart($datakolom['kolom']);
				
				$datakolom['nameid'] = $pref.$field;
				
				/* if($datakolom['type'] == 'D')
					$value = (empty($row['real_'.$field]) ? $row[$field] : $row['real_'.$field]); */
				if(!empty($row['real_'.$field]))
					$value = $row['real_'.$field];
				else
					$value = $row[$field];
				
				$input = uForm::getInput($datakolom,$value);
				
				$data[] = array('id' => $pref.$field, 'input' => $input, 'notnull' => $datakolom['notnull']);
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
		
		// mendapatkan input data sederhana
		function getDataInputOnly($row,$field) {
			foreach($row as $t_row)
				if($t_row['id'] == $field)
					break;
			
			if($t_row['id'] == $field)
				return $t_row['input'];
			else
				return false;
		}
		
		// me-wrap data dan inputnya
		function getDataInputWrap($show,$edit,$isshow=true) {
            $data = '';
            
            if($isshow)
			    $data .= '<span id="show">'.$show.'</span>';
			
			$data .= '<span id="edit" style="display:none">'.$edit.'</span>';
			
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
		function getDetailTable($row,$kolom,$field,$label,$withno=true,$edit=true) {
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
				<?	if($withno) { ?>
					<th align="center" class="HeaderBG" width="30">No</th>
				<?	}
					foreach($kolom as $datakolom) { ?>
					<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
				<?	}
					if($edit) { ?>
					<th align="center" class="HeaderBG" width="30" id="edit" style="display:none">Aksi</th>
				<?	} ?>
				</tr>
				<?	$i = 0;
					if(!empty($rowd)) {
						foreach($rowd as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_keyrow = array();
							foreach($key as $t_key)
								$t_keyrow[] = $row[trim($t_key)];
							
							$t_key = implode('|',$t_keyrow);
				?>
				<tr valign="top" class="<?= $rowstyle ?>">
				<?		if($withno) { ?>
					<td><?= $i ?></td>
				<?		}
						foreach($kolom as $datakolom) { ?>
					<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?= uForm::getLabel($datakolom,$row[$datakolom['kolom']]) ?></td>
				<?		}
						if($edit) { ?>
					<td id="edit" align="center" style="display:none">
						<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('<?= $field ?>',this)" style="cursor:pointer">
					</td>
				<?		} ?>
				</tr>
				<?		}
					}
					if($i == 0) { ?>
				<tr>
					<td align="center" colspan="<?= $colspan ?>">Data kosong</td>
				</tr>
				<?	}
					if($edit) { ?>
				<tr valign="top" class="LeftColumnBG" id="edit" style="display:none">
				<?	if($withno) { ?>
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
				<?	} ?>
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
					
					foreach($kolom as $t_datakolom)
						if($t_col == $t_datakolom['kolom'])
							$data[] = array('kolom' => $t_col, 'label' => $t_datakolom['label'], 'str' => $t_str);
				}
			}
			
			return $data;
		}
		
		// mengambil halaman terakhir
		function getLastPage() {
			return $_SESSION[SITE_ID]['EX'][Route::thisPage()]['LASTPAGE'];
		}
		
		// menyimpan filter
		function setFilter($filter) {
			$cfilter = $_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTER'];
			
			if($filter != '') {
				$filter = CStr::removeSpecial($filter);
				
				if(is_numeric($filter))
					unset($cfilter[$filter]);
				else
					$cfilter[] = $filter;
				
				sort($cfilter);
				
				$_SESSION[SITE_ID]['EX'][Route::thisPage()]['FILTER'] = $cfilter;
			}
			
			$sfilter = array();
			if(!empty($cfilter)) {
				foreach($cfilter as $t_filter) {
					list($t_col,$t_str) = explode(':',$t_filter);
					$sfilter[] = Query::colFilter($t_col,$t_str);
				}
			}
			
			return $sfilter;
		}
		
		// menyimpan halaman terakhir
		function setLastPage($page) {
			$_SESSION[SITE_ID]['EX'][Route::thisPage()]['LASTPAGE'] = $page;
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
			if($sort[0] == ':')
				$sort = '';
			
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
		
		// header untuk laporan
		function setHeaderFormat($format,$namafile) {
			switch($format) {
				case 'doc';
					ob_clean();
					header("Content-Type: application/msword");
					header('Content-Disposition: attachment; filename="'.$namafile.'.doc"');
					break;
				case 'xls' :
					ob_clean();
					header("Content-Type: application/msexcel");
					header('Content-Disposition: attachment; filename="'.$namafile.'.xls"');
					break;
				default : header("Content-Type: text/html");
			}
		}
	}
?>
