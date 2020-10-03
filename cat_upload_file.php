<?php 
	include("includes/funciones.php");
	$mysqlC=conexion_db();	
	
	$tx_p1	= $_GET['tx_p1']; //consultar
	$tx_p2	= $_GET['tx_p2']; //insertar
	$tx_p3	= $_GET['tx_p3']; //actualziar
	$tx_p4	= $_GET['tx_p4']; //borrar
	$tx_p5	= $_GET['tx_p5']; //exportar
	$tx_p6	= $_GET['tx_p6']; //UPLOADING
	$tx_p7	= $_GET['tx_p7']; 
	$tx_p8	= $_GET['tx_p8']; 
	$tx_p9	= $_GET['tx_p9']; 
	$tx_p10	= $_GET['tx_p10']; 

	//PERMISOS	
	if ($tx_p6=="1")
		$tx_habilitado="";
	else
		$tx_habilitado="disabled='disabled'";
		
	
	
	
	
	//Carga la informacion para combo anio
	//============================================
	$sql = "SELECT tx_anio from tbl_anio ORDER BY tx_anio " ; 	
	$result = mysqli_query($mysqlC, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnio[] = array(
			'id_anio'	=>$row["tx_anio"],
			'tx_anio'	=>$row["tx_anio"]
			);
	} 

mysqli_close($mysqlC);	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
           
<html>
<title> CSI Uploading file ...</title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: CSI: Bancomer Control Servicios Inform&aacute;ticos v2.0 ::.</title>
<!-- Estilos -->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui-personal/jquery-ui-1.7.2.custom.css"/> 
<!-- <link type="text/css" href="css/dark-hive/jquery-ui-1.7.3.custom.css" rel="stylesheet" />	-->
<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="css/estilos.css"/> 

<!-- Librerias -->
<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>



<script type="text/javascript">


	
function enviarPopUp(sDestinoURL)
{
window.open(sDestinoURL,"Ayuda","toolbar=no,menubar=no,location=no,directories=no,menubar=no,resizable=yes,scrollbars=yes,width=500,height=650");
}


function wait(){

	if(document.readyState=='loading')
			{
			document.getElementById("divWait").style.background='rgb(235,235,241)';
			document.getElementById("divWait").style.left=0;
			document.getElementById("divWait").style.top=0;
			document.getElementById("divWait").style.width=document.body.clientWidth+40;
			document.getElementById("divWait").style.height=document.body.clientHeight+40;
			document.getElementById("divWait").style.filter='alpha (opacity=70)';
			document.getElementById("divWait").style.visibility='visible';
			document.getElementById("divWait").style.zIndex=1;
			}
			//document.getElementById("divWait").style.border='dotted';
			//document.getElementById("divWait").style.borderColor='#000000';
}
function envia()
{
	
	document.forms[0].submit();
	wait();
}



function verificaRadio()
{
	
	var combo1=$("#divComboInf");
	var combo2=$("#divComboAnio");
	
	if (document.forms[0].tipofile[0].checked == true)
		{  
		combo1.show();  
		combo2.hide();  
		}

	if (document.forms[0].tipofile[1].checked == true)
		{  
		combo1.hide();  
		combo2.show();  
		}
	
}

$(document).ready(function() {

	var p1= $('#tx_p1').val();
	var p2= $('#tx_p2').val();
 	var p3= $('#tx_p3').val();
 	var p4= $('#tx_p4').val();
	var p5= $('#tx_p5').val();
	var p6= $('#tx_p6').val();
	var p7= $('#tx_p7').val();
	var p8= $('#tx_p8').val();
	var p9= $('#tx_p9').val();


	
	
	 $("#divComboAnio").hide();
	 $("#btnSubmit").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
	 $("#btnAyuda").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});

	//PERMISOS
	 if (p6==0)
		 $("#btnSubmit").addClass('ui-state-disabled').attr("disabled","disabled");
	 else
		 $("#btnSubmit").removeClass('ui-state-disabled').removeAttr("disabled");
		 
		  

		/*
		   $("#btnExaminar").click (   function()  
												{
												alert("emula");
												document.forms[0].userfile.click();
												document.forms[0].txtFakeText.value = document.forms[0].userfile.value;
												}
									);


*/
	/*		
		$("#btnSubmit").click ( 
						function()  
							
							{
							alert("emula2");

							document.getElementById("divWait").style.background='rgb(235,235,241)';
							document.getElementById("divWait").style.left=0;
							document.getElementById("divWait").style.top=0;
							document.getElementById("divWait").style.width=document.body.clientWidth+40;
							document.getElementById("divWait").style.height=document.body.clientHeight+40;
							document.getElementById("divWait").style.filter='alpha (opacity=70)';
							document.getElementById("divWait").style.visibility='visible';
							document.getElementById("divWait").style.zIndex=1;
							document.getElementById("myForm").submit();
							
							
							}	
				);
*/
   

			}
			);




			
</script>

	
</head>

<body>

		
		

<div id="divWait"  STYLE='position:absolute; top:250; left:300; width:90%;height:90%;visibility:hidden;' align="center">
	<table id="imgLoading" height="100%" border=0>
		<tr>
			<td valign="middle"><img src="images/MundoGif.gif" width="70px" height="70px" alt="Por favor espere"></td>
		</tr>
	</table>
</div>

<form id="myForm"   action="process_upload_file.php" method="post" enctype="multipart/form-data" >
		<input id="tx_p1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
        <input id="tx_p2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
        <input id="tx_p3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
        <input id="tx_p4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
        <input id="tx_p5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
        <input id="tx_p6" name="tx_p6" type="hidden" value="<? echo $tx_p6 ?>" />
        <input id="tx_p7" name="tx_p7" type="hidden" value="<? echo $tx_p7 ?>" />
        <input id="tx_p8" name="tx_p8" type="hidden" value="<? echo $tx_p8 ?>" />
        <input id="tx_p9" name="tx_p9" type="hidden" value="<? echo $tx_p9 ?>" />
        <input id="tx_p10" name="tx_p10" type="hidden" value="<? echo $tx_p10 ?>" />
        
   	<input type="hidden" name="dispatch" value="loadFile">
   <div id="divCarga" >
   
			<table border="0"  align="center" width="30%"><tr><td nowrap="nowrap" >
			<fieldset>
				<legend  > <STRONG>CARGA DE ARCHIVO</STRONG> </legend>
				<table border="0" cellspacing="1" cellpadding="2" align="center">
                <tr><td >&nbsp;</td></tr>
					<tr>
						<td > 
							<fieldset><legend >TIPO</legend> 
								<table>
								<tr><td  align="right"> &nbsp; 
                                
                                <input type="radio" name="tipofile" value="0" checked="checked" onclick="verificaRadio();" > </td><td >GPS</td>
                                
                                <td align="right">
								  
                                  <div  id="divComboInf" >
                                    <select name="tipoInf"  >
                                      <option value="M">Mensual</option>
                                      <option value="A">Acumulada</option>
                                    </select>
                                    </div>                                    
                                    </td></tr>
								<tr><td align="right"> &nbsp;
                                
                                <input type="radio"  name="tipofile" value="1" onclick="verificaRadio();"   > </td><td >ESSBASE</td>
                                
                                <td align="right">
                                   <div  id="divComboAnio" align="right" >
                                     &nbsp; &nbsp; 
                                     
                                     
                                     
                                  
                                     
                                     	<select name="anioInf" > 
                                     	<OPTION VALUE=0> -Seleccione- </OPTION>
                                     	
					           				<?								
											for ($i = 0; $i < count($TheCatalogoAnio); $i++)
											{	         			 
													$id_anio=$TheCatalogoAnio[$i]['id_anio'];		
													$tx_anio=$TheCatalogoAnio[$i]['tx_anio'];			  
													echo "<option value=$id_anio>$tx_anio</option>";
											}						 
											?>
					      				</select>
	            						
	            						
                                     
                                     
                                  </div>                                  </td></tr>
								</table> 
						  </fieldset>	 					</td> 
                    </tr>
					                <tr><td></td></tr>
                    <tr>
					  <td  align="center">
				      <input type="file" name="userfile" size="12"  <?php echo  $tx_habilitado ?>  >
				      </td>
				  </tr>
				  
                   <tr>
                   <td nowrap="nowrap">&nbsp;
	                 
                   </td></tr>
					<tr>
                    <td  align="center">
                     <a id="btnAyuda" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:enviarPopUp('html/help_load.htm')" style="font-size:smaller;" title="Ayuda" >
                Ayuda
                <span class="ui-icon ui-icon-info"></span>                </a>	
                
               <a id="btnSubmit" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left"  href="javascript:envia()" style="font-size:smaller;" title="Cargar" >
               	Cargar archivo
              	<span class="ui-icon ui-icon-script"></span>                </a>     
                        
                &nbsp;        
               	          </td></tr>
				</table>
			</fieldset>
			</td></tr></table>
	</div>
    

    
    
    
     
</form> 
</body>
</html>


