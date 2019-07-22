<?php
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager_.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>  
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php');  ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($r_page))
						$r_filterstr = Page::getFilterAll();
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
                                <td valign="top" width="50%">
                                    <table width="100%" cellspacing="0" cellpadding="4">
                                        <tr>
                                            <td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
                                            <td style="display:none"><?= uCombo::listColumn($a_kolom) ?></td>
                                            <td width="260"><input name="tfilter" id="tfilter" class="ControlStyle" size="40" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_filterstr ?>"></td>
                                            <td>
                                            	<input type="button" value="Cari" class="ControlStyle" onClick="goFilterAll()">
                                           </td>
                                        </tr>
                                    </table>
                                    </td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	} 		

					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
                            <div class="right">
							<?	
								if($c_insert) { ?>
								<div class="addButton" onClick="goNew()">+</div>
							<?	} ?>
							</div>
							<div class="right">
								<img title="Cetak Laporan" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center" id="datatabletagihan">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th width="5%">Detail</th>
						<?	
								if($c_delete){?>
						<th width="5%">Hapus</th>		
						<?		}
						?>
				

					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) { 
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							$rowc = Page::getColumnRow($a_kolom,$row);

					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<? 	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"'; 
																							

						?>
						<td <?= $t_align ?> ><?= $rowcc; ?></td>

						<?	}  ?>
						<td align="center">	
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onClick="goDetail(this)" style="cursor:pointer">
						</td>
						<?
							if($c_delete) { ?>
						<td align="center">	
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onClick="goDelete(this)" style="cursor:pointer">
						</td>
						<?	} ?>
					</tr>
					<?	} 
						if($i == 0) { 
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<th colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?>
						</div>
						</th>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				</table>
    			<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
	</div>
</div>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
$(document).ready(function() {
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function fillTable(elem,index) {
  // Declare variables 
  
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById(elem.id);
  filter = input.value.toUpperCase();
  table = document.getElementById("datatabletagihan");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[index];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
function goPrint(){
	document.getElementById("act").value = "print";
	goSubmit();
}

</script>
</body>
</html>


