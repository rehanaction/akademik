<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
	require_once($conf['model_dir'].'m_pendaftar.php');


	//parameter
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
	$tgltes  = CStr::removeSpecial($_REQUEST['tgltes']);
	$seleksi  = CStr::removeSpecial($_REQUEST['tahapujian']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	//model
	$p_model='mPendaftar';
	$p_title='ABSENSI PESERTA TES';
	$p_tbwidth = 800;
	$data=$p_model::getAbsen($conn, $periode,$jalur,$tgltes);
	$p_namafile = 'absensi_'.$periode.'_'.$jalur.'_'.$tgltes;
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Cetak Absesnsi</title>   
                        <link rel="icon" type="image/x-icon" href="image/favicon.png">
                        <link href="style/style.css" rel="stylesheet" type="text/css">
                        
                </head>
                <body style="background:white" onLoad="window.print();">
              
                <center>
						<table>
							<thead>
								<tr>
									<td>DAFTAR HADIR PESERTA UJI TULIS SIPENMARU DIKNAES <BR> POLTEKKES DENPASAR TAHUN AKADEMIK 2014/2015</td>
									
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td>
										ini adalah footer yang akan selalu tampil
									</td>
								</tr>
							</tfoot>
						</table>	
						
				</center>
                </body>
    </html>
