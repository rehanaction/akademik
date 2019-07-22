<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('lokasi'));
        require_once(Route::getModelPath('combo'));
	
	// properti halaman
	$p_title = 'Pembagian Ruangan Pendaftar';
	$p_tbwidth = 500;
	$p_aktivitas = 'UNIT';
	
        $p_model = mLokasi;
        
        $jalur      =array_values(mCombo::jalurpenerimaan($conn));
        
        $filterarray=array();
        $filterarray[] = array('nama'=>'Jalur', 'nilai'=> $jalur);
        
        $fperiode   = "-";
        $fjalur     = "-";
        $fgelombang = "-";
        $opsi       = "";
	
        if(isset($_POST['btnFilter'])){
            $jalurpenerimaan=explode("-",$_POST['Jalur']);
	    $fperiode   =$jalurpenerimaan[2];
            $fjalur     =$jalurpenerimaan[0];
            $fgelombang =$jalurpenerimaan[1];
            $opsi       =$_POST['opsi'];
	    
	    if($p_model::isRand($conn, $fperiode,$fgelombang, $fjalur)){
		$p_msg="Data ini telah diupdate sebelumnya";
	    }else{
		if($opsi=="acak"){
		    $set   = $p_model::setLokasiAcak($conn, $fperiode, $fjalur, $fgelombang, $opsi);
		}elseif($opsi=="urut"){
		    $set   = $p_model::setLokasiUrut($conn, $fperiode, $fjalur, $fgelombang, $opsi);
		}
	    }
	    
        }
	
	$data	= mPendaftar::data($conn, $fperiode, $fjalur, $fgelombang,'');
	$row	= mPendaftar::data($conn, $fperiode, $fjalur, $fgelombang,'')->RecordCount();
	
	if(isset($_POST['save'])){
	    $p=$_POST['periode'];
	    $j=$_POST['jalur'];
	    $g=$_POST['gelombang'];
	    $url="index.php?page=rep_abhp&&p=".$p."&&j=".$j."&&g=".$g;
	    header("Location:".$url);
	}				 
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="scripts/common.js"></script>
</head>
<body>
<div id="main_content">
	<? require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="3" align="center">
							<tr>
								<td valign="top" width="70%">
									<table width="100%" cellspacing="0" cellpadding="4">
                                                                                <?
                                                                                for($i=0; $i<count($filterarray); $i++){
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <? echo $filterarray[$i]['nama']; ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            <select name="<?= $filterarray[$i]['nama'] ?>">
                                                                                            <?
                                                                                            for($j=0;$j<count($filterarray[$i]['nilai']); $j++){
												if(isset($_POST['Jalur'])){
												    $filterset=$_POST['Jalur'];
												}
                                                                                                ?>
                                                                                                <option value="<?= $filterarray[$i]['nilai'][$j] ?>" <? if($filterarray[$i]['nilai'][$j]==$filterset) echo "selected='Selected'"; ?> ><?= $filterarray[$i]['nilai'][$j] ?></option>
                                                                                                <?
                                                                                            }
                                                                                            ?>
                                                                                            </select>
                                                                                        </td>
                                                                                        
                                                                                    </tr>
                                                                                    <?
                                                                                }
                                                                                ?>
                                                                                <tr>
                                                                                    <td>Opsi</td>
                                                                                    <td>
                                                                                        <select name="opsi">
                                                                                            <?
											    if(isset($_POST['opsi'])){
												    $filterset2=$_POST['opsi'];
												}
											    ?>
											    <option value="acak" <? if($filterset2=='acak') echo "selected='selected'"; ?>>Acak</option>
                                                                                            <option value="urut" <? if($filterset2=='acak') echo "selected='selected'"; ?>>Urut</option>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
									</table>
								</td>
                                                                <td valign="top" style="width: 60px;">
									<input name="btnFilter" type="submit" value="Generate Lokasi">
								</td>
								<td align="left" valign="top">
									<input name="btnRefresh" type="submit" value="Refresh">
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?
				if(!empty($p_msg)){    
				?>
				<center>
				    <div class='DivError' style="width: <?=$p_tbwidth?>px"><?=$p_msg?></div>
				</center>
				<?
				}
				    /****************/
                                    /* HEADER TABLE */
                                    /****************/
				?>
				<br>
				<center>
                                    <header style="width:<?= $p_tbwidth ?>px">
                                        <div class="inner">
                                            <div class="left title">
						<table style="width:<?= $p_tbwidth ?>px">
						    <tr>
							<td align="left"><img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><h1>Lokasi Ujian Peserta</h1></td>
							
						    </tr>
						</table>
					    </div>
					</div>
                                    </header>
				</center>
				<?
				    /****************/
                                    /* ITEMS  TABLE */
                                    /****************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="" cellspacing="0" class="GridStyle" align="center">
                                    <tr align="center" style="height: 30px; font-weight: bold; background: #c5c5c5; color: #4a4949;">
                                        <td>No Pendaftar</td>
                                        <td>Nama</td>
                                        <td>Ruang</td>
                                        <!--
					<td>Aksi</td>
					-->
                                    </tr>
                                    <?
				    if($row==0){
				    ?>
					<tr>
					    <td colspan=3 align="center"> Data kosong</td>
					</tr>
				    <?
				    }else{
                                    while($pendaftar = $data->FetchRow()){
                                        ?>
                                        <tr>
                                            <td align="center"><?= $pendaftar['nopendaftar'] ?></td>
                                            <td><?= $pendaftar['nama'] ?></td>
                                            <td align="center"><?= $pendaftar['lokasiujian'] ?></td>
                                            
                                        </tr>
                                        <?
                                    }
				    }
                                    ?>
                                </table>						
				<br>
				<?
                                 //       }
				?>
			</form>
		</div>
	</div>
</div>

</body>
</html>