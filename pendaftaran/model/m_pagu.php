<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once ($conf['helpers_dir'].'query.class.php');
	require_once(Route::getModelPath('model'));
	
	class mPagu extends mModel{
            const schema = 'pendaftaran';
	    const table = 'pd_paguunit';
	    const order = 'kodeunit';
	    const key = 'kodeunit,periodedaftar,jalurpenerimaan,idgelombang';
	    const label = 'pagu';
	    
	    // mendapatkan kueri list
		function listQuery() {
			$sql = "select p.* from pendaftaran.pd_paguunit p 
					JOIN pendaftaran.pd_gelombangdaftar g
					on p.jalurpenerimaan=g.jalurpenerimaan and p.periodedaftar=g.periodedaftar
					and p.idgelombang=g.idgelombang and g.isaktif='t'
					";
			
			return $sql;
		}
	    // mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jalur': return "p.jalurpenerimaan = '$key'";
			}
		}
	    function getPagu($periodedaftar){
                global $conn;
			$sql = "select u.namaunit, SUM(p.pagu) as pagu
							from pendaftaran.pd_paguunit p INNER JOIN gate.ms_unit u
							ON p.kodeunit=u.kodeunit
							where periodedaftar='$periodedaftar'
							GROUP BY (u.namaunit)";
			return $conn->SelectLimit($sql,10);   
        }
	    function getUnit($conn){
			$sql="SELECT kodeunit, namaunit FROM gate.ms_unit WHERE level='2' and isakad=-1";
			return Query::arrQuery($conn,$sql);
	    }
	    function getDataPagu($conn, $periode=''){
			
			$sql="select p.jalurpenerimaan,p.pagu,u.namaunit,g.namagelombang 
					from pendaftaran.pd_paguunit p
					JOIN gate.ms_unit u using (kodeunit)
					JOIN pendaftaran.lv_gelombang g using (idgelombang)
					where p.periodedaftar='$periode'";
					
			return $conn->GetArray($sql);
		}
		function cekPagu($conn,$data){

			$pagu=$conn->GetOne("select pg.pagu from pendaftaran.pd_paguunit pg
								where pg.jalurpenerimaan='".$data['jalurpenerimaan']."' and pg.periodedaftar='".$data['periodedaftar']."' and pg.idgelombang='".$data['idgelombang']."' and pg.kodeunit='".$data['pilihanditerima']."'");
			$jum_pendaftar=$conn->GetOne("select sum(1) from pendaftaran.pd_pendaftar pg
								where pg.isdaftarulang=-1 and pg.jalurpenerimaan='".$data['jalurpenerimaan']."' and pg.periodedaftar='".$data['periodedaftar']."' and pg.idgelombang='".$data['idgelombang']."' and pg.pilihanditerima='".$data['pilihanditerima']."'");
			
			$sisapagu=$pagu-$jum_pendaftar;	
			return $sisapagu;
			
		}
		
	}
?>
