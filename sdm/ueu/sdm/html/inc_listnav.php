<div style="width:100%;float:left;">
	<center>
		<div style="width:<?= $p_tbwidth ?>px;">
			<div class="extension bottom inright pagination">
				<div class="dataTables_paginate paging_full_numbers" id="DataTables_Table_0_paginate">
					<a class="first paginate_button <?= $r_page == 1 ? 'paginate_button_disabled' : '" onClick="goFirst()' ?>"><<</a>
					<a class="previous paginate_button <?= $r_page == 1 ? 'paginate_button_disabled' : '" onClick="goPrev()' ?>"><</a>
					<a class="next paginate_button <?= $r_page == $p_lastpage ? 'paginate_button_disabled' : '" onClick="goNext()' ?>">></a>
					<a class="last paginate_button <?= $r_page == $p_lastpage ? 'paginate_button_disabled' : '" onClick="goLast()' ?>">>></a>
				</div>
			</div>
		</div>
	</center>
</div>