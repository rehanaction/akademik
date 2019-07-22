<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// properti halaman
	$p_title = 'Daftar Barang (Tree)';
	$p_tbwidth = 700;
	$p_aktivitas = 'barang tree';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/menuedit.css" rel="stylesheet" type="text/css">
	<link href="style/jstree/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
						</div>
					</div>
				</header>
			</center>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0"align="center">
	            <tr valign="top">
		            <td nowrap width="50%">
			            <div id="treeold"></div>
		            </td>
		            <td nowrap width="50%">
			            <div id="treenew"></div>
		            </td>
	            </tr>
            </table>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.hotkeys.js"></script>
<script type="text/javascript" src="scripts/jquery.jstree.js"></script>
<script type="text/javascript">
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
    //barang lama
	$("#treeold").jstree({ 
		"json_data" : {
			"ajax" : {
				"url" : ajaxpage+"&f=treebarang",
				"data" : function (n) { 
					return { 
						id : n.attr ? n.attr("id") : 0 ,
						level : n.attr ? n.attr("level") : 1 ,
					}; 
				}
			}
		},
		"plugins" : ["themes","json_data","ui"],
		"themes": {
            "icons": false
        }
	})
	.bind("loaded.jstree", function (event, data) { 
	    //$('a').before('<img src="../images/deletesmall.gif" height="15" width="15" style="cursor:pointer">&nbsp;');
	})
	.bind("select_node.jstree", function (event, data) {
        //alert(data.rslt.obj.attr("id"));
        //doEdit(data.rslt.obj.attr("id"),data.rslt.obj.attr("namabarang"),'');
        //if(confirm('Hapus ?')){
        //    alert(data.rslt.obj.attr("id"));
            //ayoHapus(data.rslt.obj.attr("id"),data.rslt.obj.attr("level"));
        //}
    })
    .delegate("a", "click", function (event, data) { event.preventDefault(); });

/*
    //barang tmp
	$("#treenew").jstree({ 
		"json_data" : {
			"ajax" : {
				"url" : ajaxpage+"&f=treebarangtmp",
				"data" : function (n) { 
					return { 
						id : n.attr ? n.attr("id") : 0 ,
						level : n.attr ? n.attr("level") : 1 ,
					}; 
				}
			}
		},
		"plugins" : ["themes","json_data","ui"],
		"themes": {
            "icons": false
        }
	})
	.bind("select_node.jstree", function (event, data) {
        //if(confirm('Hapus ?')){
        //    alert(data.rslt.obj.attr("id"));
        //}
        //doEdit(data.rslt.obj.attr("id"),data.rslt.obj.attr("namabarang"),data.rslt.obj.attr("idbaru"));
    })
    .delegate("a", "click", function (event, data) { event.preventDefault(); });
*/
});

function ayoHapus(pidbarang,plevel){
    $.post('ajax.php?f=ayohapus', { idbarang1: pidbarang, level: plevel }, function(res){
	    if(res.err == '0') {
	        $('#treeold').jstree('refresh');
	    }else{
	    	alert('Gagal');
	    }
	},'json');
}
	
	function doEdit(idbarang1,namabarang,idbaru){
        getForm(idbarang1,namabarang,idbaru);
	}
	
    function getForm(idbarang1,namabarang,idbaru){
	    TINY.box.show({
	        url:'ajax.php',
	        post:'f=getbarangform',
	        width:550,
	        height:100,
	        openjs:function(){
	            $('#id').val(idbarang1);
	            $('#idbarang1').val(idbarang1);
	            $('#idbaru').val(idbaru);
	            $('#namabarang').val(namabarang);
	            $('#act').val('edit');
            },
	        closejs:function(){ }
        });
    }
	
	function doSimpan(){
        TINY.box.hide();
        //$('a').removeClass('jstree-clicked');
	    $.post('ajax.php?f=gosavebarangold', $("#BarangForm").serialize(), function(res){
		    if(res.err == '0') {
//		        $('#treeold').jstree.refresh(res.id);
//                $('#tree').jstree("select_node", -1);
		        $('#treeold').jstree('refresh');
//                $.jstree._reference("treeold").refresh(res.id);

		    }else{
		    	alert('ID barang baru tersebut tidak terdapat didatabase ');
		    }
		},'json');
	}
	
</script>
</body>
</html>
