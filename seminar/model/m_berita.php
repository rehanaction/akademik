<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
        require_once(Route::getModelPath('model'));
	
        class mBerita extends mModel{
		const schema = 'pendaftaran';
		const table = 'pd_berita';
		const order = 'idberita';
		const key = 'idberita';
		const label = 'berita';
		const sequence = 'pd_berita_idberita_seq';
		
		function getBerita($conn){
		    $sql="SELECT * FROM pendaftaran.pd_berita where isaktif ='-1' order by t_updatetime desc LIMIT 5 ";
		    return $conn->SelectLimit($sql);
		}
		function getMaxpage($conn){
		    $sql="SELECT * FROM pendaftaran.pd_berita";
		    $ok = $conn->SelectLimit($sql);
		    $ok=$ok->RecordCount();
		    $ok=($ok/3)+1;
		    
		    return (int)$ok;
		}
	    
        }
