<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	/**
	 * Language for mobile application: English
	 * @author Sevima
	 * @version 1.0
	 */
	class cLang {
		const ACT = 'Operation';
		const ACT_BILL = 'Bill data retrieval';
		const ACT_CALENDAR = 'Calendar data retrieval';
		const ACT_UPLOAD_CALENDAR = 'Calendar data uploaded';
		const ACT_CALENDAR_ACADEMIC = 'Academic Calendar data retrieval';
		const ACT_DAILY_CALENDAR = 'Daily Calendar data retrieval';
		const ACT_EXERCISE_CALENDAR = 'Academic exercise data retrieval';
		const ACT_PERSONAL_CALENDAR = 'Personal Calendar data retrieval';
		const ACT_PAYMENT = 'Payment data retrieval';
		const ACT_DEVICE = 'Device registration';
		const ACT_COURSE = 'Course retrieval';
		const ACT_FACULTY = 'Faculty and department retrieval';
		const ACT_LOGIN = 'Login'; // use login for noun or adjective and log in for verb
		const ACT_LOGIN_FACEBOOK = 'Login Facebook';
		const ACT_LOGOUT = 'Logout';
		const ACT_ME = 'User data retrieval';
		const ACT_FACEBOOK_LINK = 'User Link with Facebook';
		const ACT_FACEBOOK_UNLINK = 'User unLink with Facebook';
		const ACT_PASSWORD_FORGET = 'Password request';
		const ACT_PASSWORD_RESET = 'Password reset';
		const ACT_NOTIFICATION = 'Notification retrieval';
		const ACT_UPLOAD_NOTIFICATION = 'Notification uploaded';
		const ACT_PERIOD = 'Period retrieval';
		const ACT_PRESENCE_CLASS = 'Class presence retrieval';
		const ACT_PRESENCE_STUDENT = 'Student presence retrieval';
		const ACT_SCHEDULE = 'Student schedule retrieval';
		const ACT_STUDENT = 'Student retrieval';
		const ACT_STUDY = 'Study retrieval';
		const ACT_FINANCE = 'Finance retrieval';
		const ACT_TIMELINE = 'Timeline retrieval';
		const ACT_POST_TIMELINE = 'Post Timeline';
		const ACT_LIKE_TIMELINE = 'Like Timeline';
		const ACT_CANCEL_LIKE_TIMELINE = 'Cancel Like Timeline';
		const ACT_POST_COMMENT = 'Post comment';
		const ACT_FORUM = 'Forum List Retrieval';
		const ACT_MEMBER = 'Forum Member Retrieval';
		
		const DATA_BILL = 'current bill';
		const DATA_CALENDAR = 'current calendar';
		const DATA_CALENDAR_ACADEMIC = 'current academic calendar';
		
		const ERROR_ACCESS = 'Data access forbidden';
		const ERROR_ACCESS_FEATURE = 'Feature access forbidden';
		const ERROR_ACCESS_STUDENT = 'Student data access forbidden';
		
		const ERROR_EXISTS = 'Data already exists';
		const ERROR_INVALID_TOKEN = 'Token invalid';
		
		const ERROR_COURSE_STATE_NOT_FOUND = 'Unknown state';
		const ERROR_FINANCE_STATE_NOT_FOUND = 'Unknown state';
		
		const ERROR_LOGIN_PASSWORD_INCORRECT = 'Password incorrect';
		const ERROR_LOGIN_USER_NOT_FOUND = 'User not found';
		
		const ERROR_PASSWORD_FORGET_EMAIL_NOT_FOUND = 'Email not found';
		const ERROR_PASSWORD_RESET_PASSWORD_INCORRECT = 'Old password incorrect';
		
		function getEmptyMsg($data='data') {
			return 'No '.$data;
		}
		
		function getFailedMsg($act) {
			return $act.' failed';
		}
		
		function getSuccessMsg($act) {
			return $act.' success';
		}
	}