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
				$field = CStr::getLastPart($datakolom['kolom']);
				
				$datakolom['nameid'] = $pref.$field;
				
				if($datakolom['type'] == 'D')
					$value = (empty($row['real_'.$field]) ? $row[$field] : $row['real_'.$field]);
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
		function getDataInput($row,$field,$separator=' ') {
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
			
			$data = '<span id="show">'.implode($separator,$t_value).'</span>';
			$data .= '<span id="edit" style="display:none">'.implode($separator,$t_input).'</span>';
			
			return $data;
		}
		
		// mendapatkan nilai data
		function getDataValue($row,$field) {
			foreach($row as $t_row)
				if($t_row['id'] == $t_field)
					break;
			
			if($t_row['id'] == $t_field)
				return $t_row['value'];
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
		function getDetailTable($row,$kolom,$field,$label,$no=true) {
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
						<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('<?= $field ?>',this)" style="cursor:pointer">
					</td>
				</tr>
				<?	}
					if($i == 0) { ?>
				<tr>
					<td align="center" colspan="<?= $colspan ?>">Data kosong</td>
				</tr>
				<?	} ?>
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
	}
?>