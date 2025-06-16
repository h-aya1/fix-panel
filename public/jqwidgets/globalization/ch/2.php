<?php require_once('../../Connections/opsisdis.php'); ?>
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


/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.6, 2011-02-27
 */

/** Error reporting */
error_reporting(E_ALL);



/** PHPExcel */
require_once '../Classes/PHPExcel.php';

date_default_timezone_set("Asia/Jakarta");//penting agar di set time seperti ini
//echo date("d-m-Y H:i:s");

 






// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

 
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("");


// #Set properties
$objPHPExcel->getProperties()->setCreator("AGUS EKO")
							 ->setLastModifiedBy("AGUS EKO")
							 ->setTitle("Office 2007 XLSX Report")
							 ->setSubject("Office 2007 XLSX Report")
							 ->setDescription("Report for excell")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


switch($dayname)
{
case "Monday" : $hari = "SENIN";
             break;
case "Tuesday" : $hari = "SELASA";
             break;
case "Wednesday" : $hari = "RABU";
             break;
case "Thursday" : $hari = "KAMIS";
             break;
case "Friday" : $hari = "JUM'AT";
             break;
case "Saturday" : $hari = "SABTU";
             break;
case "Sunday" : $hari = "MINGGU";
             break;
}

 

$jadwal = mysql_query($query_jadwal, $opsisdis) or die(mysql_error());
$row_jadwal = mysql_fetch_assoc($jadwal);
$totalRows_jadwal = mysql_num_rows($jadwal);


$text='BACKUP JADWAL '.$from.'-'.$to.'';
$objPHPExcel->getActiveSheet()->setCellValue('A7', $text);

$text='APD:';
$objPHPExcel->getActiveSheet()->setCellValue('A9', $text);
$text='HARI:';
$objPHPExcel->getActiveSheet()->setCellValue('A10', $text);
$text='TGL :'.$from.'-'.$to.'';
$objPHPExcel->getActiveSheet()->setCellValue('A11', $text);
$text='PERIODE:';
$objPHPExcel->getActiveSheet()->setCellValue('A12', $text);
$text='BACKUP';
$objPHPExcel->getActiveSheet()->setCellValue('C12', $text);


$i='16';//baris data excell di mulai
do  {


$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row_jadwal['nomor']);//
$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row_jadwal['jam_pekerjaan']);//
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row_jadwal['pelaksana_pek']);//
$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row_jadwal['sifat_jenis_pek']);//
$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row_jadwal['penyulang']);//
$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row_jadwal['gi']);//
$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row_jadwal['gardu']);//
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row_jadwal['jtm']);//
$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row_jadwal['aj']);//
$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row_jadwal['keterangan']);//
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row_jadwal['jenis_jadwal']);//

# set border per Cell
$styleThinBlackBorderOutline = array(
	'borders' => array(
		'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => 'FF000000'),
		),
	),
);
//$objPHPExcel->getActiveSheet()->getStyle('A4:E10')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':A'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':E'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':F'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':G'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':H'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':I'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':J'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':K'.$i.'')->applyFromArray($styleThinBlackBorderOutline);
$i++;
} while ($row_jadwal = mysql_fetch_assoc($jadwal )); 







// Freeze panes
//$objPHPExcel->getActiveSheet()->freezePane('A12');




// Export to Excel2007 (.xlsx)
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// #Redirect output to a client's web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="BACKUP_Jadwal_Pemeliharaan_'.$from.'_'.$to.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');



