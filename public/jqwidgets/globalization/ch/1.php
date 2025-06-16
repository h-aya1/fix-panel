<?php 
date_default_timezone_set("Asia/Jakarta");//penting agar di set time seperti ini
$from=isset($_POST['from']) ? ($_POST['from']):date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")-date("d")+1, date("Y")));
$to=isset($_POST['to']) ? $_POST['to'] : date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"), date("Y")));
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css" title="currentStyle">
			@import "Table_Tools/media/css/demo_page.css";
			@import "Table_Tools/media/css/demo_table_php.css";
			
			@import "Table_Tools/media/css/TableTools.css";
			<!--@import "Table_Tools/media/css/ColReorder.css";-->
			
			<!--@import "Table_Tools/media/css/ColVis.css";-->
			thead input { width: 100% }
			input.search_init { color: #999 }
			
			.bold{	
			/*font-size: 12px;*/
			 font-weight: bold;
			 }			
		</style>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="js/timepicker.js"></script>
<script type="text/javascript">
$(function() {
    $('#tanggal1,#from,#to,#from2,#to2').datepicker({
		duration: '',
		dateFormat:"yy-mm-dd",
		changeMonth: true,  
        changeYear: true ,
		time24h: true,
        showTime: false,
		showOn: "both",		 
        //maxDate: " +0D",
				 
        constrainInput: false
     });
});
</script>        
</head>

<body>
<div id="dt_example">
<div id="container">
<h1 style="width:480px"> <form id="form1" name="form1" method="post" action="">					
<label for="from">TGL :</label>
<input name="from" type="text" id="from" value="<?php echo $from ?>" size="15"/>
<label for="to">s/d</label>
<input name="to" type="text" id="to" value="<?php echo $to ?>" size="15"/>
<input type="submit" name="cari" id="cari" value="BACK UP" />
	</form></h1>
<!--					<h1>JADWAL PEMELIHARAAN 20KV APD </h1>-->
<div id="demo">
<fieldset style="width:320px" ><legend >excell eksport</legend >
<form id="form2" name="form2" method="post" action="">
<table  >
  <tr>
    <td>Tanggal:
    </td>
    <td>
      <div align="left">
        <input name='tanggal1' id="tanggal1" type='text' value=''  /></div>
    </td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>Wilayah:</td>
    <td><select name="wilayah1" id="wilayah1">
      <option value="BARAT">BARAT</option>
      <option value="TIMUR">TIMUR</option>
      <option value="TANGERANG">TANGERANG</option>
    </select></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>Periode</td>
    <td><input name="periode1" type="text" id="periode1" value="" size="5" /></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>Jenis Jadwal:</td>
    <td><select name="jenis_jadwal1" id="jenis_jadwal1">
      <option value="RUTIN">RUTIN</option>
      <option value="SUSULAN">SUSULAN</option>
      <option value="RUTIN/SUSULAN">RUTIN-SUSULAN</option>
    </select></td>
    <td><input type="image" name="ekspor"  id="ekspor" src="Table_Tools/media/images/xls_hover.png"  style=" padding-top:0px; padding-left:10px" title="Save to Excell"/></td>
    </tr>
</table>
</form>
</fieldset>
<br />
<h1 style="width:580px"> <form id="form2" name="form2" method="post" action="">					
<label for="from">TGL :</label>
<input name="from" type="text" id="from2" value="<?php echo $from ?>" size="15"/>
<label for="to">s/d</label>
<input name="to" type="text" id="to2" value="<?php echo $to ?>" size="15"/>
<input type="submit" name="cari" id="cari" value="BACK UP DATABASE" />
	</form></h1>
</div>
</div>
</div>

</body>
</html>