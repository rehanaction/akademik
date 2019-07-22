<div style="width:100%;float:left;">
	<center>
		<div style="width:<?= $p_partnav ? '100%' : $p_tbwidth.'px' ?>">
			<div class="extension bottom inright pagination">
				<div class="dataTables_paginate paging_full_numbers" id="DataTables_Table_0_paginate">
				<? /*
					<a class="first paginate_button <?= $rs->atFirstPage() ? 'paginate_button_disabled' : '" onClick="goFirst()' ?>"><<</a>
					<a class="previous paginate_button <?= $rs->atFirstPage() ? 'paginate_button_disabled' : '" onClick="goPrev()' ?>"><</a>
					<a class="next paginate_button <?= $rs->atLastPage() ? 'paginate_button_disabled' : '" onClick="goNext()' ?>">></a>
					<a class="last paginate_button <?= $rs->atLastPage() ? 'paginate_button_disabled' : '" onClick="goLast()' ?>">>></a>
				*/ ?>
					<a class="first paginate_button <?= $r_page == 1 ? 'paginate_button_disabled' : '" onClick="goFirst()' ?>"><<</a>
					<a class="previous paginate_button <?= $r_page == 1 ? 'paginate_button_disabled' : '" onClick="goPrev()' ?>"><</a>
					<a class="next paginate_button <?= $p_lastpage ? 'paginate_button_disabled' : '" onClick="goNext()' ?>">></a>
					<a class="last paginate_button <?= $p_lastpage ? 'paginate_button_disabled' : '" onClick="goLast()' ?>">>></a>
				</div>
			</div>
			<? if($p_navpage) { ?>
			<div class="PagePaginate">
			<table>
				<tr>
					<?	if($r_page-3 > 1) $i = $r_page-3;
						else $i = 1;
						
						for(;$i<$r_page;$i++) { ?>
					<td onclick="goPage(<?= $i ?>)">
						<span><?= $i ?></span>
					</td>
					<?	} ?>
					<td onclick="goPage(<?= $r_page ?>)">
						<span>Halaman <?= $r_page ?></span>
					</td>
					<?	if($r_page+3 < $r_lastpage) $j = $r_page+3;
						else $j = $r_lastpage;
						
						for($i=$r_page+1;$i<=$j;$i++) { ?>
					<td onclick="goPage(<?= $i ?>)">
						<span><?= $i ?></span>
					</td>
					<?	} ?>
				</tr>
			</table>
			</div>
			<? } ?>
		</div>
	</center>
</div>