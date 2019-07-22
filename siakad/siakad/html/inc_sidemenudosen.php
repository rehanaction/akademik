<style>
	table.sidemenu{
		position:relative;
		right:10px;
		bottom:10px;
		border:1px solid #d1d1d1;
		border-radius:4px;
	}
	
	table.sidemenu th{
		background:#64937F;
		color:#fff;
		padding:10px;
		font-weight:bold;
	}
	
	table.sidemenu tr td{
		padding:8px 12px;
		border-bottom:1px solid #d1d1d1;
	}
	
	table.sidemenu tr:last-child td{
		border-bottom:none;
	}
	
	table.sidemenu tr{
		cursor:pointer;
		
	background: #fbfff4; /* Old browsers */
	/* IE9 SVG, needs conditional override of 'filter' to 'none' */
	background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZiZmZmNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlZmY5ZjEiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
	background: -moz-linear-gradient(top,  #fbfff4 0%, #eff9f1 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fbfff4), color-stop(100%,#eff9f1)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #fbfff4 0%,#eff9f1 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #fbfff4 0%,#eff9f1 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #fbfff4 0%,#eff9f1 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #fbfff4 0%,#eff9f1 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fbfff4', endColorstr='#eff9f1',GradientType=0 ); /* IE6-8 */
	}
	
	table.sidemenu tr:hover{
		background:#D7EDDB;
		text-decoration:underline;
	}
	
</style>
 
<form name="waliform" id="waliform" method="post" style="float:left;">
<table class="sidemenu" cellspacing="0">
	<tr>
		<th>Menu</th>
	</tr>	
<? if (($_REQUEST['key'])=='') {?>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('view_mengajar','') ?>')">Kelas Mengajar</td>
	</tr>
<? }else{ ?>	

	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('set_pesertakelas','') ?>')">Peserta</td>
	</tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('list_jurnal') ?>')">Jurnal (Riwayat)</td>
	</tr> 
	<!--tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('set_absensi') ?>')">Absensi Mhs</td>
	</tr-->
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('set_nilai') ?>')">Isi Nilai</td>
	</tr>  
	<!--tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('view_mengajar') ?>')">Jadwal Mengajar</td>
	</tr>  

	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('view_nilai') ?>')">Pengumuman Nilai</td>
	</tr>  
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		<td onClick="submitPage('npm','<?= Route::navAddress('rep_pftgsuts') ?>')">PF-Tugas-UTS</td>
	</tr-->  
	
<? } ?>	

</table>
<input type="hidden" name="npm" value="<?= $r_key ?>">
</form>
