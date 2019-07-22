<div class="addButton<?= $r_page == 1 ? ' addButton_disabled' : '' ?>" style="float:left"<?= $r_page == 1 ? '' : ' onClick="goFirst()"' ?>><<</div>
<div class="addButton<?= $r_page == 1 ? ' addButton_disabled' : '' ?>" style="float:left"<?= $r_page == 1 ? '' : ' onClick="goPrev()"' ?>><</div>
<div class="addButton<?= $p_lastpage ? ' addButton_disabled' : '' ?>" style="float:left"<?= $p_lastpage ? '' : ' onClick="goNext()"' ?>>></div>
<div class="addButton<?= $p_lastpage ? ' addButton_disabled' : '' ?>" style="float:left"<?= $p_lastpage ? '' : ' onClick="goLast()"' ?>>>></div>