<?php
	// model laporan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLaporan {
		function getDataUnit($conn, $idunit){
		    $sql = "select kodeunit, namaunit,infoleft,inforight from aset.ms_unit where idunit = '$idunit'";
			return $conn->GetRow($sql);
		}
		
		function getDataCOA($conn, $idcoa){
		    $sql = "select idcoa, namacoa from aset.ms_coa where idcoa = '$idcoa'";
			return $conn->GetRow($sql);
		}

		function getDataLokasi($conn, $idlokasi){
		    $sql = "select idlokasi, namalokasi from aset.ms_lokasi where idlokasi = '$idlokasi'";
			return $conn->GetRow($sql);
		}
		
		function getDataSupplier($conn, $idsupplier){
		    $sql = "select idsupplier, namasupplier from aset.ms_supplier where idsupplier = '$idsupplier'";
			return $conn->GetRow($sql);
		}
		
		function getDataStatus($conn, $idstatus){
		    $sql = "select idstatus, status from aset.ms_status where idstatus = '$idstatus'";
			return $conn->GetRow($sql);
		}
		
		function getDataKondisi($conn, $idkondisi){
		    $sql = "select idkondisi, kondisi from aset.ms_kondisi where idkondisi = '$idkondisi'";
			return $conn->GetRow($sql);
		}
		
		function getDataBarang($conn, $idbarang){
		    $sql = "select idbarang, namabarang from aset.ms_barang where idbarang = '$idbarang'";
			return $conn->GetRow($sql);
		}
		
		function getJenisRawat($conn, $idjenisrawat){
		    $sql = "select idjenisrawat, jenisrawat from aset.ms_jenisrawat where idjenisrawat = '$idjenisrawat'";
			return $conn->GetRow($sql);
		}
		
		function getLokasi($conn, $idlokasi){
		    $sql = "select idlokasi, namalokasi from aset.ms_lokasi where idlokasi = '$idlokasi'";
			return $conn->GetRow($sql);
		}
		
		function getCabang($conn, $idcabang){
		    $sql = "select idcabang, namacabang from aset.ms_cabang where idcabang = '$idcabang'";
			return $conn->GetRow($sql);
		}
		
		function getPegawai($conn, $idpegawai){
		    $sql = "select nip, namalengkap as pegawai from sdm.v_biodatapegawai where idpegawai = '$idpegawai'";
			return $conn->GetRow($sql);
		}        
        
        function getPetugasRuang($conn, $idlokasi){
		    $sql = "select nip, namalengkap as pegawai 
		            from aset.ms_lokasi l
		            join sdm.v_biodatapegawai p on p.idpegawai = l.idpetugas where idlokasi = '$idlokasi'";
			return $conn->GetRow($sql);
		}
        		
		function getGedung($conn, $idgedung){
		    $sql = "select idgedung, namagedung from aset.ms_gedung where idgedung = '$idgedung'";
			return $conn->GetRow($sql);
		}
		
		//untuk graph
		function getJmlAsetPeriode($conn){
		    $a_data = array();
		    $a_periode = Aset::getPrevPeriode();
            $r_fperiode = $a_periode[0];
            
            $a_unit = Modul::getLeftRight();

			$sql = "select sum(p.qty) as total 
					from aset.as_perolehan p
					where p.isverify = 1 and substring(convert(varchar, p.tglperolehan, 112),1,6) < '$r_fperiode'";
            $totawal += (int)$conn->GetOne($sql);
                        
			$sql = "select count(*) as total 
                from aset.as_penghapusan p join aset.as_penghapusandetail d on d.idpenghapusan = p.idpenghapusan 
                where p.isverify = 1 and substring(convert(varchar, p.tglpenghapusan, 112),1,6) < '$r_fperiode'";
            $totawal -= (int)$conn->GetOne($sql);
			
			$sql = "select substring(convert(varchar,p.tglperolehan,112),1,6) as periode, sum(pd.qty) as total 
                from aset.as_perolehanheader p 
				join aset.as_perolehan pd on pd.idperolehanheader = p.idperolehanheader
                where pd.isverify = 1 and substring(convert(varchar, p.tglperolehan, 112),1,6) >= '$r_fperiode'  
                group by substring(convert(varchar, p.tglperolehan, 112),1,6)";
            $rs = $conn->Execute($sql);
            while($row = $rs->FetchRow()){
                $a_tmpt[$row['periode']] = $row['total'];
            }
			
			//penghapusan
			$sql = "select substring(convert(varchar,p.tglpenghapusan,112),1,6) as periode, count(*) as total 
                from aset.as_penghapusan p join aset.as_penghapusandetail d on d.idpenghapusan = p.idpenghapusan 
                where p.isverify = 1 and substring(convert(varchar, p.tglpenghapusan, 112),1,6) >= '$r_fperiode' 
                group by substring(convert(varchar, p.tglpenghapusan, 112),1,6)";
            $rs = $conn->Execute($sql);
            while($row = $rs->FetchRow()){
                $a_tmpk[$row['periode']] = $row['total'];
            }

            foreach($a_periode as $key => $val){
                $total += (int)$a_tmpt[$val]-(int)$a_tmpk[$val];
                $a_data[$val] = $total + (int)$totawal ;
            }
            
            //$a_data[$r_fperiode] += (int)$totawal;
            
		    return array(Aset::getPrevPeriodeName(), $a_data);
		}

		function getJmlAsetKelompok($conn){
		    $a_data = array();
		    $a_kelompok = array();
		    $a_unit = Modul::getLeftRight();
		    
		    $sql = "select substring(idbarang,1,1) as brg, namabarang 
		        from aset.ms_barang 
		        where level = 1 and substring(idbarang,1,1) != '1' order by idbarang";
		    $rs = $conn->Execute($sql);
		    while($row = $rs->FetchRow())
		        $a_kelompok[$row['brg']] = $row['namabarang'];
		    
		    $sql = "select substring(idbarang,1,1) as brg, count(*) as jml 
		        from aset.as_seri s join aset.ms_unit u on u.idunit = s.idunit 
		        where substring(idbarang,1,1) != '1' and idstatus in ('A','P','R') 
		        and u.infoleft >= ".(int)$a_unit['LEFT']." and inforight <= ".(int)$a_unit['RIGHT']." 
		        group by substring(idbarang,1,1)";
		    $rs = $conn->Execute($sql);
		    while($row = $rs->FetchRow())
		        $a_data[$row['brg']] = $row['jml'];
		    
		    return array($a_kelompok, $a_data);
		}
		
		function getJmlJenisPerolehan($conn){
		    $a_data = array();
		    $a_jenis = array();
		    $a_unit = Modul::getLeftRight();
		    
		    $sql = "select jenisperolehan,sum(qty) as total 
		        from aset.as_perolehan p join aset.ms_jenisperolehan j on j.idjenisperolehan = p.idjenisperolehan 
		        join aset.ms_unit u on u.idunit = p.idunit 
		        where isverify = 1 and u.infoleft >= ".(int)$a_unit['LEFT']." and inforight <= ".(int)$a_unit['RIGHT']." 
		        group by jenisperolehan order by jenisperolehan";
	        $rs = $conn->Execute($sql);
	        while($row = $rs->FetchRow()){
	            $a_jenis[] = $row['jenisperolehan'];
	            $a_data[] = $row['total'];
	        }

		    return array($a_jenis, $a_data);
		}

		function getJmlPerolehan($conn){
			$sql = "select count(idperolehan) as total
					from aset.as_perolehan
					where isverify != 1 or isverify is null ";
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getJmlPermintaanHP($conn){
			$sql = "select count(idtranshp) as total
					from aset.as_transhp
					where status = 'A'
					and tok = 'K' ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnverifiedRawat($conn){
			$sql = "select count(idrawat) as total
					from aset.as_rawat
					where status = 'A' ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnverifiedPinjam($conn){
			$sql = "select count(idpinjam) as total
					from aset.as_pinjam
					where status = 'A' ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnverifiedMutasi($conn){
			$sql = "select count(idmutasi) as total
					from aset.as_mutasi
					where status = 'A' ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnverifiedHapus($conn){
			$sql = "select count(idpenghapusan) as total
					from aset.as_penghapusan
					where status = 'A' ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnprocessedRawat($conn){
			$sql = "select count(idrawat) as total
					from aset.as_rawat
					where isverify = 1 and isok1 is null ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnprocessedPinjam($conn){
			$sql = "select count(idpinjam) as total
					from aset.as_pinjam
					where isverify = 1 and isok1 is null ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnprocessedMutasi($conn){
			$sql = "select count(idmutasi) as total
					from aset.as_mutasi
					where isverify = 1 and isok1 is null ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}

		function getUnprocessedHapus($conn){
			$sql = "select count(idpenghapusan) as total
					from aset.as_penghapusan
					where isverify = 1 and isok1 is null ";
			
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data = $row['total'];
			}

			return $a_data;
		}
		
		/*function getPerolehanBarang($conn, $p=''){
			$sql ="select p.idbarang, b.namabarang, p.merk, p.spesifikasi, pd.qty, p.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_satuan s on s.idsatuan=p.idsatuan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getRekapBarang($conn,$unit,$kondisi) {
			$sql ="select s.idbarang,b.namabarang,count(s.idseri) as total 
			   from aset.as_seri s join aset.ms_barang b on b.idbarang = s.idbarang 
			   where s.idunit = '$unit' and s.idkondisi = '$kondisi' 
			   group by s.idbarang,b.namabarang 
			   order by s.idbarang";
		   
            $sql = "select top 10 idbarang,namabarang from aset.ms_barang order by idbarang";

			return $conn->Execute($sql);
		}*/
		
		/*function getSeriBarang($conn, $p=''){
			$sql ="select right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idbarang, b.namabarang, l.namalokasi, u.namaunit
							from aset.as_seri s
							left join aset.ms_barang b on b.idbarang=s.idbarang
							left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
							left join aset.ms_unit u on u.idunit=s.idunit
							where s.idlokasi = '{$p['idlokasi']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.noseri";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPertumbuhanBarang($conn){
			$sql ="select p.idbarang, b.namabarang, p.idsatuan, sum(p.qty) as jumlah
						from aset.ms_barang b
						join aset.as_perolehan p on p.idbarang=b.idbarang
						group by p.idbarang, b.namabarang, p.idsatuan";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getRekapStatusBarang($conn, $p=''){
			$sql ="select s.idbarang, b.namabarang
							from aset.as_seri s 
							left join aset.ms_barang b on b.idbarang=s.idbarang 
							left join aset.ms_status st on st.idstatus=s.idstatus
							left join aset.ms_unit u on u.idunit=s.idunit
							where st.idstatus = '{$p['idstatus']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getRekapKondisiBarang($conn, $p=''){
			$sql ="select s.idbarang, b.namabarang
							from aset.as_seri s 
							left join aset.ms_barang b on b.idbarang=s.idbarang 
							left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
							left join aset.ms_unit u on u.idunit=s.idunit
							where k.idkondisi = '{$p['idkondisi']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getMutasiBarang($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, 
							right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, u1.namaunit as unitasal, u2.namaunit as unittujuan, 
							m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang,0,1) != '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getMaintenance($conn, $p=''){
			$sql ="select s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), r.tglrawat, 105) as tglrawat, 
							convert(varchar(10), r.tglpembukuan, 105) as tglpembukuan
							from aset.as_rawat r
							join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
							left join aset.ms_jenisrawat jr on jr.idjenisrawat=r.idjenisrawat
							left join aset.as_seri s on s.idseri=rd.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u on u.idunit=r.idunit
							where jr.idjenisrawat = '{$p['idjenisrawat']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and 
							substring(s.idbarang,0,1) != '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getMaintenanceJatuhTempo($conn, $p=''){
			$sql ="select s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), r.tglrawat, 105) as tglrawat, 
							convert(varchar(10), r.tglpembukuan, 105) as tglpembukuan
							from aset.as_rawat r
							join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
							left join aset.ms_jenisrawat jr on jr.idjenisrawat=r.idjenisrawat
							left join aset.as_seri s on s.idseri=rd.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u on u.idunit=r.idunit
							where jr.idjenisrawat = '{$p['idjenisrawat']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and 
							substring(s.idbarang,0,1) != '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getBukuInventaris($conn, $p=''){
			$sql ="select p.nobukti, convert(varchar(12), p.tglpembukuan, 106) as tglpembukuan, b.namabarang, p.idbarang, p.merk, 
							p.idsumberdana, pd.qty, p.harga, isnull((pd.qty*p.harga),0) as jumlah, p.catatan, u.namaunit, pd.idlokasi
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.nobukti";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getBukuInduk($conn, $p=''){
			$sql ="select p.idperolehan, p.idjenisperolehan, p.idunit, u.namaunit, pd.idlokasi, l.namalokasi, p.idbarang, b.namabarang, 
							p.idsatuan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, 
							p.nobukti, p.idsumberdana, pd.qty, p.harga, pg.namadepan+' '+pg.gelarbelakang as pegawai, p.merk, p.spesifikasi
							from aset.as_perolehan p 
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_lokasi l on l.idlokasi=pd.idlokasi
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_jenisperolehan jp on jp.idjenisperolehan=p.idjenisperolehan
							left join sdm.ms_pegawai pg on pg.idpegawai=pd.idpegawai
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.idperolehan";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getMutasiPeriode($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, 
							right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, u1.namaunit as unitasal, u2.namaunit as unittujuan, 
							m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang,0,1) != '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getOpname($conn, $p=''){
			$sql ="select s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), o.tglopname, 105) as tglopname, 
							convert(varchar(10), o.tglpembukuan, 105) as tglpembukuan
							from aset.as_opname o
							join aset.as_opnamedetail od on od.idopname=o.idopname
							left join aset.as_seri s on s.idseri=od.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u on u.idunit=o.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang,0,1) != '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getOpnameHP($conn, $p=''){
			$sql ="select od.idbarang, b.namabarang, convert(varchar(10), o.tglopname, 105) as tglopname, 
							convert(varchar(10), o.tglpembukuan, 105) as tglpembukuan, od.qtyawal, od.qtyakhir, od.idsatuan
							from aset.as_opnamehp o
							join aset.as_opnamehpdetail od on od.idopnamehp=o.idopnamehp
							left join aset.ms_barang b on od.idbarang=b.idbarang
							left join aset.ms_satuan s on s.idsatuan=od.idsatuan
							left join aset.ms_unit u on u.idunit=o.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(od.idbarang,0,1)= '1'
							order by od.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getAsetAkuntansi($conn, $p=''){
			$sql ="select p.idbarang, b.namabarang, p.idsatuan, pd.qty, p.harga, u.namaunit, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, p.catatan,
							substring(convert(varchar(10), p.tglpembukuan, 101),4,2)+'/'+substring(convert(varchar(10), p.tglpembukuan, 101),9,2) as bln
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPerolehanAset($conn, $p=''){
			$sql ="select p.idbarang, b.namabarang, p.merk, p.spesifikasi, pd.qty, p.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(p.idbarang,0,1) != '1' and p.nobukti is not null
							order by p.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getStockHabisPakai($conn, $p=''){
			$sql ="select s.idbarang, b.namabarang, s.jmlstock, st.satuan
							from aset.as_stockhp s 
							left join aset.ms_barang b on b.idbarang=s.idbarang 
							left join aset.ms_satuan st on st.idsatuan=s.idsatuan
							left join aset.ms_unit u on u.idunit=s.idunit
							where s.idbarang = '{$p['idbarang']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang,0,1)= '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getMutasiHabisPakai($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, 
							convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, 
							b.namabarang, u1.namaunit as unitasal,
							u2.namaunit as unittujuan, m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang,0,1)= '1'
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPenghapusanAset($conn, $p=''){
			$sql ="select p.idpenghapusan, jp.jenispenghapusan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, pd.idseri, 
							b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi
							from aset.as_penghapusan p 
							left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
							left join aset.as_seri s on s.idseri=pd.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_jenispenghapusan jp on jp.idjenispenghapusan=p.idjenispenghapusan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by p.idpenghapusan";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getNilaiAset($conn, $p=''){
			$sql ="select s.idbarang, b.namabarang, isnull(s.nilaiaset,0) nilaiaset, k.kondisi, l.namalokasi
							from aset.as_seri s
							left join aset.ms_barang b on b.idbarang=s.idbarang
							left join aset.ms_unit u on u.idunit=s.idunit
							left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
							left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPenyusutanAset($conn, $p=''){
			$sql ="select d.periode, s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, s.idunit, 
							jp.jenispenyusutan, d.nilaisusut, d.nilaiaset, u.kodeunit 
							from aset.as_histdepresiasi d 
							left join aset.as_seri s on s.idseri=d.idseri 
							left join aset.ms_barang b on b.idbarang=s.idbarang 
							left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan=d.idjenispenyusutan 
							left join aset.ms_unit u on u.idunit=s.idunit 
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getRekapPenyusutan($conn, $p=''){
			$sql ="select d.periode, s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, s.idunit, 
							jp.jenispenyusutan, d.nilaisusut, d.nilaiaset, u.kodeunit 
							from aset.as_histdepresiasi d 
							left join aset.as_seri s on s.idseri=d.idseri 
							left join aset.ms_barang b on b.idbarang=s.idbarang 
							left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan=d.idjenispenyusutan 
							left join aset.ms_unit u on u.idunit=s.idunit 
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPerolehanHP($conn, $p=''){
			$sql ="select p.idbarang, b.namabarang, p.merk, p.spesifikasi, pd.qty, p.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang b on b.idbarang=p.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(p.idbarang,0,1) = '1'
							order by p.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPermintaanHP($conn, $p=''){
			$sql ="select td.idbarang, b.namabarang, td.idsatuan, td.qty, td.harga, t.idsumberdana, s.namasupplier, 
							convert(varchar(10), t.tglpembukuan, 105) as tglpembukuan
							from aset.as_transhp t
							join aset.as_transhpdetail td on td.idtranshp=t.idtranshp
							left join aset.ms_barang b on b.idbarang=td.idbarang
							left join aset.ms_unit u on u.idunit=t.idunit
							left join aset.ms_supplier s on s.idsupplier=t.idsupplier
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							t.idjenistranshp != 306
							order by td.idbarang";
			
			return $conn->Execute($sql);
		}*/
		
		/*function getPenghapusan($conn, $key){
			$sql ="select p.idpenghapusan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, pd.idseri, 
							b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi,
							b.idbarang, u.namaunit, convert(varchar(10), p.tglpenghapusan, 105) as tglpenghapusan, u.kodeunit,
							count(case when s.idkondisi = 'B' then 1 else 0 end) as b,
							count(case when s.idkondisi = 'RB' then 1 else 0 end) as rb,
							count(case when s.idkondisi = 'RR' then 1 else 0 end) as rr,
							count(case when s.idkondisi = 'TB' then 1 else 0 end) as tb
							from aset.as_penghapusan p 
							left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
							left join aset.as_seri s on s.idseri=pd.idseri
							left join aset.ms_barang b on s.idbarang=b.idbarang
							left join aset.ms_unit u on u.idunit=p.idunit
							where p.idpenghapusan = '$key'
							group by p.idpenghapusan, p.tglpembukuan, pd.idseri, b.namabarang, pd.nilaipenghapusan, 
							s.noseri, s.merk, s.spesifikasi, b.idbarang, p.tglpenghapusan, u.namaunit, u.kodeunit";
			
			return $conn->GetRow($sql);
		}*/
		
		function getPerolehan($conn, $key, $iddetail){
		    $sql = "select u.kodeunit, u.namaunit, p.tglperolehan, pg.nip, pg.namalengkap
		              from aset.as_perolehan p 
					  join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
		              left join aset.ms_unit u on u.idunit = p.idunit 
					  left join sdm.v_pegawai pg on pg.idpegawai=pd.idpegawai
		              where p.idperolehan = '$key' and pd.iddetperolehan = '$iddetail' ";

			return $conn->GetRow($sql);
		}

		/*function getBASTBNew($conn, $key, $iddetail){
			$sql = "select p.idbarang, b.namabarang, p.merk, p.spesifikasi, k.kondisi,
			               pd.idlokasi, l.namalokasi, pd.idlokasi+' - '+l.namalokasi as lokasi, pd.idpegawai, pg.namalengkap, 
						   right('000000' + cast(s.noseri as varchar(6)), 6) noseri
					  from aset.as_perolehan p
					  join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					  left join aset.as_seri s on s.iddetperolehan=pd.iddetperolehan
					  left join aset.ms_barang b on b.idbarang=p.idbarang
					  left join aset.ms_lokasi l on l.idlokasi=pd.idlokasi
					  left join aset.ms_kondisi k on k.idkondisi=p.idkondisi
					  left join sdm.v_pegawai pg on pg.idpegawai=pd.idpegawai
					 where p.idperolehan = '$key' and pd.iddetperolehan = '$iddetail'
					 order by pd.iddetperolehan ";

			return $conn->Execute($sql);
		}*/
		
		/*function getBASTBHapus($conn, $key){
		    $sql = "select right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idbarang, b.namabarang, s.merk, s.spesifikasi, p.nilaipenghapusan
				from aset.as_penghapusandetail p
				left join aset.as_seri s on s.idseri=p.idseri
				left join aset.ms_barang b on b.idbarang=s.idbarang
		        where p.idpenghapusan = '$key' 
		        order by p.iddetpenghapusan";

			return $conn->Execute($sql);
		}*/
		
		/*function getBASTBMutasi($conn, $key){
		    $sql = "select s.idbarang,b.namabarang,s.merk,s.spesifikasi, right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        m.idunittujuan, m.idlokasitujuan, m.idpegawaitujuan, p.namalengkap as namapegawai, convert(varchar(10), s.tglperolehan, 105) as tglperolehan
		        from aset.as_mutasi m
		        join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
		        left join aset.as_seri s on s.idseri = md.idseri 
		        left join aset.ms_barang b on b.idbarang = s.idbarang 
		        left join sdm.v_biodatapegawai p on p.idpegawai = m.idpegawaitujuan
		        where m.idmutasi = '$key' 
		        order by md.iddetmutasi";

			return $conn->Execute($sql);
		}*/
		
		/*function getHeaderRawatUnit($conn, $key){
			$sql = " select u.kodeunit, u.namaunit, r.idlokasi, l.namalokasi, p.namalengkap as pemakai
         			   from aset.as_rawat r
					   join aset.as_rawatdetail rd on rd.idrawat = r.idrawat
					   left join aset.ms_unit u on u.idunit = r.idunit 
					   left join aset.ms_lokasi l on l.idlokasi = r.idlokasi
   					   left join aset.as_seri s on s.idseri = rd.idseri
					   left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai
					  where r.idrawat = '$key' ";

			return $conn->GetRow($sql);
		}

		function getBASTBRawat($conn, $key){
		    $sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri, 
					s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.idbarang+' - '+b.namabarang as barang, p.namalengkap as pegawai,
					s.tglperolehan, s.tglgaransihabis 
					from aset.as_rawatdetail d 
					left join aset.as_seri s on s.idseri = d.idseri 
					left join aset.ms_barang b on b.idbarang = s.idbarang 
					left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
					where d.idrawat = '$key' ";

			return $conn->Execute($sql);
		}*/

		/*function getHeaderRawatSupplier($conn, $key){
			$sql = " select r.idsupplier, s.namasupplier, s.alamat, s.namacp, s.kota, s.notlp, s.nohp
         			   from aset.as_rawat r
					   left join aset.ms_supplier s on s.idsupplier = r.idsupplier 
					  where r.idrawat = '$key' ";

			return $conn->GetRow($sql);
		}*/
		
		/*function getBASTBRawatSupplier($conn, $key){
		    $sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri, 
					s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.idbarang+' - '+b.namabarang as barang,
					r.tglrawat, r.tglkembali, r.biaya
					from aset.as_rawatdetail d 
					join aset.as_rawat r on r.idrawat = d.idrawat
					left join aset.as_seri s on s.idseri = d.idseri 
					left join aset.ms_barang b on b.idbarang = s.idbarang 
					left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
					where d.idrawat = '$key' ";

			return $conn->Execute($sql);
		}*/
		
		/*function getBASTBPinjam($conn, $key){
		    $sql = "select s.idbarang,b.namabarang,s.merk,s.spesifikasi, p.catatan,
		        convert(varchar(10), p.tglpinjam, 105) as tglpinjam, convert(varchar(10), p.tglkembali, 105) as tglkembali, 
		        convert(varchar(10), s.tglperolehan, 105) as tglperolehan
		        from aset.as_pinjam p
		        join aset.as_pinjamdetail pd on pd.idpinjam=p.idpinjam
		        left join aset.as_seri s on s.idseri = pd.idseri 
		        left join aset.ms_barang b on b.idbarang = s.idbarang 
		        where p.idpinjam = '$key' 
		        order by pd.iddetpinjam";

			return $conn->Execute($sql);
		}*/
		
		/*function getKIR($conn,$p=''){
			$sql="select s.idseri, s.idbarang,b.namabarang as namabarang, l.namalokasi as namalokasi, u.namaunit as namaunit, 
					right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idkondisi, s.idstatus, s.merk, 
					s.spesifikasi, convert(varchar(10), s.tglperolehan, 105) as tglperolehan, s.idlokasi, s.idunit, p.namalengkap, k.kondisi, st.status
					from aset.as_seri s
					left join aset.ms_barang b on b.idbarang=s.idbarang
					left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
					left join aset.ms_unit u on u.idunit=s.idunit
					left join sdm.v_pegawai p on l.idpetugas=p.idpegawai
					left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
					left join aset.ms_status st on st.idstatus=s.idstatus
					where s.idlokasi = '{$p['idlokasi']}' and
					u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} ";
			
			return $conn->Execute($sql);
		}*/
		
	}
?>
