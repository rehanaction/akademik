<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
        require_once($conf['model_dir'].'m_pendaftar.php');
        require_once($conf['model_dir'].'m_lokasi.php');
        require_once($conf['model_dir'].'m_gelombang.php');
        require_once($conf['model_dir'].'m_materi.php');
        
        //parameter
        $periode    = CStr::removeSpecial($_REQUEST['periode']);
        $jalur      = CStr::removeSpecial($_REQUEST['jalur']);
        $gelombang  = CStr::removeSpecial($_REQUEST['gelombang']);
		$ruang  = CStr::removeSpecial($_REQUEST['ruang']);
       $r_format = CStr::removeSpecial($_REQUEST['format']);
        //model
        $p_model='mPendaftar';
        $p_model2='mLokasi';
        $p_tbwidth = 900;
        $p_namafile = 'kartu_'.$periode.'_'.$jalur.'_'.$gelombang;
	Page::setHeaderFormat($r_format,$p_namafile);

?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Cetak Kartu</title>   
                        <link rel="icon" type="image/x-icon" href="image/favicon.png">
                        <link href="style/style.css" rel="stylesheet" type="text/css">
                        
                </head>
                <body style="background:white" onLoad="window.print();">
                        
                        <?
                                $kapasitas  = $p_model2::getKapasitasRuang($conn, $ruang);
                                $kapasitas  = $kapasitas['kapasitaslokasi'];
                                
                                /**********/
				/* HEADER */
				/**********/
			?>
                        <center>
                        <div style="border: solid; border-width: thin; border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
                        <center>
                                <table width="<?=$p_tbwidth-130 ?>px">
				    <tr>
						<td>
							    <img src="images/logo.jpg" />
						</td>
						<td>
							    <p id="header" style="font-size: medium">
									<strong>
									A L B U M &nbsp  B U K T I &nbsp H  A D I R &nbsp  P E S E R T A  &nbsp (A B H P ) <br>
									S E L E K S I &nbsp  P E N E R I M A A N &nbsp  M A H A S I S W A  &nbsp B A R U  <br>
									UNIVERSITAS &nbsp  ESA &nbsp  UNGGUL &nbsp P E R I O D E <?= $periode ?><br>
									<?= $jalur ?> - <?= $gelombang ?>
									</strong>
							    </p>
						</td>
				    </tr>
				</table>
				
                        </center>
                        <br>
                        <div align="right" style="margin-right: 30px;">
                                <table width="300" style="border: solid; border-width: thin; border-color: #000;">
                                        <tr >
                                                <td style="width:100px;" align="center">
                                                        <span id="header" style="font-size: xx-large;"><?= $ruang ?></span>                                                      
                                                </td>
                                                <td align="center">
                                                        <span id="header"><?= $periode ?>-<?= $gelombang ?>-1-<?= $ruang ?></span><br>
                                                        <strong>s/d</strong><br>
                                                        <span id="header"><?= $periode ?>-<?= $gelombang ?>-<?= $kapasitas ?>-<?= $ruang ?></span>
                                                </td>
                                        </tr>
                                </table>
                        </div>
                        <br>
                        <?
                                 /**********/
				/* HEADER */
				/**********/
                        ?>
                        <center>
                                <table class="GridStyle" width="<?= $p_tbwidth-50 ?>" border=1 cellspacing=0 >
                                        <?
                                        $data=$p_model::getDataLokasi($conn, $ruang);
                                        $no=0;
                                        while($pendaftar = $data->FetchRow()){
                                                $no++;
                                        ?>
                                        <tr>
                                                <td style="width: 30px;" align="center"><?=$no ?></td>
                                                <td style="width: 120px;" align="center"><img height=120 width=90 src="uploads/fotocamaba/<?= $periode ?>-<?= $jalur ?>-<?= $gelombang ?>/<?= $pendaftar['nopendaftar'] ?>.jpg"></td>
                                                <td>
                                                        <table width=90% align="center">
                                                                <tr>
                                                                        <td> Nama :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><? echo  $pendaftar['gelardepan'].$pendaftar['nama'].$pendaftar['gelarbelakang'] ?> </td>
                                                                </tr>
                                                                <tr>
                                                                        <td> No.Pendaftar :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><?= $pendaftar['nopendaftar'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                        <td> Alamat :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><p><?= $pendaftar['alamat'] ?></p></td>
                                                                </tr>
                                                        </table>
                                                </td>
                                                <td>
                                                        <table>
                                                        <?
                                                        $materi = mMateri::getMateri($conn);
                                                        $nMateri=0;
                                                        while ($materis = $materi->FetchRow()){
                                                        $nMateri++;
                                                        ?>
                                                                <tr style="height: 20px;">
                                                                        <td><?= $nMateri ?></td>
                                                                        <td><?= $materis['namamateri'] ?> </td>
                                                                        <td><? if($nMateri%2==1) echo '(_________)'; ?></td>
                                                                        <td><? if($nMateri%2==0) echo '(_________)'; ?></td>
                                                                </tr>
                                                        <?
                                                        }
                                                        ?>
                                                        </table>
                                                </td>
                                                <td>
                                                        <table>
                                                                <?
                                                                for($index=0;$index<$nMateri; $index++){
                                                                ?>
                                                                <tr style="height: 20px;">
                                                                        <td><?= $index+1 ?></td>
                                                                        <td>___________________</td>
                                                                </tr>
                                                                <?
                                                                }
                                                                ?>
                                                        </table>
                                                </td>
                                        </tr>
                                        <?
                                        }
                                        while($no<$kapasitas){
                                                $no++;
                                        ?>
                                        <tr>
                                                <td style="width: 30px;" align="center"><?=$no ?></td>
                                                <td style="width: 120px;" align="center"><img height=120 width=90 src="" title="Foto 3x4"></td>
                                                <td>
                                                        <table width=90% align="center">
                                                                <tr>
                                                                        <td> Nama :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td>_________________________</td>
                                                                </tr>
                                                                <tr>
                                                                        <td> No.Pendaftar :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td>_________________________</td>
                                                                </tr>
                                                                <tr>
                                                                        <td> Alamat :</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><p>_________________________<br><br>_________________________</p></td>
                                                                </tr>
                                                        </table>
                                                </td>
                                                <td>
                                                        <table>
                                                        <?
                                                        $materi = mMateri::getMateri($conn);
                                                        $nMateri=0;
                                                        while ($materis = $materi->FetchRow()){
                                                        $nMateri++;
                                                        ?>
                                                                <tr style="height: 20px;">
                                                                        <td><?= $nMateri ?></td>
                                                                        <td><?= $materis['namamateri'] ?> </td>
                                                                        <td><? if($nMateri%2==1) echo '(_________)'; ?></td>
                                                                        <td><? if($nMateri%2==0) echo '(_________)'; ?></td>
                                                                </tr>
                                                        <?
                                                        }
                                                        ?>
                                                        </table>
                                                </td>
                                                <td>
                                                        <table>
                                                                <?
                                                                for($index=0;$index<$nMateri; $index++){
                                                                ?>
                                                                <tr style="height: 20px;">
                                                                        <td><?= $index+1 ?></td>
                                                                        <td>___________________</td>
                                                                </tr>
                                                                <?
                                                                }
                                                                ?>
                                                        </table>
                                                </td>
                                        </tr>
                                        <?
                                        }
                                        ?>
                                </table>
                                <br>
                        </center>
                        </div>
                        </center>
                        
                        <?        
                       // }
                        ?>
                </body>
    </html>
