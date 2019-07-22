/*
					if(isset($_FILES["file_image"])){	
						if($_FILES["file_image"]['size'] > 2048000){
							$sys->setError('Ukuran gambar terlalu besar.', 400);
							return cHelper::getJSON($sys);
						}
							
						$file = explode('.', $_FILES["file_image"]['name']);
						$extention = end($file);

						$server = $_SERVER['SCRIPT_FILENAME'];
						$cek = str_replace("index.php", "", $server);
						$target_dir = $cek."/timeline_picture/";
						$name_file = $lastId.'.'.$extention;
						$upload = move_uploaded_file($_FILES["file_image"]["tmp_name"], $target_dir.$name_file);
						
						if($upload){
							$data = array('timeline_photo' => $name_file);
							$err = $this->tModel->updateTimeline($data, $lastId);
						}else{
							$sys->setError('Gagal upload gambar',400);
							return cHelper::getJSON($sys);						
						}
					}

					if(isset($_FILES["file_attach"])){	
						if($_FILES["file_attach"]['size'] > 2048000){
							$sys->setError('Ukuran file terlalu besar.', 400);
							return cHelper::getJSON($sys);
						}
						
						$file = explode('.', $_FILES["file_attach"]['name']);
						$extention = end($file);

						$server = $_SERVER['SCRIPT_FILENAME'];
						$cek = str_replace("index.php", "", $server);
						$target_dir = $cek."/timeline_file/";
						$name_file = $lastId.'.'.$extention;
						$upload = move_uploaded_file($_FILES["file_attach"]["tmp_name"], $target_dir.$name_file);
						if($upload){
							$data = array('timeline_file' => $name_file);
							$err = $this->tModel->updateTimeline($data, $lastId);
						}else{
							$sys->setError('Gagal upload file',400);
							return cHelper::getJSON($sys);						
						}
					}*/