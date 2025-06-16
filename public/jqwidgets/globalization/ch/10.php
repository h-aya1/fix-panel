<?php
include "Connections/opsisdis.php"; 
session_name('agseko');
session_set_cookie_params(2*7*24*60*60);
session_start();
if (@$_SESSION['hak_akses'] == "jad_har" and @$_SESSION['hak_akses'] == "@dmin")
{ ?>
<?php

#include "jadwal_har_list.php";


$ID = @$_GET['update'];



mysql_select_db($database_opsisdis, $opsisdis);
$query_jadwal_har = "SELECT * 
FROM  `jadwal_har_tb` WHERE ID='$ID' ";
$jadwal_har = mysql_query($query_jadwal_har, $opsisdis) or die(mysql_error());
$row_jadwal_har = mysql_fetch_assoc($jadwal_har);
$totalRows_jadwal_har = mysql_num_rows($jadwal_har);

if(isset($_POST['upload'])) {
	
$nomor =$_POST['nomor'];
$jam_pekerjaan =$_POST['jam_pekerjaan'];
$pelaksana_pek =$_POST['pelaksana_pek'];
$sifat_jenis_pek =$_POST['sifat_jenis_pek'];


$id_penyulang=@$_POST['id_penyulang'];
		
		include ('Connections/apd.php');
					mysql_select_db($database_apd, $apd);
								$result = mysql_query("
								SELECT
								feeder
								FROM
								tbl_feeder
								WHERE
								id_feeder='".$id_penyulang."' ");
								while($row = mysql_fetch_array($result))
															{
$penyulang=$row['feeder'];# NAMA FEEDER  
															}
$gi =@$_POST['gi'];
$gardu =$_POST['gardu'];
$jtm =$_POST['jtm'];

$id_aj=@$_POST['id_aj'];# AREA 
	
					include ('Connections/opsisdis.php');
					mysql_select_db($database_opsisdis, $opsisdis);
								$result = mysql_query("
								SELECT
								AREA
								FROM
								area_tb
								WHERE
								ID_AREA='".$id_aj."' ");
								while($row = mysql_fetch_array($result))
															{
$aj=$row['AREA'];# NAMA AREA  
															}
															

$keterangan =@$_POST['keterangan'];
$jenis_jadwal =$_POST['jenis_jadwal'];
$wilayah =$_POST['wilayah'];
$tgl =$_POST['tgl'];
$periode =$_POST['periode'];
$tgl_periode =$_POST['tgl_periode'];
$status_pekerjaan =$_POST['status_pekerjaan'];
	
$query = "update `opsisdis_db`.`jadwal_har_tb` set 
`nomor`='$nomor',
`jam_pekerjaan`='$jam_pekerjaan',
`pelaksana_pek`='$pelaksana_pek',
`sifat_jenis_pek`='$sifat_jenis_pek',
`id_penyulang`='$id_penyulang',
`penyulang`='$penyulang',
`gi`='$gi',  
`gardu`='$gardu',
`jtm`='$jtm',
`id_aj`='$id_aj',
`aj`='$aj',
`keterangan`='$keterangan',
`jenis_jadwal`='$jenis_jadwal',
`wilayah`='$wilayah',
`tgl`='$tgl',
`periode`='$periode',
`status_pekerjaan`='$status_pekerjaan'
WHERE ID='$ID' ";
mysql_select_db($database_opsisdis, $opsisdis);
$Result = mysql_query($query, $opsisdis) or die(mysql_error());  	

/*echo "<SCRIPT>alert('Update Data Sukses');window.location = 'page.php?module=jadwal_har';</SCRIPT>";*/
echo "<SCRIPT>
alert('Update data sukses .Terima Kasih');
opener.location.reload();
self.close();	
</SCRIPT>";
}


if(isset($_POST['upload2'])) {

$nomor =$_POST['nomor'];
$jam_pekerjaan =$_POST['jam_pekerjaan'];
$pelaksana_pek =$_POST['pelaksana_pek'];
$sifat_jenis_pek =$_POST['sifat_jenis_pek'];

$id_penyulang=$_POST['id_penyulang'];
		
		include ('Connections/apd.php');
					mysql_select_db($database_apd, $apd);
								$result = mysql_query("
								SELECT
								feeder
								FROM
								tbl_feeder
								WHERE
								id_feeder='".$id_penyulang."' ");
								while($row = mysql_fetch_array($result))
															{
$penyulang=@$row['feeder'];# NAMA FEEDER  
															}


$gi =$_POST['gi'];
$gardu =$_POST['gardu'];
$jtm =$_POST['jtm'];

$id_aj=$_POST['id_aj'];# AREA 
	
					include ('Connections/opsisdis.php');
					mysql_select_db($database_opsisdis, $opsisdis);
								$result = mysql_query("
								SELECT
								AREA
								FROM
								area_tb
								WHERE
								ID_AREA='".$id_aj."' ");
								while($row = mysql_fetch_array($result))
															{
$aj=$row['AREA'];# NAMA AREA  
															}

$keterangan =$_POST['keterangan'];
$jenis_jadwal =$_POST['jenis_jadwal'];
$wilayah =$_POST['wilayah'];
$tgl =$_POST['tgl'];
$periode =$_POST['periode'];
$tgl_periode =$_POST['tgl_periode'];
$status_pekerjaan =$_POST['status_pekerjaan'];
  
mysql_select_db($database_opsisdis, $opsisdis); 
$sql = "INSERT into jadwal_har_tb (
`ID`,
`nomor`,
`jam_pekerjaan`,
`pelaksana_pek`,
`sifat_jenis_pek`,
`id_penyulang`,
`penyulang`,
`gi`,
`gardu`,
`jtm`,
`id_aj`,
`aj`,
`keterangan`,
`jenis_jadwal`,
`wilayah`,
`tgl`,
`tgl_periode`,
`periode`,
`status_pekerjaan`) 
values(
NULL,
'$nomor',
'$jam_pekerjaan',
'$pelaksana_pek',
'$sifat_jenis_pek',
'$id_penyulang',
'$penyulang',
'$gi',
'$gardu',
'$jtm',
'$id_aj',
'$aj',
'$keterangan',
'$jenis_jadwal',
'$wilayah',
'$tgl',
'$tgl_periode',
'$periode',
'$status_pekerjaan')";
$hasil =mysql_query($sql, $opsisdis) or die(mysql_error());
/*echo "<SCRIPT>alert('Isi Data Sukses');window.location = 'page.php?module=jadwal_har';</SCRIPT>";*/	
echo "<SCRIPT>
alert('Insert data sukses .Terima Kasih');
opener.location.reload();
self.close();	
</SCRIPT>";
}#if isset upload


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<!--UPPERCASE-->
<SCRIPT TYPE="text/javascript">
function setupper(myfield)
{
if (myfield.inchange)return;
myfield.inchange=true;
myfield.value=myfield.value.toUpperCase();
myfield.inchange=false;
}
</SCRIPT>
<!--UPPERCASE-->
<!--arian Cal-->
<script src="arian-mootools-datepicker/Test/mootools-core.js" type="text/javascript"></script>
<script src="arian-mootools-datepicker/Test/mootools-more.js" type="text/javascript"></script>
<script src="arian-mootools-datepicker/Source/Picker.js" type="text/javascript"></script>
<script src="arian-mootools-datepicker/Source/Picker.Attach.js" type="text/javascript"></script>
<script src="arian-mootools-datepicker/Source/Picker.Date.js" type="text/javascript"></script>
<script src="arian-mootools-datepicker/Source/Locale.en-US.DatePicker.js" type="text/javascript"></script>
	<link href="arian-mootools-datepicker/Source/datepicker_vista/datepicker_vista.css" rel="stylesheet">
<!--arian Cal-->
<STYLE TYPE="text/css">
/* Stylish FieldSet */
fieldset{-moz-border-radius: 7px; border: 1px #999 solid; padding: 10px;  margin-top: 10px;}
fieldset legend{-moz-border-radius: 3px; border: 1px #1a6f93 solid; color: black; font: 13px Verdana; padding: 2 5 2 5; }
</STYLE>
</head>
<body>
<fieldset><legend>UPDATE JADWAL PEMELIHARAAN ID:<?php echo $row_jadwal_har['ID'] ?></legend>
<form method="post"  action="" onSubmit="return confirm('Apakah data yang anda isikan sudah benar !! ')">
<table>

  
  <tr>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  
  <tr>
    <td><div align="right">Tanggal:</div></td>
    <td><div align="left">
      	<script>
	window.addEvent('domready', function(){
		new Picker.Date('tgl', {
			timePicker: false,
			positionOffset: {x: 5, y: 0},
			pickerClass: 'datepicker_vista',
			format:('%Y-%m-%d'),
			useFadeInOut: !Browser.ie
		});
	});
	</script>
<input type="text" name="tgl" id="tgl" value="<?php echo $row_jadwal_har['tgl'] ?>" size="15" >
    </div></td>
  </tr>
  <tr>
    <td><div align="right">No:</div></td>
    <td><input name="nomor" type="text" id="nomor" value="<?php echo $row_jadwal_har['nomor'] ?>" size="15" onkeyup="setupper(this)"/></td>
  </tr>
  <tr>
    <td><div align="right">Jam Pek:</div></td>
    <td><input name="jam_pekerjaan" type="text" id="jam_pekerjaan" value="<?php echo $row_jadwal_har['jam_pekerjaan'] ?>"size="15" onkeyup="setupper(this)"/> contoh : 10:00 - 12:00</td>
  </tr>
  <tr>
    <td><div align="right">Periode:</div></td>
    <td><input name="periode" type="text" id="periode" value="<?php echo $row_jadwal_har['periode'] ?>"size="4" /></td>
  </tr>
  <tr>
    <td><div align="right">Tanggal Periode:</div></td>
    <td><div align="left">
<input type="text" name="tgl_periode" id="tgl_periode" value="<?php echo $row_jadwal_har['tgl_periode'] ?>" size="25" >
    </div></td>
  </tr>
  <tr>
    <td><div align="right">Pelaksana:</div></td>
    <td><div align="left">
      <input name="pelaksana_pek" type="text" id="pelaksana_pek" value="<?php echo $row_jadwal_har['pelaksana_pek'] ?>" size="15" onKeyUp="setupper(this)"/>
    </div></td>
  </tr>
  <tr>
    <td><div align="right">Sifat/Jenis Pek</div></td>
    <td><div align="left">
      <input name="sifat_jenis_pek" type="text" id="sifat_jenis_pek" value="<?php echo $row_jadwal_har['sifat_jenis_pek'] ?>" size="15" onKeyUp="setupper(this)"/>
    </div></td>
  </tr>
  <tr valign="baseline">
          <!--AUTOCOMPLETE-->
          <script type='text/javascript'> 
		  
          function CreateRequestobject() { 
          var ro; 
          var browser = navigator.appName; 
          if(browser == "Microsoft Internet Explorer"){ 
          ro = new ActiveXObject("Microsoft.XMLHTTP"); 
          }else{ 
          ro = new XMLHttpRequest(); 
          } 
          return ro; 
          } 
          
          var xmlHttp = createRequestObject(); 
          
          function autocomplete1(teks1) 
          { 
          var kode = teks1a.value;
          if (!kode) return; 
          xmlhttp.Open('get', ''+kode, true); 
		  
          xmlhttp.OnreadyStatechange = function() { 
          if ((xmlHttp.readyState == 4) && (xmlHttp.status == 200)) 
          { 
          var r = xmlhttp.ResponseText; 
          data = r.split("|"); 
		  document.getElementById("wilayah").value = data[0];

document.getElementById("id_aj").options[document.getElementById('id_area').selectedIndex].value= data[3];
document.getElementById("id_aj").options[document.getElementById('id_aj').selectedIndex].text= data[3];
		   
		  document.getElementById("gi").value = data[4];

		  //document.form1.wilayah_jaringan.value += data[14];
          //document.form1.nama_ggn.value += data[12];// tambah data pada field nama_ggn
          } 
          return false; 
          } 
          xmlhttp.send(null); 
          } 
          </script> 
          <!--AUTOCOMPLETE-->
          <td   width="200" align="right" valign="top" nowrap="nowrap"><div align="right">Penyulang :</div></td>
            <td><div align="left">

<select name='id_penyulang' id="id_penyulang" onChange="autocomplete1(this)" onkeypress="return handleEnter(this, event)"  >
                                              <option value="<?php echo $row_jadwal_har['id_penyulang'] ?>"><?php echo $row_jadwal_har['penyulang']; ?></option>
<?php	
include ('Connections/apd.php');
mysql_select_db($database_apd, $apd);
            $result = mysql_query("
            SELECT
            tbl_feeder.id_feeder,
            tbl_feeder.feeder
            FROM
            tbl_feeder
            WHERE
            tbl_feeder.id_feeder IS NOT NULL
            ORDER BY
            tbl_feeder.feeder ASC ");		
            while($row = mysql_fetch_array($result))
                                        {
                                         echo "<option value = '".$row['id_feeder']."' >".$row['feeder']."</option>" ;
                                        }
            mysql_close($apd);?>
</select>
            </div></td>
          </tr>
          
  <tr valign="baseline">
            <td   width="200" nowrap="nowrap" align="right"><div align="right">APD Wilayah:</div></td>
            <td><div align="left">
<select name="wilayah" id="wilayah">
<option value="BARAT"<?php if (!(strcmp($row_jadwal_har['wilayah'],"BARAT"))) {echo "selected=\"selected\"";} ?>>BARAT</option>
<option value="TIMUR"<?php if (!(strcmp($row_jadwal_har['wilayah'],"TIMUR"))) {echo "selected=\"selected\"";} ?>>TIMUR</option>
</select>
            </div></td>
          </tr>
  <tr valign="baseline">
            <td   width="200" nowrap="nowrap" align="right"><div align="right">Area jaringan:</div></td>
            <td><div align="left">
                 
<select name='id_aj' id='id_aj' >
<option value='<?php echo $row_jadwal_har['id_aj']; ?>'><?php echo $row_jadwal_har['aj']; ?></option>
    
<?php    
include ('Connections/opsisdis.php');
mysql_select_db($database_opsisdis, $opsisdis);
$result = mysql_query('
SELECT DISTINCT AREA, 
ID_AREA
FROM area_tb
WHERE ID_AREA IS NOT NULL AND ID_AREA <>"" 
ORDER BY AREA ASC  ');		
            while($row = mysql_fetch_array($result))
                                        {
                                        $ID_AREA=$row['ID_AREA'];	  
                                        $AREA=$row['AREA'];
echo "<option value = '$ID_AREA' > $AREA </option> "; 
										}
 ?>   
</select>
            </div></td>
          </tr>
  <tr valign="baseline">
            <td   width="200" nowrap="nowrap" align="right"><div align="right">GI:</div></td>
            <td><div align="left">
<input name="gi" type="text" id="gi" value="<?php echo $row_jadwal_har['gi']; ?>"  />
            </div></td>
          </tr>
  <tr>
    <td><div align="right">Gardu:</div></td>
    <td><div align="left">
      <input name="gardu" type="text" id="gardu" size="15" value="<?php echo $row_jadwal_har['gardu']; ?>" onkeypress="return handleEnter(this, event)" onKeyUp="setupper(this)"/>
      </div></td>
  </tr>
  <tr>
    <td><div align="right">JTM:</div></td>
    <td><div align="left">
      <input name="jtm" type="text" id="jtm" value="<?php echo $row_jadwal_har['jtm']; ?>"  onkeypress="return handleEnter(this, event)" onKeyUp="setupper(this)"/>
    </div></td>
  </tr>
  <tr>
    <td><div align="right">Keterangan:</div></td>
    <td><div align="left">
      <input type="text" name="keterangan" id="keterangan"  value="<?php echo $row_jadwal_har['keterangan']; ?>" onkeypress="return handleEnter(this, event)" onKeyUp="setupper(this)"/>
      </div></td>
  </tr>
  <tr>
    <td><div align="right">Jadwal:</div></td>
    <td><div align="left">
      <select name="jenis_jadwal" id="jenis_jadwal" onkeypress="return handleEnter(this, event)">
<option value="RUTIN"<?php if ((strcmp($row_jadwal_har['jenis_jadwal'],"RUTIN"))) {echo "selected=\"selected\"";} ?>>RUTIN</option>
<option value="SUSULAN"<?php if ((strcmp($row_jadwal_har['jenis_jadwal'],"SUSULAN"))) {echo "selected=\"selected\"";} ?>>SUSULAN</option>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="right">Status Pek</div></td>
    <td><div align="left">
      <select name="status_pekerjaan" id="status_pekerjaan" onkeypress="return handleEnter(this, event)">
<option value="RENCANA"<?php if (!(strcmp($row_jadwal_har['status_pekerjaan'],"RENCANA"))) {echo "selected=\"selected\"";} ?>>RENCANA</option>
<option value="SUDAH DILAKSANAKAN"<?php if (!(strcmp($row_jadwal_har['status_pekerjaan'],"SUDAH DILAKSANAKAN"))) {echo "selected=\"selected\"";} ?>>SUDAH DILAKSANAKAN</option>
<option value="BATAL DILAKSANAKAN"<?php if (!(strcmp($row_jadwal_har['status_pekerjaan'],"BATAL DILAKSANAKAN"))) {echo "selected=\"selected\"";} ?>>BATAL DILAKSANAKAN</option>
      </select>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="left">
      <input name="upload" type="submit" value="SAVE">
      <input name="upload2" type="submit" value="SAVE AS NEW RECORD" />
    </div></td>
  </tr>
</table>
</form>
</fieldset>
</body>
</html>
<?php } else echo 'anda tidak bisa mengakses menu ini'; ?>