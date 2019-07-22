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
  <br>
  <table width="100%">
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>Jakarta, <?= CStr::formatDateInd($a_data['tglskpi'])?></td>

             </td>
      	  </tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td><em>Jakarta, <?= date('F jS, Y', strtotime($a_data['tglskpi']))?></em></td>
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
        	  <td><strong><u><?= $a_data['dekan']?></u></strong></td>
      	  </tr>
			<tr>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>&nbsp;</td>
        	  <td>
				  <strong>Dekan <?=$a_data['namafakultas']?><br></strong>
				  <em>Dean of <?=$a_data['namafakultas']?> </em>
        	  </td>
      	  </tr>
        	<tr>
        	  <td width="5%">&nbsp;</td>
        	  <td width="45%">&nbsp;</td>
        	  <td width="5%">&nbsp;</td>
        	  <td width="45%">
				  <strong>Nomor Induk Pegawai : <?= $a_data['nipdekan']?> </strong><br>
				  <em>Employee ID Number</em>
        	  </td>
   	      </tr>
   </table>
