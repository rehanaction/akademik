<div>
    <div id="navigasi" style="margin-left: 10px;" <? if($_GET['page']=='data_pribadi') echo 'class="active"'; ?>  onclick="goView('data_pribadi')">Biodata</div>
    <div id="navigasi" <? if($_GET['page']=='data_ortu') echo 'class="active"'; ?>  onclick="goView('data_ortu')">Data Wali</div>
    <div id="navigasi" <? if($_GET['page']=='data_akademik') echo 'class="active"'; ?>  onclick="goView('data_akademik')">Akademik</div>
    <div id="navigasi" <? if($_GET['page']=='data_final') echo 'class="active"'; ?>  onclick="goView('data_final')">Upload Foto</div>
</div>