<?php 
ob_start();
require_once('Connections/opsisdis_mysqli.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
$id_pek=$_GET['delete'];

$query   = "SELECT file_name FROM jadwal_upload_area WHERE id_pek = '$id_pek'";
$hasil   = $mysqli_opsisdis->query($query) or die($mysqli_opsisdis->error.__LINE__);
 
	

	
while($row_data = mysqli_fetch_array($hasil))	 {
	
$filename = $row_data['file_name'];
$area = $row_data['area'];
$file="../usulan_jadwal/".$filename;		
if( @filesize($file)) {
#hapus file pada folder
	         unlink("../usulan_jadwal/" . $filename);
	#echo "File telah dihapus";
						}


	# dilanjut hapus data pada tabel 

$deleteSQL2 ="DELETE FROM jadwal_upload_area WHERE area='$area'";
$result2 = $mysqli_opsisdis->query($deleteSQL2) or die($mysqli_opsisdis->error.__LINE__);
                                                  }
$deleteSQL ="DELETE FROM jadwal_har_tb WHERE area='$area'";
$result1 = $mysqli_opsisdis->query($deleteSQL) or die($mysqli_opsisdis->error.__LINE__);

echo "<SCRIPT>alert('FILE  Sukses Ter-Hapus .Terima Kasih');history.go(-1);</SCRIPT>";


 // header("Location:page.php?module=sukses");
ob_flush();
?>
