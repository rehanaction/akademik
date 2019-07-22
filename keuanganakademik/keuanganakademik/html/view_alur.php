<html>
<head>
	<title>Alur Tagihan</title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div id="div_setting">
					<header style="width:<?= $p_tbwidth-50 ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/SETTING.png" onerror="loadDefaultActImg(this)"> <h1>Alur Tagihan </h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
				<form name="pageformglobal" id="pageformglobal" method="post">
					<!--<div class="box-content" style="width:<?= $p_tbwidth-72 ?>px">
						
					</div>-->
                    <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>1. Calon Pendaftar</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>1.1</strong></td>
                                            <td>Calon Pendaftar membayar formulir di Kasir</td>
                                        </tr>
                                        <tr>
                                        	<td><strong>1.2</strong></td>
                                            <td>Setelah Membayar maka calon pendaftar akan mendapatkan token yang akan di gunakan untuk mendaftar online</td>
                                        </tr>
                                        <tr>
                                        	<td><strong>1.3</strong></td>
                                            <td>Calon Pendaftar melakukan pendaftaran online</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                       <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>2. Pendaftaran</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>2.1</strong></td>
                                            <td>Setelah mendaftar online, maka calon pendaftar akan tercatat sebagai pendaftar</td>
                                        </tr>
                                    	<tr>
                                        	<td width="5%"><strong>2.2</strong></td>
                                            <td>Pendaftar mengikuti proses seleksi </td>
                                        </tr>
                                    	<tr>
                                        	<td width="5%"><strong>2.3</strong></td>
                                            <td>Pendaftar yang lolos akan secara otomatis tergenerate tagihan - tagihan daftar ulang </td>
                                        </tr>
                                    	<tr>
                                        	<td width="5%"><strong>2.4</strong></td>
                                            <td>Pendaftar melakukan pembayaran daftar ulang, dan melakukan daftar ulang untuk tercatat sebagai mahasiswa dan mendapatkan NIM (Nomor Induk Mahasiswa)  </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                      <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>3. Mahasiswa Baru</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>3.1</strong></td>
                                            <td>Mahasiswa melakukan KRS Online / Paket KRS, maka secara otomatis setelah KRS di tutup / di validasi tagihan - tagihan SKS seperti UTS UAS akan tergenerate</td>
                                        </tr>
                                    	
                                    </table>
                                </fieldset>
                            </div>
                           <div style="clear:both"></div>
                           <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>4. Mahasiswa Lama ( Tiap Semester)</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>4.1</strong></td>
                                            <td>Mahasiswa melakukan KRS Online, maka secara otomatis setelah KRS di tutup / di validasi tagihan - tagihan SKS seperti UTS UAS akan tergenerate</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>5. Mahasiswa KKN</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>5.1</strong></td>
                                            <td>Mahasiswa melakukan KRS Online dan mengambil matakuliah KKN, maka secara otomatis setelah KRS di tutup / di validasi tagihan KKN akan di generate</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>6. Mahasiswa Skripsi</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>6.1</strong></td>
                                            <td>Mahasiswa melakukan KRS Online dan mengambil matakuliah Skripsi, maka secara otomatis setelah KRS di tutup / di validasi tagihan Skripsi akan di generate</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend><strong>7. Mahasiswa Wisuda</strong></legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td width="5%"><strong>7.1</strong></td>
                                            <td>Mahasiswa yang terdaftar di dalam daftar Yudisium akan secara otomatis tagihan wisuda di generate</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
					
				</form>
				</div>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
});
function goSetGlobal() {
	document.getElementById("pageformglobal").submit();
}
</script>

</body>
</html>