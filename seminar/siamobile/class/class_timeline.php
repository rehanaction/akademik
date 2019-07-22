<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_helper'));
	require_once(Route::getViewPath('class_object'));
	require_once(Route::getViewPath('class_model'));
	require_once(Route::getViewPath('class_model_timeline'));

	
	/**
	 * Class untuk controller user aplikasi mobile fitur timeline
	 * @author Sevima
	 * @version 1.0
	 */
	 
	class fTimeLine {
		protected $model; //butuh buat otorisasi token
		protected $tModel;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobile();
			$this->tModel = new mMobileTimeline();
		}
		/**
		* List Timeline Public
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function timeline_public($param,$post) {
			
			// parameter
			//$state 		= $param[1];
			$latestId	= $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_TIMELINE);
			
			$a_time 	= array('index'=>'timeLines','data'=>array());	
			
			// data timeline
			$data = $this->tModel->getListTimeline(null, $latestId, null);
			
			if(!empty($data)){
				foreach($data as $key => $val){
					$timeline = new cTimeline();
					$timeline->setAttributes($val);
					
					$a_time['data'][] = $timeline;
				}
			}
			return cHelper::getJSON($sys,$a_time);
			
		}
		/**
		* List Timeline
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function timeline($param,$post) {
			// parameter
			$token 		= $param[0];
			$lastId	= $param[1];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_TIMELINE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			if(empty($groupId)){
				require_once(Route::getViewPath('class_forum'));
				
				$forum	= new fForum();				
				$resforum = $forum->forum(array($token), array());
				$resforum = json_decode($resforum,true);
				$resgroup = array();
				
				foreach($resforum['data']['groups'] as $k => $v){
					$resgroup[] = $v['id'];
				}
				
				$groupId = "'".implode("','", $resgroup)."'";
			}
			
			$a_time 	= array('index'=>'timeLines','data'=>array());	
			
			// data timeline
			$data = $this->tModel->getListTimeline($row['userid'], $lastId);

			if(!empty($data)){
				foreach($data as $key => $val){
					$timeline = new cTimeline();
					$timeline->setAttributes($val);
					
					$a_time['data'][] = $timeline;
				}
			}
			
			$data = $this->tModel->getCountNotif($row['userid']);
			$countNotif = new cCountNotif();
			$countNotif->setAttributes($data);
			
			
			return cHelper::getJSON($sys,$countNotif,$a_time);
			
		}
		
		/**
		* Upload Timeline
		* @param array $param
		* @param array $post (status, file_image, file_attach, group_id)
		* @return string json
		*/
		function upload($param,$post) {
			global $conn;
			list($token) = $param;

			$sys = new cSevimaSystem(cLang::ACT_POST_TIMELINE);
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}

			if(empty($post['status']) && empty($post['link']) && !isset($_FILES['file_image']) && !isset($_FILES['file_attach']) && empty($post['group_id'])){
				$sys->setError('Data yang dikirim kurang lengkap',400);
				return cHelper::getJSON($sys);
			}else{
				// ini untuk grup public karena dari device kalo tanpa apa2 dikasi ''
				if($post['groupId']=='')
					$post['groupId'] = NULL;
				// ini untuk handel status yang ''
				if($post['status']=='')
					$post['status'] = NULL;
				
				$data = array(
					'timeline_status' => $post['status'],
					'timeline_link' => $post['link'],
					'timeline_photo' => NULL,
					'timeline_file' => NULL,
					'timeline_date' => date('Y-m-d H:i:s'),
					't_updatetime' => date('Y-m-d H:i:s'),
					'group_id' => $post['groupId'],
					'is_materi_kuliah' => $post['isMateriKuliah'],
					'user_id' => $row['userid']
				);

				$conn->StartTrans(); 
				$err = $this->tModel->uploadTimeline($data);
				if($err){
					$lastId = $this->tModel->getLastId('mobile.ms_timeline_timeline_id_seq');

					/* NEW FEATURE*/
					if(isset($_FILES["file_image"])){	
						$upload = cHelper::uploadImage('file_image', $lastId, 'timeline_picture');

						if($upload === TRUE){
							$data = array('timeline_photo' => $_FILES["file_image"]['name']);
							$err = $this->tModel->updateTimeline($data, $lastId);
						}else{
							$sys->setError($upload, 400);
							return cHelper::getJSON($sys);						
						}
					}

					if(isset($_FILES["file_attach"])){
						$upload = cHelper::uploadFile('file_attach', $lastId, 'timeline_file');

						if($upload === TRUE){
							$data = array('timeline_file' => $_FILES["file_attach"]['name']);
							$err = $this->tModel->updateTimeline($data, $lastId);
						}else{
							$sys->setError($upload,400);
							return cHelper::getJSON($sys);						
						}
					}
					

					if($err){
						$conn->CompleteTrans();
						$sys->setInfos('Posting berhasil disimpan');
						return cHelper::getJSON($sys);
					}
				}
				$sys->setError('Gagal menyimpan postingan',400);
				return cHelper::getJSON($sys);			
			}
		}
		
		/**
		* Like / Cancel like Timeline
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function like($param,$post) {
			// parameter
			$token 	= $param[0];
			$timelineId	= $param[1];
			$isLike	= $param[2];
			
			// set object
			if($isLike == "1")
				$sys = new cSevimaSystem(cLang::ACT_LIKE_TIMELINE);
			else
				$sys = new cSevimaSystem(cLang::ACT_CANCEL_LIKE_TIMELINE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			$this->tModel->uploadLikeTimeline($row['userid'], $timelineId, $isLike);

			return cHelper::getJSON($sys);
		}
		
		/**
		* Comment Timeline
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function comment($param,$post) {
			// parameter
			$token 		= $param[0];
			$timelineId	= $param[1];
			$comment	= $post["comment"];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_POST_COMMENT);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			$data = $this->tModel->uploadCommentTimeline($row['userid'], $timelineId, $comment);
			
			$timeline = null;	
			if(!empty($data)){
				$timeline = new cPostTimeLine();
				$timeline->setAttributes($data);
			}
			
			
			return cHelper::getJSON($sys, $timeline);
		}
		
		/**
		* Comment Timeline
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function requestcomment($param,$post) {
			$post["comment"] = null;
			return $this->comment($param,$post);
		}
	}

?>