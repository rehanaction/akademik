<?= str_repeat('<br />', 8)?>
<!--<div class="border" style="page-break-before:always">-->  
<div class="border">
	<table width="100%">
		<tr>
			<td width="30" style="vertical-align:top">
				<b>V.</b>
			</td>
			<td>
				<div class="content-title"><strong>KERANGKA KUALIFIKASI NASIONAL INDONESIA (KKNI)</strong></div>
				<div class="content-subtitle en">
					Indonesian Qualification Framework
				</div>
			</td>
		</tr>
	</table>
</div>
<table width="100%">
	<tr>
	  <td width="5%">&nbsp;</td>
	  <td width="45%">
		<?=$a_setting['infokkni']?>
	  </td>
	  <td width="5%">&nbsp;</td>
	  <td width="45%">
		  <em>
			<?=$a_setting['infokknien']?>
		  </em>
	  </td>
	</tr>
	<tr>
		<td colspan="4" align="center"><img src="<?=uForm::getPathImageSKPI($conn,'1')?>"</td>
	</tr>
</table>
  <br><br><br><br>
<div class="border">
	<table width="100%">
            	<tr>
                	<td width="30" style="vertical-align:top"><b>VI.</b></td>
                    <td>
                    	<div class="content-title"><strong>PENGESAHAN SKPI </strong></div>
                   	  <div class="content-subtitle en">SKPI Legalization</div>
                  </td>
                </tr>
		</table>
</div>
 
  <table width="100%">
          <tr>  <br>
          </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td> 
        	  <td>Jakarta,<?= date('d M Y', strtotime($a_data['tglskpi']))?></td>

             </td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td> 
        	  <td><em>Jakarta, </em> <?= date('F jS, Y', strtotime($a_data['tglskpi']))?></td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td style="border-top:1px dashed">&nbsp;</td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td><strong><u><?= $a_data['namaketuasementara']?></u></strong></td>
      	  </tr>
			<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>
				  <strong>Dekan <?=$a_data['namafakultas']?><br></strong>
				  <em>Dean of <?=$a_data['namafakultasen']?> </em>
        	  </td>
      	  </tr>
        	<tr>
        	  <td width="5%">&nbsp;</td>
        	  <td width="45%">&nbsp;</td>
        	  <td width="5%">&nbsp;</td>
        	  <td width="45%">
				  <strong>Nomor Induk Karyawan : <?= $a_data['nipketuasementara']?> </strong><br>
				  <em>Employee ID Number</em>  
          
			  
        	  </td>
   	      </tr>
   </table>



<div style="page-break-after:always"></div>

