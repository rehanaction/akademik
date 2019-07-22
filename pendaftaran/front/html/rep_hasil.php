<!DOCTYPE html>
<?php
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
    class hasil{
        function PageLoad(){
            $r_user=$_SESSION[SITE_ID]['STATUS']['nopendaftaran'];
            $data=mPendaftaran::getData($r_user)->FetchRow ();
            unset($_SESSION[SITE_ID]['STATUS']);
            if($data['lulusujian']!=true && $data['lulusujian']!=false){
                ?>
                
                <div id="DivOk" style="width: 380px; margin-left: 0px; margin-top: 70px;">
		    Berkas Anda sedang di proses<br/> Pengumuman dapat dilihat mulai <br/> <?php echo mPendaftaran::getAnoTime($r_user).$data['lulusujian'];?>
		</div>
                
                <?php
	    }
            else{
                if($data['lulusujian']==false){
                    ?>
                        <div class="DivError" style="width: 380px; margin-left: 0px; margin-top: 170px;">Maaf Anda belum lolos seleksi masuk kali ini.</div>
            	<?php
                }else{
                ?>
                    <div id="DivOk" style="width: 380px; margin-left: 0px; margin-top: 70px;">Selamat Anda lolos SPMB Universitas Esa Ungguk</br> Silakan melakukan registrasi ulang sesuai jadwal yang telah ditetapkan.</div>
                    </br></br>
                    <div style="width: 380px; margin-left: -2px;">
                        <table style="margin-left: 10px; font-family: fantasy; color: #000; width: 380px;" cellspacing=0>
                            <tr style="background: #000; color:#FFF; height: 30px;">
                                <td align="center" colspan=3>Data Pendaftar</td>
                            </tr>
			    <tr>
                                <td align="left">Nama</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo $data['gelardepan']." ".$data['nama']." ".$data['gelarbelakang']; ?></td>
                            </tr>
                            <tr style="background:#d8d8d8;">
                                <td align="left">No.Pendaftaran</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo$r_user; ?></td>
                            </tr>
                            <tr>
                                <td align="left">No.Ujian</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo $data['nomorujian']; ?></td>
                            </tr>
                            <tr style="background:#d8d8d8;">
                                <td align="left">Pilihan I</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo mPendaftaran::getPilihan($data['pilihan1']); ?></td>
                            </tr>
                            <tr>
                                <td align="left">Pilihan II</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo mPendaftaran::getPilihan($data['pilihan1']); ?></td>
                            </tr>
                            <tr style="background:#d8d8d8;">
                                <td align="left">Pilihan III</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo mPendaftaran::getPilihan($data['pilihan2']); ?></td>
                            </tr>
                            <tr>
                                <td align="left">Diterima di</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo mPendaftaran::getPilihan($data['pilihanditerima']); ?></td>
                            </tr>
                            <tr style="background:#d8d8d8;">
                                <td align="left">Nilai</td>
                                <td align="center"> : </td>
                                <td align="left"><?php echo $data['nilaiujian']; ?></td>
                            </tr>
                     </table>
                    </div>
                <?php
                }
            }
        }
    }
?>
