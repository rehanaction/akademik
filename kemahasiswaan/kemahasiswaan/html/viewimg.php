<?php // modal dialog

$key = $_POST['key'];
$img = $_POST['src'];
$type = $_POST['type'];
?>
<div id="overlay">

	<center>
		<table border="0" cellspacing="10" class="nowidth">
			<tr>
				<td class="TDButton" onclick="goDownload()"><img src="images/download.png"> Download</td>
				<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
			</tr>
		</table>
	</center>
	<div style="overflow:scroll; width:100%; height:80%;">

	<div id="wrapper" style="width:650px">
		<div class="SideItem" style="border:none; width:650px; margin:-10px 0 0 -20px">
					<img src="<?=$img?>">
			</div>
		</div>

	</div>
</div>
<div id="fade"></div>

<script>
function goDownload(){
	location.href = "index.php?page=download&type=<?=$type?>&id=<?=$key?>";
}
</script>
