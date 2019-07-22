<?php
	define( '__VALID_ENTRANCE', 1 );
	
	require_once('init.php');
	
	// memanggil fungsi
	$param = explode('/',$_GET['param']);
	if($param[0] != 'list') {
		$ctrl = $param[0];
		$func = $param[1];
		$param = array_slice($param,2);
		
		// include
		require_once(Route::getViewPath('lang_'.$conf['lang']));
		require_once(Route::getViewPath('class_'.$ctrl));
		
		$ctcl = 'f'.ucfirst($ctrl);
		$cont = new $ctcl();
		if(!method_exists($cont,$func)) {
			$sys = new cSevimaSystem();
			$sys->setError('Halaman tidak ditemukan',404);
			
			$return = cHelper::getJSON($sys);
		}
		else {
			// temp
			if($ctrl == 'user' and $func == 'login' and empty($_POST['reg_id'])) {
				$_POST['username'] = $param[0];
				$_POST['password'] = $param[1];
			}
			else if($ctrl == 'user' and $func == 'device' and empty($_POST['id'])) {
				$_POST['id'] = $param[1];
				$_POST['name'] = $param[2];
				$_POST['regId'] = $param[3];
				$_POST['brand'] = $param[4];
				$_POST['manufacturer'] = $param[5];
				$_POST['model'] = $param[6];
				$_POST['product'] = $param[7];
			}
			else if($ctrl == 'user' and $func == 'reset' and empty($_POST['token'])) {
				$_POST['token'] = $param[0];
				$_POST['passwordLama'] = $param[1];
				$_POST['passwordBaru'] = $param[2];
			}
			else if($ctrl == 'user' and $func == 'password' and empty($_POST['email']))
				$_POST['email'] = $param[0];
			
			$return = $cont->$func($param,$_POST);
		}
		
		// bersihkan buffer dan set header
		ob_clean();
		
		header('content-type: application/json');
		
		// tampilkan
		die($return);
	}
	else {
		// include
		require_once(Route::getViewPath('class_model'));
		
		// untuk testing interface
		$p_title = 'Percobaan Interface Mobile';
		$p_url = $conf['url'];
		
		// user
		$a_user = array();
		$a_user['201312003'] = 'Mahasiswa';
		$a_user['5000'] = 'Dosen';
		
		$r_user = $_POST['user'];
		if(empty($r_user))
			$r_user = $_SESSION['UEUSIAMOBILE']['USER'];
		else
			$_SESSION['UEUSIAMOBILE']['USER'] = $r_user;
		if(empty($r_user))
			$r_user = key($a_user);
		
		// password
		if($r_user == '201312003') $r_pass = '201312003';
		if($r_user == '5000') $r_pass = '5000';
		
		$model = new mMobile();
		$row = $model->getLoginByUsername($r_user);
		list($ismhs,$isdosen) = $model->getRolesByUsername($r_user);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo (empty($p_title) ? '' : $p_title.' - ').SITE_NAME ?></title>
	<link rel="icon" type="image/x-icon" href="../siakad/images/favicon.png">
</head>
<body>
	<h3>Daftar Interface</h3>
	<h4>
		<form id="form" method="post">
		<?php echo UI::createSelect('user',$a_user,$r_user,'ControlStyle',true,'onchange="document.getElementById(\'form\').submit()"') ?>
		</form>
	</h4>
	<h4>
		Username: <?php echo $r_user ?>
		<?php if(!empty($row)) { ?>
		<br />Token: <?php echo $row['token'] ?>
		<?php if(!empty($row['regid'])) { ?>
		<br />Reg ID: <?php echo $row['regid'] ?>
		<?php }} ?>
	</h4>
	<ol>
		<li><a href="<?php echo $p_url."timeline/timeline_public" ?>" target="_blank" style="color:blue">Request Timeline Public</a></li>
		<?php if(empty($row)) { ?>
		<li><a href="<?php echo $p_url."user/login/$r_user/$r_pass" ?>" target="_blank" style="color:blue">Require Login</a></li>
		<li><a href="<?php echo $p_url."user/loginfacebook/100000569625699" ?>" target="_blank" style="color:blue">Require Login Facebook</a></li>
		<li><a href="<?php echo $p_url."user/password/nyanda_kame@yahoo.com" ?>" target="_blank" style="color:blue">Request Password</a></li>
		<?php } else { ?>
		<li><a href="<?php echo $p_url."user/reset/".$row['token']."/$r_pass/$r_pass" ?>" target="_blank" style="color:blue">Reset Password</a></li>
		<?php if(empty($row['regid'])) { ?>
		<li><a href="<?php echo $p_url."user/device/".$row['token']."/IREJECT0123456789/iReject_XS_2015/regidnyaireject/iReject/Apel/IR001/iReject XS" ?>" target="_blank" style="color:blue">Register Device</a></li>
		<?php } else { ?>
		<li><a href="<?php echo $p_url."user/notification/".$row['token'] ?>" target="_blank" style="color:blue">Request Notification User</a></li>
		<li><a href="<?php echo $p_url."user/uploadnotification/".$row['token'] ?>" target="_blank" style="color:blue">Upload Notification User</a></li>
		<li><a href="<?php echo $p_url."timeline/timeline/".$row['token'] ?>" target="_blank" style="color:blue">Request Timeline User</a></li>
		<li><a href="<?php echo $p_url."timeline/upload/".$row['token'] ?>" target="_blank" style="color:blue">Upload Timeline (User dan Forum)</a></li>
		<li><a href="<?php echo $p_url."timeline/requestcomment/".$row['token']."/14" ?>" target="_blank" style="color:blue">Request Comment</a></li>
		<li><a href="<?php echo $p_url."timeline/comment/".$row['token']."/14" ?>" target="_blank" style="color:blue">Upload Comment</a></li>
		<li><a href="<?php echo $p_url."timeline/like/".$row['token']."/7/1" ?>" target="_blank" style="color:blue">Upload Like Timeline (User dan Forum) is like</a></li>
		<li><a href="<?php echo $p_url."timeline/like/".$row['token']."/7/0" ?>" target="_blank" style="color:blue">Upload Like Timeline (User dan Forum) is cancel like</a></li>
		<li><a href="<?php echo $p_url."forum/timeline/".$row['token']."/78" ?>" target="_blank" style="color:blue">Request Timeline Forum</a></li>
		<li><a href="<?php echo $p_url."forum/forum/".$row['token'] ?>" target="_blank" style="color:blue">Request List Forum</a></li>
		<li><a href="<?php echo $p_url."forum/member/".$row['token']."/78" ?>" target="_blank" style="color:blue">Request List Member on Forum</a></li>
		<li><a href="<?php echo $p_url."user/me/".$row['token'] ?>" target="_blank" style="color:blue">Request Me</a></li>
		<li><a href="<?php echo $p_url."user/profile/".$row['token'] ?>" target="_blank" style="color:blue">Request User Profile</a></li>
		<li><a href="<?php echo $p_url."user/uploadprofile/".$row['token']."/1234556" ?>" target="_blank" style="color:blue">Upload Profile</a></li>
		<li><a href="<?php echo $p_url."facebook/link/".$row['token']."/100000569625699" ?>" target="_blank" style="color:blue">Upload Link Facebook</a></li>
		<li><a href="<?php echo $p_url."facebook/unlink/".$row['token'] ?>" target="_blank" style="color:blue">Upload unLink Facebook</a></li>
		<li><a href="<?php echo $p_url."academic/period/".$row['token'] ?>" target="_blank" style="color:blue">Request Period</a></li>
		
		<li><a href="<?php echo $p_url."user/calendar/".$row['token']?>" target="_blank" style="color:red">Request Calendar</a></li>
		<li><a href="<?php echo $p_url."user/uploadcalendar/".$row['token']."/1234556" ?>" target="_blank" style="color:blue">Upload Calendar</a></li>
		<li><a href="<?php echo $p_url."academic/calendar/".$row['token']?>" target="_blank" style="color:red">Request Academic Calendar</a></li>
		<li><a href="<?php echo $p_url."academic/presence/".$row['token'] ."/201312003" ?>" target="_blank" style="color:blue">Request Presence</a></li>
		<li><a href="<?php echo $p_url."academic/course/".$row['token']."/201312003/STUDY_KRS" ?>" target="_blank" style="color:red">Request Course (STUDY_KRS)</a></li>
		<li><a href="<?php echo $p_url."academic/course/".$row['token']."/201312003/STUDY_KHS" ?>" target="_blank" style="color:blue">Request Course (STUDY_KHS)</a></li>
		<li><a href="<?php echo $p_url."academic/course/".$row['token']."/201312003/STUDY_TRANSCRIPT" ?>" target="_blank" style="color:red">Request Course (STUDY_TRANSCRIPT)</a></li>
		<li><a href="<?php echo $p_url."academic/finance/".$row['token']."/201312003/FINANCE_BILL" ?>" target="_blank" style="color:blue">Request Finance Info (FINANCE_BILL)</a></li>
		<li><a href="<?php echo $p_url."academic/finance/".$row['token']."/201312003/FINANCE_PAYMENT" ?>" target="_blank" style="color:blue">Request Finance Info (FINANCE_PAYMENT)</a></li>
		<?php /* if($isdosen) { ?>
		<li><a href="<?php echo $p_url."academic/presenceclass/".$row['token']."/2014-06-01|1|20133|2012|99|ESA120|24|K|1" ?>" target="_blank" style="color:blue">Class Presence</a></li>
		<li><a href="<?php echo $p_url."academic/presencesave/".$row['token'] ?>/201011173/2014-06-01|1|20133|2012|99|ESA120|24|K|1" target="_blank" style="color:blue">Save Presence</a></li>
		<?php } else { ?>
		<li><a href="<?php echo $p_url."academic/presenceclass/".$row['token']."/2014-06-30|1|20133|2012|2210120|EAA811|02|K|1" ?>" target="_blank" style="color:blue">Class Presence</a></li>
		<?php } */ ?>
		<?php if($isdosen) { ?>
		<li><a href="<?php echo $p_url."academic/guidance/".$row['token'] ?>" target="_blank" style="color:blue">Request Guidance</a></li>
		<?php } ?>
		<?php } ?>
		<li><a href="<?php echo $p_url."user/logout/".$row['token'] ?>" target="_blank" style="color:blue">Require Logout</a></li>
		<?php } ?>
	</ol>
</body>
</html>