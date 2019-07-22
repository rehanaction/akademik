<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_helper'));
	require_once(Route::getViewPath('class_object'));
	require_once(Route::getViewPath('class_model_timeline'));
	require_once(Route::getViewPath('class_model'));
	
	/**
	 * Class untuk controller user aplikasi mobile fitur timeline
	 * @author Sevima
	 * @version 1.0
	 */
	 
	class fForum {
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
		* List Forum 
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function forum($param,$post) {
			// parameter
			$token 		= $param[0];
			$latestId	= $param[1];
			$keyword	= $post['search'];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_FORUM);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			$this->tModel->addForumUniversitas($row['userid']);
			
			// data forum
			$role = $this->tModel->getRole($row['userid']);
			if($role['koderole'] == 'D')
				$data = $this->tModel->addForumDosen($row['userid']);
			else if($role['koderole'] == 'M')
				$data = $this->tModel->addForumMahasiswa($row['userid']);
				
			
			$data = $this->tModel->getListForum($row['userid'], $keyword, $latestId);
				
			$index = forward_static_call(array('cGroup','getIndexName'),true);
			$a_forum = array('index'=>$index,'data'=>array());
			
			//forum
			if(!empty($data)){
				foreach($data as $key => $val){
					$forum = new cGroup();
					$forum->setAttributes($val);
					
					$a_forum['data'][] = $forum;
				}
			}
			
			return cHelper::getJSON($sys,$a_forum);
		}
		
		
		/**
		* Forum Member
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function member($param,$post) {
		
			// parameter
			$token 		= $param[0];
			$groupId	= $param[1];
			$lastId		= $param[2];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_MEMBER);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			$index 		= forward_static_call(array('cMember','getIndexName'),true);
			$a_member 	= array('index'=>$index,'data'=>array());	
		
			$data = $this->tModel->getListMember($groupId, $lastId);	
			
			if(!empty($data)){
				foreach($data as $key => $val){						
					$member = new cMember();
					$member->setAttributes($val);
					
					$a_member['data'][] = $member;
				}
			}
			
			return cHelper::getJSON($sys,$a_member);
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
			$groupId	= $param[1];
			$lastId 	= $param[2];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_TIMELINE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			/*if(empty($groupId)){
				require_once(Route::getViewPath('class_forum'));
				
				$forum	= new fForum();				
				$resforum = $forum->forum(array($token), array());
				$resforum = json_decode($resforum,true);
				$resgroup = array();
				
				foreach($resforum['data']['groups'] as $k => $v){
					$resgroup[] = $v['id'];
				}
				
				$groupId = "'".implode("','", $resgroup)."'";
			}*/
			
			$a_time 	= array('index'=>'timeLines','data'=>array());	
			
			// data timeline
			$data = $this->tModel->getListTimelineGroup($row['userid'],$lastId,$groupId);
			
			if(!empty($data)){
				foreach($data as $key => $val){
					$timeline = new cTimeline();
					$timeline->setAttributes($val);
					
					$a_time['data'][] = $timeline;
				}
			}
			return cHelper::getJSON($sys,$a_time);
			
		}
	}

?>