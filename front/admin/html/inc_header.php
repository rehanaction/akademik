<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$a_hakakses = array();
	foreach(Modul::getAccessRole() as $t_akses)
		$a_hakakses[$t_akses['role'].':'.$t_akses['unit']] = $t_akses['namarole'].' - '.$t_akses['namaunit'];
	
	// combo hak akses
	$l_hakakses = UI::createSelect('hakakses',$a_hakakses,Modul::getRole().':'.Modul::getUnit())
?>
<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="scripts/jquery.menu.js"></script>
<script type="text/javascript" src="scripts/common.js"></script>
<link type="text/css" href="style/menu.css" rel="stylesheet">
<div class="WorkHeader">
	<div style="width:1010px;margin:0 auto;">
		<ul class="topnav">
			<li>
				<a href="javascript:void(0)" style="padding:0px;height:22px;">
					<img src="images/gear.png" width="25" style="position:relative;bottom:5px; float:left;">
				</a>
				<ul class="subnav" style="width:450px;padding:15px 5px; border-bottom-left-radius:4px; border-bottom-right-radius:4px;">
					<div class="DivImg" style="margin-bottom:10px;">
						<img src="images/unit.png">
						<?= $l_hakakses ?>
						<input type="button" value="Ganti" onclick="changeRole()"> &nbsp;
					</div>				
					<div style="float:left;margin-left:75px">&nbsp;</div>
						<div class="DivButton" onclick="goView('home')" style="float:left;margin-left:3px;margin-top:-3px">
							<img src="images/home.png" width="16"> <br>Home
						</div>
						<div class="DivButton" onclick="goGuide()" style="float:left;margin-left:3px;margin-top:-3px">
							<img src="images/help.png" width="16"> <br>User Guide
						</div>
						<div class="DivButton" onclick="goTo('<?= $conf['menu_path'] ?>')" style="float:left;margin-left:3px;margin-top:-3px">
							<img src="images/menu.png" width="16"> <br>Menu SIM
						</div>
						<div class="DivButton" onclick="goView('logout')" style="float:left;margin-left:3px;margin-top:-3px">
							<img src="images/exit.png" width="16"> <br>Log Out
						</div>
				</ul>
			</li>
		<?php
			$i = 0;
			echo Modul::createMenu($i);
		?>
		</ul>
		<? /*
		<ul class="topnav" style="float:right;padding-top:0px;">
			<li style="padding:0px;">
				<a href="javascript:void(0)">
					<img src="images/user.png" style="float:left;margin-right:5px;"> 
					<?= Modul::getUserName() ?>
					<span></span>
					<ul class="subnav" style="right:0px;border-bottom-right-radius:4px;border-bottom-left-radius:4px;">
						<li style="padding:10px;text-align: right">
							<?= $l_hakakses ?>
							<input type="button" value="Ganti" onclick="changeRole()" style="margin-top:5px;">
						</li>
					</ul>
				</a>
			</li>
		</ul>
		<div class="DivImg" style="float:right;vertical-align:top">
			 &nbsp; <img src="images/unit.png">
			<?= $l_hakakses ?>
			<input type="button" value="Ganti" onclick="changeRole()"> &nbsp;
		</div>
		*/ ?>
		<img src="images/logosim2.png" style="vertical-align:top;float:right;position:relative;bottom:12px;">
	</div>
</div>
<? /*
<div class="Header">
	<? // &nbsp; <img src="images/logo.png" width="128"> &nbsp; ?>
	<img src="images/logosim.png" style="vertical-align:top">	
	<div class="SubHeader">
		<div style="float:left;width:210px">&nbsp;</div>
		<div class="DivButton" onclick="goView('home')" style="float:left;margin-left:3px;margin-top:-3px">
			<img src="images/home.png" width="16"> <br>Home
		</div>
		<div class="DivButton" onclick="goGuide()" style="float:left;margin-left:3px;margin-top:-3px">
			<img src="images/help.png" width="16"> <br>User Guide
		</div>
		<div class="DivButton" onclick="goTo('<?= $conf['menu_path'] ?>')" style="float:left;margin-left:3px;margin-top:-3px">
			<img src="images/menu.png" width="16"> <br>Menu SIM
		</div>
		<div class="DivButton" onclick="goView('logout')" style="float:left;margin-left:3px;margin-top:-3px">
			<img src="images/exit.png" width="16"> <br>Log Out
		</div>
	</div>
</div>
*/ ?> 
<script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
