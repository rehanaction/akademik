<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
        require_once(Route::getModelPath('model'));
	
        class mKuisioner extends mModel{
		const schema = 'pendaftaran';
		const table = 'pd_jawabanquisioner';
		const order = 'nopendaftar';
		const key = 'nopendaftar';
		const label = 'Kuisioner';
		
		function arrPertanyaan1(){
			$data = array('1'=>'Kunjungan Sekolah',
							'2'=>'Pameran Sekolah', 
							'3'=>'Iklan Cetak',
							'4'=>'Data Langsung',
							'5'=>'Media Online',
							'6'=>'Website',
							'7'=>'Spanduk',
							'8'=>'Pameran JHCC',
							'9'=>'Seminal',
							'10'=>'Teman',
							'11'=>'Poster',
							'12'=>'Lain nya');
			return $data;
			
		}
		function arrPertanyaan2(){
			$data = array('1'=>'Biaya Perkuliahan Terjangkau',
						  '2'=>'Program akademik berkualitas',
						  '3'=>'Permintaan dari orang tua/diajak Saudara',
						  '4'=>'Akses Mudah',
						  '5'=>'Kegiatan Mahasiswanya menarik',
						  '6'=>'Fasilitas IT yang lengkap (Wifi,Microsoft learning gateway, dll)',
						  '7'=>'Diajak teman',
						  '8'=>'Lain nya');
			return $data;
		}
		function arrPertanyaan3(){
			$data = array('1'=>'Koran',
						  '2'=>'Majalah',
						  '3'=>'Tidak Ada',
						  '4'=>'Lain nya');
			return $data;
		}
		function arrPertanyaan4(){
			$data = array('1'=>'Ya',
						  '2'=>'Tidak');
			return $data;
		}
		


		function arrPertanyaan7(){
			$data = array('1'=>'Orang tua',
						  '2'=>'Ikatan dinas',
						  '3'=>'Wali',
						  '4'=>'Sendiri',
						  '5'=>'Beasiswa',
						  '6'=>'Lainnya');
			return $data;
		}
		function arrPertanyaan8(){
			$data = array('1'=>'Orang tua',
						  '2'=>'Ikut saudara',
						  '3'=>'Sendiri',
						  '4'=>'kost/sewa/kontrak',
						  '5'=>'Lainnya');
			return $data;
		}
		function arrPertanyaan10(){
			$data = array('1'=>'Kendaraan Pribadi',
						  '2'=>'Motor',
						  '3'=>'Kendaraan Umum',
						  '4'=>'Lainnya');
			return $data;
		}
		function arrPertanyaan11(){
			$data = array('1'=>'Bakat',
						  '2'=>'Ikatan dinas',
						  '3'=>'Super Semar',
						  '4'=>'Beasiswa Swasta',
						  '5'=>'Jurusan Langka',
						  '6'=>'Lainnya');
			return $data;
		}
		function arrPertanyaan13(){
			$data = array('1'=>'Olahraga',
						  '2'=>'Kesenian',
						  '3'=>'Olimpiade',
						  '4'=>'Juara Sekolah',
						  '5'=>'Lainnya');
			return $data;
		}
			
		
		function getcolumn($conn){
			$a_input = array();
			//alasan tau ueu
			$a_input[] = array('kolom' => 'jawab_1',	'label' => 'Darimana anda mengetahui UEU ?','type'=>'S', 'option'=>self::arrPertanyaan1());
			$a_input[] = array('kolom' => 'jawab_2',	'label' => 'Berikan urutan alasan dibawah ini, sesuai dengan urutan alasan anda masuk UEU ?','type'=>'S', 'option'=>self::arrPertanyaan2());
			$a_input[] = array('kolom' => 'jawab_3',	'label' => 'Sebutkan media cetak yang sering anda baca ?','type'=>'S', 'option'=>self::arrPertanyaan3());
			$a_input[] = array('kolom' => 'jawab_4',	'label' => 'Apakah saudara mendaftar di perguruan tinggi ?','type'=>'S', 'option'=>self::arrPertanyaan4());
			$a_input[] = array('kolom' => 'jawab_5',	'label' => 'Apakah saudara mendaftar di perguruan tinggi swasta lainnya ?','type'=>'S', 'option'=>self::arrPertanyaan4());
			
			$a_input[] = array('kolom'=>'jawabketerangan_1','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_2','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_3','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_4','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_5','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			
			$a_input[] = array('kolom'=>'jawab_6_1','label'=>'&nbsp;&nbsp;&nbsp;1.  ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawab_6_2','label'=>'&nbsp;&nbsp;&nbsp;2.  ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawab_6_3','label'=>'&nbsp;&nbsp;&nbsp;3.  ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawab_6_4','label'=>'&nbsp;&nbsp;&nbsp;4.  ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawab_6_5','label'=>'&nbsp;&nbsp;&nbsp;5.  ','maxlength' => 100, 'size' => 30);
			
			// data mendukung
			$a_input[] = array('kolom' => 'jawab_7',	'label' => 'Kuliah Di biayai oleh ?','type'=>'S', 'option'=>self::arrPertanyaan7());
			$a_input[] = array('kolom' => 'jawab_8',	'label' => 'Status tempat tinggal ?','type'=>'S', 'option'=>self::arrPertanyaan8());
			$a_input[] = array('kolom' => 'jawab_9',	'label' => 'Memiliki Komputer ?','type'=>'S', 'option'=>self::arrPertanyaan4());
			$a_input[] = array('kolom' => 'jawab_10',	'label' => 'Transportasi yang digunakan menuju kampus UEU ?','type'=>'S', 'option'=>self::arrPertanyaan10());
			$a_input[] = array('kolom' => 'jawab_11',	'label' => 'Jenis Beasiswa (hanya diisi bagi penerima beasiswa lain luar UEU) ?','type'=>'S', 'option'=>self::arrPertanyaan11());
			$a_input[] = array('kolom' => 'jawab_13',	'label' => 'Prestasi yang pernah dicapai','type'=>'S', 'option'=>self::arrPertanyaan13());

			$a_input[] = array('kolom'=>'jawabketerangan_7','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_8','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_10','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_11','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_12','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			$a_input[] = array('kolom'=>'jawabketerangan_13','label'=>'Keterangan ','maxlength' => 100, 'size' => 30);
			return $a_input;
			
		}
		function cekJawaban($urutan,$jawaban){
			if ($urutan == $jawaban)
				$data = '<img src="images/check.png">';
			else
				$data = '';
			return $data; 
			
			}
		
		
	    
        }
