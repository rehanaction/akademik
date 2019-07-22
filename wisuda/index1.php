<?php
  include_once "koneksi.php"; 
  error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));  
  ini_set ('display_errors', 'Off');
?>

<html>
<head>
<title>PESERTA WISUDA</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>
  <form name="form1" method="post" action="index1.php">
  <table align="center" width="918" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="keliling" border="1" font-face="Open Sans">
      
      <tr>
        <td align="right" colspan="13" bgcolor="#D4E6FF"><b>DAFTAR PESERTA WISUDA 2017</b></td>
      </tr>  
      
      <tr>
        <td colspan="13" bgcolor="#D4E6FF">
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="30%" align="right"><b>PROGRAM STUDI :</b>
            </td>
            <td width="78%">
                <select name="cmbKategori">
                    <option value="selected">[Pilih Prodi]</option>
                    
                    <?php 
                      $sql = "SELECT * FROM akademik.ak_prodi
                      ORDER BY nama_program_studi";
                      $qry = pg_query($koneksi, $sql) OR die ("Gagal Query Daftar Prodi");
                      
                      while ($data = pg_fetch_array($qry))
                      {  
                        if ($data[kodeunit]==$_POST['cmbKategori'])
                             {$cek="selected";}
                        else {$cek="";}
                        
                        echo "<option value='$data[kodeunit]' $cek> $data[nama_program_studi]</option>";
                      }
                    ?>
                </select>   
                <input name="TbShow" type="submit" value="Tampilkan">
                                               
            </td>           
          </tr> 
          
                  
          </tr>
          
          </table> 
        </td>
      </tr>
      <!-- <tr bgcolor="#FFFFFF">
        <td colspan="6">&nbsp;</td>
      </tr>-->
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
      <!--<tr>
        <td colspan="6" bgcolor="#CCFF33"><b>DAFTAR MAHASISWA</b></td>
      </tr>-->
      
      <tr bgcolor="#D4E6FF">
        <td width="5" align="right"><b>No</b></td> 
        <td align="center" width="5"><b>NIM</b></td> 
        <td align="center" width="5"><b>STS</b></td> 
        <td width="50" align="center"><strong>NAMA</strong></td>      
        <td width="5" align="center"><strong>BASIS</strong></td> 
        <td align="center" width="10"><b>SKS</b></td> 
        <td width="10" align="center"><strong>IPK</strong></td>   
        
      </tr>
      
      <?php 
        if (!trim($_POST['cmbKategori'])==""){  
        
          $sql = "SELECT a.nim, a.kodeunit, a.sks, a.ipk, b.nama, b.statusmhs, b.sistemkuliah 
                
                FROM akademik.tmp_pw a 
                LEFT JOIN akademik.ms_mahasiswa b
                ON a.nim=b.nim    
                WHERE a.kodeunit='".$_POST['cmbKategori']."'                                                                  
                
                ORDER BY a.nim ASC";
          
        }
        else{
          $sql = "SELECT a.*, b.nama
          FROM akademik.tmp_pw a  
          LEFT JOIN akademik.ms_mahasiswa b
          ON a.nim=b.nim
          ORDER BY a.nim";
        }
        $qry = pg_query($koneksi, $sql) or die ('Gagal Query Data Wisudawan');
        
        $no=0;
        while ($wisudawan = pg_fetch_array($qry)) {
  
              $sql1 = "SELECT * FROM akademik.ak_transkrip WHERE nim='$wisudawan[nim]' ";
              $qry1 = pg_query($koneksi, $sql1) or die ('Gagal Query Data Transkrip');
    
              $skslulus = 0;
              $totalmutu = 0;
    
              while ($data=pg_fetch_array($qry1)) 
                {
                    $skslulus += $data['sks'];
                    $totalmutu += $data['sks']*$data['nangka'];
     
                }    
      
                $ipk= round($totalmutu/$skslulus,2);  
        
                $no++;  
                $nmmhs = STRTOUPPER($wisudawan['nama']);
                $nimhs = SUBSTR($wisudawan['nim'],0,4).'-'.SUBSTR($wisudawan['nim'],4,2).'-'.SUBSTR($wisudawan['nim'],6,5); 
        
                echo "<tr bgcolor=#FFFFFF >       
                    <td align=right> $no. </td>          
                    <td align=center>$nimhs</td>        
                    <td align=center>$wisudawan[statusmhs]</td>
                    <td> $nmmhs </td>           
                    <td align=center>$wisudawan[sistemkuliah]</td>        
                    <td align=center>$skslulus</td>
                    <td align=center> $ipk </td>               
        </tr>";    
         
      }  
      
      
      ?>
  </table>  
  </table>   
 
</body>
</html>
