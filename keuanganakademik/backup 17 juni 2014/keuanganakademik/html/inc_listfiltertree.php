<?php
	if(!empty($a_filtertree)) {
		// fungsi untuk tree
		function showTree($kode,$label,$filter) {
			if(is_array($label)) {
				if($filter[$kode])
					$t_check = ' checked';
				else
					$t_check = '';
?>
		<li id="<?= $kode ?>">
			<? /* <?= $label['label'] ?> */ ?>
			<u class="ULink"><?= $label['label'] ?></u> <?= $t_check ? ' <img src="images/check.png">' : '' ?>
			<ul>
<?php
				foreach($label['data'] as $ckode => $clabel)
					showTree($ckode,$clabel,$filter);
?>
			</ul>
		</li>
	
<?php
			}
			else {
				if($filter[$kode])
					$t_check = ' checked';
				else
					$t_check = '';
?>
		<li id="<?= $kode ?>">
			<? /* <table cellpadding="0" cellspacing="0">
				<tr valign="top">
					<td style="padding-right:5px"><input type="checkbox" id="or"<?= $t_check ?>></td>
					<td><u class="ULink"><?= $t_label ?></u><?= $t_check ? ' <img src="images/check.png">' : '' ?></td>
				</tr>
			</table> */ ?>
			<u class="ULink"><?= $label ?></u> <?= $t_check ? ' <img src="images/check.png">' : '' ?>
		</li>
<?php
			}
		}
		
		function getTreeLabel($ckode,$tree) {
			foreach($tree as $kode => $filter) {
				if(is_array($filter)) {
					if($kode == $ckode)
						return $filter['label'];
					else if(($cek = getTreeLabel($ckode,$filter['data'])) !== false)
						return $cek;
				}
				else if($kode == $ckode)
					return $filter;
			}
			
			// tidak ketemu
			return false;
		}
		// end of function
		
		$a_ctfilter = Page::getFilterTree();
		if(empty($a_ctfilter))
			$a_ctfilter = array();
		
		$a_ktfilter = array();
		foreach($a_ctfilter as $t_filter) {
			list($t_id,$t_kode) = explode(':',$t_filter);
			$a_ktfilter[$t_id][$t_kode] = true;
		}
?>
<div style="border:1px solid #CCC;width:200px;margin-right:15px;float:left">
	<center>
		<header style="width:200px">
			<div class="inner">
				<div class="left title">
					<img id="img_workflow" width="24px" src="images/aktivitas/CARI.png" onerror="loadDefaultActImg(this)"> <h1>Filter</h1>
				</div>
			</div>
		</header>
	</center>
	<div id="div_filtertree" style="height:404px"> 
	<?php
			foreach($a_filtertree as $t_id => $t_tree) {
	?>
		<h3><?= $t_tree['label'] ?></h3>
		<div style="overflow-x:auto">
		<font size="1">
	<?php
				if(!empty($t_tree['data'])) {
	?>
		<ul id="<?= $t_id ?>" class="navigation">
	<?php
					foreach($t_tree['data'] as $t_kode => $t_label)
						showTree($t_kode,$t_label,$a_ktfilter[$t_id]);
	?>
		</ul>
	<?php
				}
	?></font>
		</div>	
	<?		} ?>
	</div>
	<div align="center" style="padding:8px 0px 8px 0px;background:#E0FFF3;border-top:1px solid #58B793;">
	<font size="1">
		<? /* <input type="button" value="Tampilkan" onclick="goFilterCheckTree()"> &nbsp; */ ?>
		<?	if(!empty($a_ctfilter)) { ?>
		<div style="margin:2px 0 8px 0;padding-left:4px;text-align:left">
		<?		foreach($a_ctfilter as $t_filter) {
					list($t_id,$t_kode) = explode(':',$t_filter);
		?>
			<u class="ULink" id="<?= $t_filter ?>"><?= $a_filtertree[$t_id]['label'] ?>: <?= getTreeLabel($t_kode,$a_filtertree[$t_id]['data']) ?></u><br>
		<?		} ?>
		</div>
		<?	} ?>
		<input type="button" value="Reset Filter" onclick="goRemoveFilterTree()">
	</font>
	</div>
</div>
<?php
	}
?>
