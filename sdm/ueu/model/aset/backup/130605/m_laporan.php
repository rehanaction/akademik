<?php
	// model laporan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLaporan {
		function getDataUnit($conn, $idunit){
		    $sql = "select kodeunit, namaunit,infoleft,inforight from aset.ms_unit where idunit = '$idunit'";
			return $conn->GetRow($sql);
		}
		
		function getDataLokasi($conn, $idlokasi){
		    $sql = "select idlokasi, namalokasi from aset.ms_lokasi where idlokasi = '$idlokasi'";
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
		
		function getDataBarang($conn, $idbarang1){
		    $sql = "select idbarang1, namabarang from aset.ms_barang1 where idbarang1 = '$idbarang1'";
			return $conn->GetRow($sql);
		}
		
		function getJenisRawat($conn, $idjenisrawat){
		    $sql = "select idjenisrawat, jenisrawat from aset.ms_jenisrawat where idjenisrawat = '$idjenisrawat'";
			return $conn->GetRow($sql);
		}
		
		function getPerolehanBarang($conn, $p=''){
			$sql ="select pd.idbarang1, b.namabarang, pd.merk, pd.spesifikasi, pd.qty, b.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_satuan s on s.idsatuan=pd.idsatuan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by pd.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getRekapBarang($conn,$unit,$kondisi) {
			$sql ="select s.idbarang1,b.namabarang,count(s.idseri) as total 
			   from aset.as_seri s join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
			   where s.idunit = '$unit' and s.idkondisi = '$kondisi' 
			   group by s.idbarang1,b.namabarang 
			   order by s.idbarang1";
		   
            $sql = "select top 10 idbarang1,namabarang from aset.ms_barang1 order by idbarang1";

			return $conn->Execute($sql);
		}
		
		function getSeriBarang($conn, $p=''){
			$sql ="select right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idbarang1, b.namabarang, l.namalokasi, u.namaunit
							from aset.as_seri s
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1
							left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
							left join aset.ms_unit u on u.idunit=s.idunit
							where s.idlokasi = '{$p['idlokasi']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.noseri";
			
			return $conn->Execute($sql);
		}
		
		function getPertumbuhanBarang($conn){
			$sql ="select pd.idbarang1, b.namabarang, b.idsatuan, sum(pd.qty) as jumlah
						from aset.ms_barang1 b
						join aset.as_perolehandetail pd on pd.idbarang1=b.idbarang1
						group by pd.idbarang1, b.namabarang, b.idsatuan";
			
			return $conn->Execute($sql);
		}
		
		function getRekapStatusBarang($conn, $p=''){
			$sql ="select s.idbarang1, b.namabarang
							from aset.as_seri s 
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
							left join aset.ms_status st on st.idstatus=s.idstatus
							left join aset.ms_unit u on u.idunit=s.idunit
							where st.idstatus = '{$p['idstatus']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getRekapKondisiBarang($conn, $p=''){
			$sql ="select s.idbarang1, b.namabarang
							from aset.as_seri s 
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
							left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
							left join aset.ms_unit u on u.idunit=s.idunit
							where k.idkondisi = '{$p['idkondisi']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getMutasiBarang($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, 
							right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, u1.namaunit as unitasal, u2.namaunit as unittujuan, 
							m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang1
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang1,0,1) != '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getMaintenance($conn, $p=''){
			$sql ="select s.idbarang1, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), r.tglrawat, 105) as tglrawat, 
							convert(varchar(10), r.tglpembukuan, 105) as tglpembukuan
							from aset.as_rawat r
							join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
							left join aset.ms_jenisrawat jr on jr.idjenisrawat=r.idjenisrawat
							left join aset.as_seri s on s.idseri=rd.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u on u.idunit=r.idunit
							where jr.idjenisrawat = '{$p['idjenisrawat']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and 
							substring(s.idbarang1,0,1) != '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getMaintenanceJatuhTempo($conn, $p=''){
			$sql ="select s.idbarang1, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), r.tglrawat, 105) as tglrawat, 
							convert(varchar(10), r.tglpembukuan, 105) as tglpembukuan
							from aset.as_rawat r
							join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
							left join aset.ms_jenisrawat jr on jr.idjenisrawat=r.idjenisrawat
							left join aset.as_seri s on s.idseri=rd.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u on u.idunit=r.idunit
							where jr.idjenisrawat = '{$p['idjenisrawat']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and 
							substring(s.idbarang1,0,1) != '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getBukuInventaris($conn, $p=''){
			$sql ="select p.nobukti, convert(varchar(12), p.tglpembukuan, 106) as tglpembukuan, b.namabarang, pd.idbarang1, pd.merk, p.idsumberdana, 
							pd.qty, pd.harga, isnull((pd.qty*pd.harga),0) as jumlah, p.catatan, u.namaunit, l.namalokasi
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_lokasi l on l.idlokasi=p.idlokasi
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.nobukti";
			
			return $conn->Execute($sql);
		}
		
		function getBukuInduk($conn, $p=''){
			$sql ="select p.idperolehan, p.idjenisperolehan, p.idunit, u.namaunit, p.idlokasi, l.namalokasi, pd.idbarang1, b.namabarang, b.idsatuan, 
							convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, p.nobukti, 
							p.idsumberdana, pd.qty, pd.harga, pg.namadepan+' '+pg.gelarbelakang as pegawai, pd.merk, pd.spesifikasi
							from aset.as_perolehan p 
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_lokasi l on l.idlokasi=p.idlokasi
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_jenisperolehan jp on jp.idjenisperolehan=p.idjenisperolehan
							left join sdm.ms_pegawai pg on pg.idpegawai=p.idpegawai
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by p.idperolehan";
			
			return $conn->Execute($sql);
		}
		
		function getMutasiPeriode($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, 
							right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, u1.namaunit as unitasal, u2.namaunit as unittujuan, 
							m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang1
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang1,0,1) != '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getOpname($conn, $p=''){
			$sql ="select s.idbarang1, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), o.tglopname, 105) as tglopname, 
							convert(varchar(10), o.tglpembukuan, 105) as tglpembukuan
							from aset.as_opname o
							join aset.as_opnamedetail od on od.idopname=o.idopname
							left join aset.as_seri s on s.idseri=od.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u on u.idunit=o.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang1,0,1) != '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getOpnameHP($conn, $p=''){
			$sql ="select od.idbarang1, b.namabarang, convert(varchar(10), o.tglopname, 105) as tglopname, 
							convert(varchar(10), o.tglpembukuan, 105) as tglpembukuan, od.qtyawal, od.qtyakhir, od.idsatuan
							from aset.as_opnamehp o
							join aset.as_opnamehpdetail od on od.idopnamehp=o.idopnamehp
							left join aset.ms_barang1 b on od.idbarang1=b.idbarang1
							left join aset.ms_satuan s on s.idsatuan=od.idsatuan
							left join aset.ms_unit u on u.idunit=o.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(od.idbarang1,0,1)= '1'
							order by od.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getAsetAkuntansi($conn, $p=''){
			$sql ="select pd.idbarang1, b.namabarang, pd.idsatuan, pd.qty, pd.harga, u.namaunit, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, p.catatan,
							substring(convert(varchar(10), p.tglpembukuan, 101),4,2)+'/'+substring(convert(varchar(10), p.tglpembukuan, 101),9,2) as bln
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']}
							order by pd.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPerolehanAset($conn, $p=''){
			$sql ="select pd.idbarang1, b.namabarang, pd.merk, pd.spesifikasi, pd.qty, b.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_satuan s on s.idsatuan=pd.idsatuan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(pd.idbarang1,0,1) != '1' and p.nobukti is not null
							order by pd.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getStockHabisPakai($conn, $p=''){
			$sql ="select s.idbarang1, b.namabarang, s.jmlstock, st.satuan
							from aset.as_stockhp s 
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
							left join aset.ms_satuan st on st.idsatuan=s.idsatuan
							left join aset.ms_unit u on u.idunit=s.idunit
							where s.idbarang1 = '{$p['idbarang1']}' and
							u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang1,0,1)= '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getMutasiHabisPakai($conn, $p=''){
			$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, 
							convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, 
							b.namabarang, u1.namaunit as unitasal,
							u2.namaunit as unittujuan, m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang1
							from aset.as_mutasi m
							join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
							left join aset.as_seri s on s.idseri=md.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u1 on u1.idunit=m.idunit
							left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
							left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
							left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
							where u1.infoleft >= {$p['unit']['infoleft']} and u1.inforight <= {$p['unit']['inforight']} and
							substring(s.idbarang1,0,1)= '1'
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPenghapusanAset($conn, $p=''){
			$sql ="select p.idpenghapusan, jp.jenispenghapusan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, pd.idseri, 
							b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi
							from aset.as_penghapusan p 
							left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
							left join aset.as_seri s on s.idseri=pd.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_jenispenghapusan jp on jp.idjenispenghapusan=p.idjenispenghapusan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by p.idpenghapusan";
			
			return $conn->Execute($sql);
		}
		
		function getNilaiAset($conn, $p=''){
			$sql ="select s.idbarang1, b.namabarang, isnull(s.nilaiaset,0) nilaiaset, k.kondisi, l.namalokasi
							from aset.as_seri s
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1
							left join aset.ms_unit u on u.idunit=s.idunit
							left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
							left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPenyusutanAset($conn, $p=''){
			$sql ="select d.periode, s.idbarang1, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, s.idunit, 
							jp.jenispenyusutan, d.nilaisusut, d.nilaiaset, u.kodeunit 
							from aset.as_histdepresiasi d 
							left join aset.as_seri s on s.idseri=d.idseri 
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
							left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan=d.idjenispenyusutan 
							left join aset.ms_unit u on u.idunit=s.idunit 
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getRekapPenyusutan($conn, $p=''){
			$sql ="select d.periode, s.idbarang1, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, s.idunit, 
							jp.jenispenyusutan, d.nilaisusut, d.nilaiaset, u.kodeunit 
							from aset.as_histdepresiasi d 
							left join aset.as_seri s on s.idseri=d.idseri 
							left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
							left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan=d.idjenispenyusutan 
							left join aset.ms_unit u on u.idunit=s.idunit 
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} 
							order by s.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPerolehanHP($conn, $p=''){
			$sql ="select pd.idbarang1, b.namabarang, pd.merk, pd.spesifikasi, pd.qty, b.idsatuan
							from aset.as_perolehan p
							join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
							left join aset.ms_barang1 b on b.idbarang1=pd.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							left join aset.ms_satuan s on s.idsatuan=pd.idsatuan
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							substring(pd.idbarang1,0,1) = '1'
							order by pd.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPermintaanHP($conn, $p=''){
			$sql ="select td.idbarang1, b.namabarang, td.idsatuan, td.qty, td.harga, t.idsumberdana, s.namasupplier, 
							convert(varchar(10), t.tglpembukuan, 105) as tglpembukuan
							from aset.as_transhp t
							join aset.as_transhpdetail td on td.idtranshp=t.idtranshp
							left join aset.ms_barang1 b on b.idbarang1=td.idbarang1
							left join aset.ms_unit u on u.idunit=t.idunit
							left join aset.ms_supplier s on s.idsupplier=t.idsupplier
							where u.infoleft >= {$p['unit']['infoleft']} and u.inforight <= {$p['unit']['inforight']} and
							t.idjenistranshp != 306
							order by td.idbarang1";
			
			return $conn->Execute($sql);
		}
		
		function getPenghapusan($conn, $key){
			$sql ="select p.idpenghapusan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, pd.idseri, 
							b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi,
							b.idbarang1, u.namaunit, convert(varchar(10), p.tglpenghapusan, 105) as tglpenghapusan, u.kodeunit,
							count(case when s.idkondisi = 'B' then 1 else 0 end) as b,
							count(case when s.idkondisi = 'RB' then 1 else 0 end) as rb,
							count(case when s.idkondisi = 'RR' then 1 else 0 end) as rr,
							count(case when s.idkondisi = 'TB' then 1 else 0 end) as tb
							from aset.as_penghapusan p 
							left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
							left join aset.as_seri s on s.idseri=pd.idseri
							left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
							left join aset.ms_unit u on u.idunit=p.idunit
							where p.idpenghapusan = '$key'
							group by p.idpenghapusan, p.tglpembukuan, pd.idseri, b.namabarang, pd.nilaipenghapusan, 
							s.noseri, s.merk, s.spesifikasi, b.idbarang1, p.tglpenghapusan, u.namaunit, u.kodeunit";
			
			return $conn->GetRow($sql);
		}
		
		function getPerolehan($conn, $key){
		    $sql = "select u.kodeunit, u.namaunit, p.idlokasi, l.namalokasi, p.idpegawai, s.namalengkap, 
				convert(varchar(10), p.tglperolehan, 105) as tglperolehan
		        from aset.as_perolehan p  
		        left join aset.ms_unit u on u.idunit = p.idunit 
		        left join aset.ms_lokasi l on l.idlokasi = p.idlokasi 
		        left join sdm.v_biodatapegawai s on s.idpegawai = p.idpegawai 
		        where p.idperolehan = '$key'";

			return $conn->GetRow($sql);
		}

		function getBASTBNew($conn, $key){
		    $sql = "select d.idbarang1, b.namabarang, d.merk, d.spesifikasi, d.qty, d.harga 
		        from aset.as_perolehandetail d  
		        join aset.ms_barang1 b on b.idbarang1 = d.idbarang1 
		        where d.idperolehan = '$key' 
		        order by d.iddetperolehan";

			return $conn->Execute($sql);
		}
		
		function getBASTBHapus($conn, $key){
		    $sql = "select right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idbarang1, b.namabarang, s.merk, s.spesifikasi, p.nilaipenghapusan
				from aset.as_penghapusandetail p
				left join aset.as_seri s on s.idseri=p.idseri
				left join aset.ms_barang1 b on b.idbarang1=s.idbarang1
		        where p.idpenghapusan = '$key' 
		        order by p.iddetpenghapusan";

			return $conn->Execute($sql);
		}
		
		function getBASTBMutasi($conn, $key){
		    $sql = "select s.idbarang1,b.namabarang,s.merk,s.spesifikasi, right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        m.idunittujuan, m.idlokasitujuan, m.idpegawaitujuan, p.namalengkap as namapegawai, convert(varchar(10), s.tglperolehan, 105) as tglperolehan
		        from aset.as_mutasi m
		        join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
		        left join aset.as_seri s on s.idseri = md.idseri 
		        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		        left join sdm.v_biodatapegawai p on p.idpegawai = m.idpegawaitujuan
		        where m.idmutasi = '$key' 
		        order by md.iddetmutasi";

			return $conn->Execute($sql);
		}
		
		function getBASTBRawat($conn, $key){
		    $sql = "select s.idbarang1,b.namabarang,s.merk,s.spesifikasi, right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        rd.biaya, convert(varchar(10), r.tglrawat, 105) as tglrawat
		        from aset.as_rawat r
		        join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
		        left join aset.as_seri s on s.idseri = rd.idseri 
		        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		        where r.idrawat = '$key' 
		        order by rd.iddetrawat";

			return $conn->Execute($sql);
		}
		
		function getBASTBPinjam($conn, $key){
		    $sql = "select s.idbarang1,b.namabarang,s.merk,s.spesifikasi, p.catatan,
		        convert(varchar(10), p.tglpinjam, 105) as tglpinjam, convert(varchar(10), p.tglkembali, 105) as tglkembali, 
		        convert(varchar(10), s.tglperolehan, 105) as tglperolehan
		        from aset.as_pinjam p
		        join aset.as_pinjamdetail pd on pd.idpinjam=p.idpinjam
		        left join aset.as_seri s on s.idseri = pd.idseri 
		        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		        where p.idpinjam = '$key' 
		        order by pd.iddetpinjam";

			return $conn->Execute($sql);
		}
		
		function getKIR($conn){
			$sql="select idperolehan,namaunit,tglpembukuan,jenisperolehan,nobukti,
			        (case when isverify = 1 then 'Verified' else '' end) as isverify 
					from aset.as_perolehan p 
					left join aset.ms_unit u on u.idunit = p.idunit
					left join aset.ms_jenisperolehan j on j.idjenisperolehan = p.idjenisperolehan";
			
			return $conn->Execute($sql);
		}
		
	}
?>
