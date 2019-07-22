<?php
	// model role
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mRole extends mModel {
		const schema = 'gate';
		const table = 'sc_role';
		const order = 'koderole';
		const key = 'koderole';
		const label = 'role';
		
		// mendapatkan hak akses sebuah file
		function getFileAuth($conn,$modul,$file,$role) {
			$sql = "select r.caninsert, r.canupdate, r.candelete, r.aksesmenu from ".static::table('sc_menurole')." r
					join ".static::table('sc_menufile')." f on r.idmenu = f.idmenu and f.filemenu = '$file'
					join ".static::table('sc_menu')." m on r.idmenu = m.idmenu and m.kodemodul = '$modul'
					where r.koderole = '$role'";
			
			return $conn->GetArray($sql);
		}
	}
?>