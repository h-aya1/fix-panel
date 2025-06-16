<?php require_once('Connections/opsisdis_mysqli.php'); ?>
<?php
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0); 
//session_name('agseko');
//session_set_cookie_params(2*7*24*60*60);
session_start();
  $_SESSION['hak_akses']     = 'usulan_jadwal';

 ?>
<?php
 if ($_SESSION['hak_akses'] !=''){
 if (preg_match('~\b(' . str_replace(',','|', $_SESSION['hak_akses']) . ')\b~', 'usulan_jadwal')) {
?><?php
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
$ID=$_GET['ID'];

$query_data = "SELECT
id,
id_pek,
file_ext,
file_basename,
file_name,
file_size,
file_type,
area,
tgl
FROM
jadwal_upload_area
WHERE ID='$ID'
";
$data = $mysqli_opsisdis->query($query_data) or die($mysqli_opsisdis->error.__LINE__);




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		
		<title>APD</title>
<!--pretyphoto-->
 <script src="js/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="prettyPhoto_compressed_3.1.5/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="prettyPhoto_compressed_3.1.5/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
<!--pretyphoto-->       
<!--UPPERCASE-->
<SCRIPT TYPE="text/javascript" src="js/uppercase.js"></SCRIPT>
<!--UPPERCASE-->
 <!--popup-->       
<script type="text/javascript" charset="utf-8" src="js/pop_up_window.js"></script>
 <!--popup-->
 <!--number only-->
 <script type="text/javascript" src="js/onlynumber.js"></script>
<!--number only-->       
<style type="text/css" title="currentStyle">
			@import "Table_Tools/media/css/demo_page.css";
			@import "Table_Tools/media/css/demo_table.css";
			
			@import "Table_Tools/media/css/TableTools.css";
			@import "Table_Tools/media/css/ColReorder.css";
			
			@import "Table_Tools/media/css/ColVis.css";
			thead input { width: 100% }
			input.search_init { color: #999 }
			
			.bold{	
			/*font-size: 12px;*/
			 font-weight: bold;
			 }			
		</style>
        <script type="text/javascript" charset="utf-8" src="js/pop_up_window_persist.js"></script>
        
        
		<!--<script type="text/javascript" language="javascript" src="Table_Tools/media/js/jquery.v1.6.1.js"></script>-->
		<script type="text/javascript" language="javascript" src="Table_Tools/media/js/jquery.dataTables.1.7.6.js"></script>
        <script type="text/javascript" language="javascript" src="Table_Tools/media/js/num-html.js"></script>
        
        <script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/ZeroClipboard.1.0.4-TableTools2.js"></script>
		<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/TableTools.2.0.1.js"></script>
		<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/ColReorder.1.0.1.js"></script>
        <!--<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/ColVis.1.0.5.js"></script>-->
        <!--<script type="text/javascript" charset="utf-8" src="Table_Tools/media/js/Scroller.1.0.0.js"></script>-->

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
			var oTable;//harus var
				
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
			
	

			
				  oTable = $('#example').dataTable( {
				<!--no urut ColReorder example with FixedColumns-->
					"fnDrawCallback": function ( oSettings ) {
					/* Need to redo the counters if filtered or sorted */
					if ( oSettings.bSorted || oSettings.bFiltered )
					{
					for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
					{
					$('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
					}
					}
					},	
					  
					
					
					
					"aaSorting": [[ 3, 'asc' ]],<!--kolom yg akan disort-->
					"oColReorder": {
			        "iFixedColumns": 1
		                           },
					<!--sembunyikan kolom-->
					"aoColumnDefs": [
						/*{ "bVisible": false, "aTargets": [ 2 ] },*/<!-- kolom 3 IP di Hide  ada masalah pada ekspor PDF-->
						<!--{ "bSortable": false, "aTargets": [ 0,2 ] },--><!-- kolom1 agar tidak bisa di sort ColReorder example with FixedColumns -->
						{ "bSortable": false, "sClass": "index", "aTargets": [ 0,1 ] },
						{ "sType": "numeric", "aTargets": [ 3 ] }
						<!--,{ "sSortDataType": "dom-checkbox","aTargets": [ 2 ] }-->
						
					],			   
					<!--ColReorder example with FixedColumns-->  
					
					<!--pager-->
			        "sPaginationType": "full_numbers",
					
					
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
					"oColVis": {
			         "buttonText": "Change columns",
					
		                       },
					
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
					"sScrollXInner": "2200px",<!--atur ini sesuaikan dg panjang tabel-->
					"bScrollCollapse": true, 
						
							
						/*aLengthMenu  */
					"iDisplayLength": -1,	
					"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					
					
					
					
				                                    } );<!--batas order var o table-->
													
				

													
//		    	<!--input filter pada tfoot-->
//				$("tfoot input").keyup( function () {
//					/* Filter on the column (the index) of this element */
//					oTable.fnFilter( this.value, $("tfoot input").index(this) );
//				} );
//				
//				
//				
//				
////				 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
////				 * the footer
//				 
//				$("tfoot input").each( function (i) {
//					asInitVals[i] = this.value;
//				} );
//				
//				$("tfoot input").focus( function () {
//					if ( this.className == "search_init" )
//					{
//						this.className = "";
//						this.value = "";
//					}
//				} );
//				
//				$("tfoot input").blur( function (i) {
//					if ( this.value == "" )
//					{
//						this.className = "search_init";
//						this.value = asInitVals[$("tfoot input").index(this)];
//					}
//				} );
//				<!--input filter pada tfoot-->
				
												  
	
				
			                              /*2*/} );/*1*/
							   
		</script>
        
</head>
<body ><!--id="dt_example"-->
        <div id="dt_example">
		<div id="container">
		<div id="demo">

    
<table>
<tr>
      <td >
      <!--** DAFTAR GANGGUAN PENYULANG  HARI INI **-->
      </td>
</tr>
</table>        
         
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" >
<thead>
        <tr>
            <th width="10">no</th>
            <th>ID pek</th>
            <th >DOCUMENT</th>
            <th >AREA</th>
            <th >TGL UPLOAD</th>
          </tr>
            <tr>
            <td align="right" ><input name="search_engine" type="text" disabled class="search_init" value="" /></td>
            <td ><input type="text" name="search_version" value="" class="search_init" /></td>
            <td ><input type="text" name="search_version" value="Search " class="search_init" /></td>
            <td ><input type="text" name="search_version" value="Search " class="search_init" /></td>            
            <td ><input type="text" name="search_version" value="Search " class="search_init" /></td>
          </tr>
</thead>
<tfoot>
        <tr>
            <th > </th>
            <th > </th>
            <th  align="right" > </th>
            <th  > </th>
            <th  > </th>
          </tr>
</tfoot>
	<tbody>
		
<?php while($row_data = mysqli_fetch_array($data))	 { ?>
		      <tr class="even_gradeX"><!--even_gradeC, odd_gradeA, odd_gradeX, gradeX, even_gradeX-->
              <script type="text/javascript" language="JavaScript">
function confirmAction(){
var confirmed = confirm("Are you sure? This will remove this entry forever.");
return confirmed;
}
</script>
              <script type="text/javascript" charset="utf-8">
			$(document).ready(function(){
				$("a[rel^='prettyPhoto']").prettyPhoto(<?php echo $i ?>);
			});
			</script>
		        <td align="right" >&nbsp;</td>
                
		        <td align="center" ><?php echo $row_data['id_pek']; ?></td>
                <?php  
$query_data2 = "SELECT *
FROM `jadwal_upload_area`
WHERE
jadwal_upload_area.id = '".$row_data['id']."' ORDER BY id DESC LIMIT 0,1 ";
$result = $mysqli_opsisdis->query($query_data2) or die($mysqli_opsisdis->error.__LINE__);
$row_data2 = mysqli_fetch_array($result)
?>                
		        <td ><a title="Download Document" href="../download_jadwal_area_document.php?file=<?php echo $row_data2['file_name']; ?>">
(<?php echo $row_data2['file_name']; ?>
                <?php echo number_format($row_data2['file_size']/1000000, 2, ",", "."); ?>.MB)
</a></td>
                <td ><?php echo $row_data['area']; ?></td>
                <td class="left"><?php echo $row_data['tgl']; ?></td>
              </tr>
		      <?php } ; ?>
    </tbody>
    
</table></div>
			</div>
		</div>
	</body>
</html>
<?php 
 } else echo 'anda tidak bisa mengakses menu ini';} ?>