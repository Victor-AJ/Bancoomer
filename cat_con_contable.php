<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
 session_start();
if 	(!isset($_SESSION["sess_user"]))   
	 echo "Sessi&oacute;n Invalida";

else
{	
	include("includes/funciones.php");  
	$mysql=conexion_db();

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
	
	$urlpermisos='tx_p1='.$tx_p1.'&tx_p2='.$tx_p2.'&tx_p3='.$tx_p3.'&tx_p4='.$tx_p4.'&tx_p5='.$tx_p5."&tx_p6=".$tx_p6."&tx_p7=".$tx_p7."&tx_p8=".$tx_p8."&tx_p9=".$tx_p9."&tx_p10=".$tx_p10;
					
	
	//Carga la informacion para combo archivos GPS
	//============================================
	$sql = "SELECT id_archivo, tx_archivo FROM tbl40_archivos_upload where in_tipo=0 and tx_status='OK' ORDER BY tx_archivo " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoGPS[] = array(
			'id_archivo'	=>$row["id_archivo"],
			'tx_archivo'	=>$row["tx_archivo"]
			);
	} 

	//	<CUENTASCONT: se agrega LISTADO  DE CUENTA  > 
	//Catalogo de cuentas contables
	//===========================
	$sql = " select id as id_cuenta, concat(tx_valor ,' : ' , tx_observaciones ) as tx_cuenta from tbl45_catalogo_global ";
	$sql.= " where substr(tx_clave,1,15)=  'CUENTA_CONTABLE'   and tx_indicador=1 ";
	
	//echo " sql ",$sql;
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogoCuenta[] = array( 'id_cuenta'=>$row["id_cuenta"], 'tx_cuenta'=>$row["tx_cuenta"] );
	}
	
	
	
	//Carga la informacion para combo archivos ESB
	//============================================
	$sql = "SELECT id_archivo, tx_archivo FROM tbl40_archivos_upload where in_tipo=1 and tx_status='OK' ORDER BY tx_archivo " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoESSBASE[] = array(
			'id_archivo'	=>$row["id_archivo"],
			'tx_archivo'	=>$row["tx_archivo"]
			);
	} 

	
	$sql = "SELECT tx_anio from tbl_anio where tx_indicador='1' ORDER BY tx_anio " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoAnio[] = array(
			'id_anio'	=>$row["tx_anio"],
			'tx_anio'	=>$row["tx_anio"]
			);
	} 
	

	$sql = " select id_factura_estatus, tx_estatus  from tbl_factura_estatus where tx_indicador='1' " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoStatusFac[] = array(
			'id_factura_estatus'	=>$row["id_factura_estatus"],
			'tx_estatus'	=>$row["tx_estatus"]
			);
	}
	
	
	$sql = " select id_proveedor, tx_proveedor_corto from tbl_proveedor where tx_indicador='1' " ; 	
	$result = mysqli_query($mysql, $sql);		
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoProveedor[] = array(
			'id_proveedor'			=>$row["id_proveedor"],
			'tx_proveedor'	=>$row["tx_proveedor_corto"]
			);
	}
	
	
?>

<script type="text/javascript">


$(document).ready(function() {


							//PERMISOS
							var p1= $('#tx_p1').val();
							var p2= $('#tx_p2').val();
							var p3= $('#tx_p3').val();
							var p4= $('#tx_p4').val();
							var p5= $('#tx_p5').val();
							var p6= $('#tx_p6').val();
							var p7= $('#tx_p7').val();
							var p8= $('#tx_p8').val();
							var p9= $('#tx_p9').val();
	
	

							$("#mytabs").tabs();	
							
							

							//PESTAÑA CONCILIACION
							$("#btnBuscar").hover(	function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");} );
							$("#btnLimpia").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
							//PERMISOS
							 if (p1==0)
							 $("#btnBuscar").addClass('ui-state-disabled').attr("disabled","disabled");
							 else
							 $("#btnBuscar").removeClass('ui-state-disabled').removeAttr("disabled");
							 
							
							
							//PESTAÑA FACTURA
							$("#btnBuscaFactura").hover(	function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");} );
							$("#btnLimpiaFac").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
							//PERMISOS
							 if (p1==0)
							 $("#btnBuscaFactura").addClass('ui-state-disabled').attr("disabled","disabled");
							 else
							 $("#btnBuscaFactura").removeClass('ui-state-disabled').removeAttr("disabled");
							
														
//							$("#btnCargar").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
//							$("#btnAyuda").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
							
							//PESTAÑA REPORTE
							$("#btnBuscaReporte").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
							//PERMISOS
							 if (p1==0)
							 $("#btnBuscaReporte").addClass('ui-state-disabled').attr("disabled","disabled");
							 else
							 $("#btnBuscaReporte").removeClass('ui-state-disabled').removeAttr("disabled");


					
		 
		  
		  
							
		
							var funcPostAjaxLoad = function(data)
													{					   			
													if(data.error == true)
															jAlert(true,true,data.message,function() {	$('#dialogMain').dialog("close"); 	});
													else 
															{						
															if(data.message != null)
																jAlert(true,false,data.message,function() {	$('#dialogMain').dialog("close"); 	});
															}	
													};

							$("#btnBuscaReporte").click (  function()  
									{
								 myargs=$("#frmUpload").serialize();
								url1="inf_reporte_seguimiento.php?"+myargs;

								$("#divListadoFacturas").html("");
								
								loadHtmlAjax(true, $("#divResultsetReporte") , url1);
								}
							);
													
						
							$( "#mytabs" ).bind( "tabsshow", function(event, ui) {
								  var selected = $( "#mytabs" ).tabs( "option", "selected" );
								  
								  if (selected ==1 )
								  { 
									  //RECARGAR COMBOS theEssbaseQueryFile, theGpsQueryFile,  theGPSQueryFileFac
										//tomar seleccion actual
										
										recargaCombosP1();

									  /*
									  var selectedGps=$("#theGpsQueryFile").val();
										var selectedEsb=$("#theEssbaseQueryFile").val();
										$("#divtheGpsQueryFile").html("");
										$("#divtheEssbaseQueryFile").html("");
										loadHtmlAjax(true, $("#divtheGpsQueryFile"), "combo_GPS.php?id="+selectedGps+"&in_anio=" + document.forms[0].anoQueryInf.value);
										loadHtmlAjax(true, $("#divtheEssbaseQueryFile"), "combo_ESB.php?id="+selectedEsb+"&in_anio=" + document.forms[0].anoQueryInf.value);
										*/
								   }
								  if ( selected ==2)
								  { 
									  //tomar seleccion actual
									  
									  recargaCombosP2();
									  /*
									  var selectedGpsFac=$("#theGPSQueryFileFac").val();
									  $("#divtheGPSQueryFileFac").html("");
									  loadHtmlAjax(true, $("#divtheGPSQueryFileFac"), "combo_GPSFac.php?id="+selectedGpsFac+"&in_anio=" + document.forms[0].anoQueryFac.value);
									  */
								   }
								});
							
							
							
									
							$("#btnBuscaFactura").click (  function()  
															{
								
															
															var url="";
															var postfijo="none";
															container=$("#divResultsetFacturas");
															
															if (!validSelect($("#theGPSQueryFileFac"), $("#errcaptura")))
																return false;
															
															    myargs=$("#frmUpload").serialize();
																url="inf_conciliacion_factura.php?"+myargs;
																
																loadHtmlAjax(true, container, url);
																$("#divResultsetFacturasCRDerrama").html("");
																$("#divResultsetFacturasCR").html("");
															}
														);
							
						    $("#btnLimpia").click (  function()  
															{
													    	$("#divDireccionConcGPS").html("");
															$("#divDireccionConcESB").html("");
															$("#divSubDireccionConcGPS").html("");
															$("#divSubDireccionConcESB").html("");
															$("#divDepartamentoConcGPS").html("");
															$("#divDepartamentoConcESB").html("");
															$("#divFacturasCSI").html("");
															$("#divFacturasGPS").html("");
															$("#theEssbaseQueryFile").val(0);
															$("#theGpsQueryFile").val(0);
															$("#tipoCuenta").val(0);
															$("#rango").val('M');
															$("#mesQueryInf").val(1);
															
															}
        											);
													
								 $("#btnLimpiaFac").click (  function()  
															{
													    	$("#divResultsetFacturas").html("");
															$("#divResultsetFacturasCR").html("");
															$("#divResultsetFacturasCRDerrama").html("");
															
															
		   
															$("#theGPSQueryFileFac").val(0);
															$("#estatusFac").val(0);
															$("#proveedorFac").val(0);
															$("#theFacturaCSI").val('');
															$("#theFacturaGPS").val('');
															$("#mesQueryFac").val(1);
															}
        												);
													

						    $("#btnBuscar").click (  function()  
														{
														//	valida filtros
														if  ($("#theGpsQueryFile").val()==0 )
															{
															jAlert(true,false,"Seleccione un archivo GPS",function(){$('#dialogMain').dialog("close");});
															return;
															}
														if  ($("#theEssbaseQueryFile").val()==0 )
															{
															jAlert(true,false,"Seleccione un archivo ESSBASE",function(){$('#dialogMain').dialog("close");});
															return;
															}
														var url1="";
														var postfijo="none";
														
														postfijo="direccion";
														container1=$("#divDireccionConcGPS");
														container2=$("#divDireccionConcESB");
														
														/*
														else if (document.forms[0].tipoNivel.value==1) 
															  {	 postfijo="subdireccion";   container1=$("#divSubDireccionConcGPS");   container2=$("#divSubDireccionConcESB"); }
															else if (document.forms[0].tipoNivel.value==2)
																{	postfijo="departamento";	container1=$("#divDepartamentoConcGPS"); container2=$("#divDepartamentoConcESB"); }
															*/
															
														myargs=$("#frmUpload").serialize();
														url1="inf_conciliacion_"+postfijo+"_GPS.php?"+myargs;
														url2="inf_conciliacion_"+postfijo+"_ESB.php?"+myargs;
														
														$("#divSubDireccionConcGPS").html("");
														$("#divSubDireccionConcESB").html("");
														$("#divDepartamentoConcGPS").html("");
														$("#divDepartamentoConcESB").html("");
														$("#divFacturasCSI").html("");
	
														$("#divFacturasGPS").html("");


														
														loadHtmlAjax(true, container1, url1);
										   				loadHtmlAjax(true, container2, url2);		
														}
												);


						}
					);	


			function recargaCombosP1()
			{
			var selectedGps=$("#theGpsQueryFile").val();
			var selectedEsb=$("#theEssbaseQueryFile").val();
			$("#divtheGpsQueryFile").html("");
			$("#divtheEssbaseQueryFile").html("");
			loadHtmlAjax(true, $("#divtheGpsQueryFile"), "combo_GPS.php?id="+selectedGps+"&in_anio=" + document.forms[0].anoQueryInf.value);
			loadHtmlAjax(true, $("#divtheEssbaseQueryFile"), "combo_ESB.php?id="+selectedEsb+"&in_anio=" + document.forms[0].anoQueryInf.value);
			}

			function recargaCombosP2()
			{
			  var selectedGpsFac=$("#theGPSQueryFileFac").val();
			  $("#divtheGPSQueryFileFac").html("");
			  loadHtmlAjax(true, $("#divtheGPSQueryFileFac"), "combo_GPSFac.php?id="+selectedGpsFac+"&in_anio=" + document.forms[0].anoQueryFac.value);
			}

			  

		function verificaRadio()
		{
			if (document.frmUpload.tipofile[0].checked == true)
					{  $("#divComboInf").show();  $("#divComboAnio").hide();  }
			
			if (document.frmUpload.tipofile[1].checked == true)
					{  $("#divComboInf").hide();  $("#divComboAnio").show();  }
			
		}      

//FUNCIONES DE PESTAÑA 2
		function btnVerDetalle (id, idPadre, verNivel,  showInContainer, tipo)
		{
			var url="";
            //alert(" btnVerDetalle() verNIvel:(" + verNivel + ") idPadre:(" + idPadre+ ") showincontainer: (" + showInContainer + ")");

           url="inf_conciliacion_"+ verNivel +"_"+ tipo +".php?";
		   url += $("#frmUpload").serialize();
		   url += "&id"+idPadre+"="+id;
		//   alert(url);			
			if (verNivel=="subdireccion")
					{
					$("#divDepartamentoConcGPS").html("");
					$("#divFacturasCSI").html("");
					$("#divFacturasGPS").html("");
					}
					
		   loadHtmlAjax(true, $(showInContainer), url);
		}

		function btnVerDetalleGE (id1, id2, idPadre1, idPadre2, verNivel,  showInContainer)
		{
		   var url="";
           //alert("btnVerDetalleGE() verNIvel:(" + verNivel + ") idPadre1:(" + idPadre1+  ") idPadre2:(" + idPadre2+ ")  showincontainer: (" + showInContainer + ")  ID1:(" + id1 + ")  ID2:(" + id2 + ")");

           url="inf_conciliacion_"+ verNivel +"_ESB.php?";
		   url += $("#frmUpload").serialize();
		   url += "&id"+idPadre1+"="+id1;
		   url += "&id"+idPadre2+"="+id2;
		//   alert(url);			
			if (verNivel=="subdireccion")
					$("#divDepartamentoConcESB").html("");
					
		   loadHtmlAjax(true, $(showInContainer), url);
		}      


//FUNCIONES DE PESTAÑA 3
		function  btnVerDetalleFacCR(txFactura)
		{
				var url="";
				
				FiltroAdicFac=txFactura;
				url="inf_conciliacion_facturaCR.php?";
				url += $("#frmUpload").serialize();
				url +=  "&FiltroAdicFac="+ FiltroAdicFac;
			   loadHtmlAjax(true, $("#divResultsetFacturasCR"), url);
				$("#divResultsetFacturasCRDerrama").html("");
			   
		}
		
		function  btnVerDetalleFacCRporGPS (txFacturaGPS)
		{
		
			var url="";
				
				//document.forms[0].theFacturaGPS.value=txFacturaGPS;
				FiltroAdicFacGPS=txFacturaGPS;
				url="inf_conciliacion_facturaCR_GPS.php?";
				url += $("#frmUpload").serialize();
				url +=  "&FiltroAdicFacGPS="+ FiltroAdicFacGPS;
			   loadHtmlAjax(true, $("#divResultsetFacturasCR"), url);
				$("#divResultsetFacturasCRDerrama").html("");
		
		}
			

		function  btnVerDetalleFacCRDerrama(id)
		{
				var url="";
				url="inf_facturas_lista_detalle.php?id="+id;
			    loadHtmlAjax(true, $("#divResultsetFacturasCRDerrama"), url);
		}
		
		
		function btnVerFacturasCSI (idCR)
		{
		
		var url="";

           url="inf_conciliacion_factura_CSI.php?";
		   url += $("#frmUpload").serialize();
		   url += "&tx_CR="+idCR;
		 //  alert(url);			
		
		   loadHtmlAjax(true, $("#divFacturasCSI"), url);
		 }
		   
		   
		function btnVerFacturasGPS (idCR)
		{
		
		var url="";

           url="inf_conciliacion_factura_GPS.php?";
		   url += $("#frmUpload").serialize();
		   url += "&tx_CR="+idCR;
		 //  alert(url);			
		
		   loadHtmlAjax(true, $("#divFacturasGPS"), url);
		   
		}
		

		function btnVerFacturasListado (idMes, idStatus, idAnio)
		{
		
		var url="";

           url="inf_seguimiento_listado.php?";
		   url += "&id_mes="+idMes  +"&id_status="+idStatus  + "&id_anio="+idAnio;
		
		   loadHtmlAjax(true, $("#divListadoFacturas"), url);
		   
		}

		var func = function(data)
		{
			if(data.error == true)
				jAlert(true,false,"Error al procesar:" + data.message, function(){$('#dialogMain').dialog("close");	} );
			else	
				jAlert(true,false,"Comentario Almacenado Exitosamente", function(){$('#dialogMain').dialog("close");} );
		};
		
		function saveComent (obj,idDir, modulo)
			{
			url="process_comentario.php?";
			url += $("#frmUpload").serialize();
			url += "&comentario="+obj.value +  "&idDireccion=" + idDir +  "&modulo=" + modulo ;
			

			executeAjax("post", false ,url, "json", func);
			}

		function saveComentFac (objVal,Factura,RefPadreGps)
		{

				//alert("saveComentFac: objVal: " + objVal + " , Factura: " + Factura + " , RFP: " +  RefPadreGps) ;
		if (Factura =="0")
			 Factura="";
		 
		if (RefPadreGps=="0")
				RefPadreGps="";
		
		url="process_comentario.php?";
		url += $("#frmUpload").serialize();
		url += "&comentario="+objVal +  "&factura=" + Factura +  "&refpadregps=" + RefPadreGps +  "&modulo=F"  ;
		

		executeAjax("post", false ,url, "json", func);
		}

		

	function enviarPopUpXls(sDestinoURL)
		{
		
		window.open(sDestinoURL,"XLS","toolbar=no,menubar=no,location=no,directories=no,menubar=no,resizable=yes,scrollbars=yes,width=500,height=500");
		}
		
		
  </script>

<style >
<!--

.mytext
{
background:#FF9999;
border:  thin; 


border-bottom-color: gray;
border-bottom-style: solid;
border-bottom-width:thin;


}

-->
</style>


<div class="ui-widget-header align-center">M&Oacute;DULO DE CONCILIACI&Oacute;N</div>	
    	
<form id="frmUpload" name="frmUpload" action="process_concontable.php" method="post" enctype="multipart/form-data" >
   
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
        
        
<div id="mytabs" >
	<ul>
	<li><a href="#mytabs-0"> Carga de Archivos </a></li>
	<li><a href="#mytabs-1"> Conciliaci&oacute;n Contable </a></li>
	<li><a href="#mytabs-2"> Detalle Facturas </a></li>
	<li><a href="#mytabs-3"> Reporte de Seguimiento </a></li>
	</ul> 
			
		<div id="mytabs-0">                    
			<div id="div_conciliacion0">
			
				
				<br>
			<div id="iframe" align="center" >
    		<iframe src="cat_upload_file.php?<?php echo $urlpermisos?>" frameborder="0" align="middle" width="95%" height="340px" scrolling="yes" >
    		</iframe>
			</div>
				
				
			</div>
		</div>
		
			
	<div id="mytabs-1">                    
		<div id="div_conciliacion1">
	            
	           
	            
	            <table border="0" cellspacing="4" cellpadding="4"  align="center" width="100%" >
	            <tr>
	              <td nowrap="nowrap"  class="ui-state-default">A&ntilde;o-Mes/Rango: &nbsp;
	                    		<select name="anoQueryInf" id="anoQueryInf" onchange="javascript:recargaCombosP1();" > 
	                    			<?								
									for ($i = 0; $i < count($TheCatalogoAnio); $i++)
									{	         			 
										$id_anio=$TheCatalogoAnio[$i]['id_anio'];		
										$tx_anio=$TheCatalogoAnio[$i]['tx_anio'];			  
										echo "<option value=$id_anio>$tx_anio</option>";
									}						 
									?>
					      		</select>
					      				
					      		<select name="mesQueryInf" id="mesQueryInf"> 
		                    		<option value="1">Enero</option> 
		                    		<option value="2">Febrero</option> 
		                    		<option value="3">Marzo</option>
		                    		<option value="4">Abril</option>
		                    		<option value="5">Mayo</option>
		                    		<option value="6">Junio</option>
		                    		<option value="7">Julio</option>
		                    		<option value="8">Agosto</option>
		                    		<option value="9">Septiembre</option>
		                    		<option value="10">Octubre</option>
		                    		<option value="11">Noviembre</option>
		                    		<option value="12">Diciembre</option>
	                    		</select>  
	              
	               			
	                    	 <select name="rango" id="rango" >
                                      <option value="M">Mensual</option>
                                      <option value="A">Acumulada</option>
                             </select>
                   </td>
                                
                    <td nowrap="nowrap"  class="ui-state-default">
                   	Cuenta CSI:&nbsp; 
                   	<select name="tipoCuenta" id="tipoCuenta" > 
                     	<option value="0">-Seleccione-</option> 
                     	<?								
							for ($i = 0; $i < count($TheCatalogoCuenta); $i++)
									{	         			 
										$id_cuenta=$TheCatalogoCuenta[$i]['id_cuenta'];		
										$tx_cuenta=$TheCatalogoCuenta[$i]['tx_cuenta'];			  
										echo "<option value=$id_cuenta>$tx_cuenta</option>";
									}						 
						?>
                     	
	               	</select>   
	                                     
	            	</td>
	            	    
	                    
	            	<td  nowrap="nowrap" class="ui-state-default" id="divtheGpsQueryFile">
	            	GPS File: &nbsp;  <select name="theGpsQueryFile"  id="theGpsQueryFile"> 
	            		
				            		<option value="0">-- Seleccione --</option>
			           				<?								
									for ($i = 0; $i < count($TheCatalogoGPS); $i++)
									{	         			 
														
											$id_archivo=$TheCatalogoGPS[$i]['id_archivo'];		
											$tx_archivo=$TheCatalogoGPS[$i]['tx_archivo'];			  
											echo "<option value=$id_archivo>$tx_archivo</option>";
									}						 
									?>
      					</select>
	            	</td>
	            	<td nowrap="nowrap" class="ui-state-default" id="divtheEssbaseQueryFile">
	            		ESSBASE File: &nbsp;  <select name="theEssbaseQueryFile"  id="theEssbaseQueryFile" > 
						            	<option value="0">-- Seleccione --</option>
					           				<?								
											for ($i = 0; $i < count($TheCatalogoESSBASE); $i++)
											{	         			 
															
													$id_archivo=$TheCatalogoESSBASE[$i]['id_archivo'];		
													$tx_archivo=$TheCatalogoESSBASE[$i]['tx_archivo'];			  
													echo "<option value=$id_archivo>$tx_archivo</option>";
											}						 
											?>
					      					</select>
	            						
	          		</td>

                	
	                        
	            	<td align="right" nowrap="nowrap">
	               		<a id="btnBuscar"  class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Conciliar" >
	               			Buscar
	              			 <span class="ui-icon ui-icon-search"> </span>
	               		</a>
	                        
                   		<a id="btnLimpia"  class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Limpiar" >
                 			Limpiar
                 			<span class="ui-icon ui-icon-refresh"> </span>
                 		</a>
                   	</td>
	            </tr>
	            <tr><td>&nbsp;
	            
	            </td></tr>
	            </table>
	            
	  
	            
	            <br>
	            
	    <table border=0 cellpadding="2" cellspacing="2"  width="100%">
	    	<tr>
	   			<td valign="top" width="50%" align="center" class="ui-state-default" >	
            	CONCILIACI&Oacute;N CSI vs GPS-P
                </td>
	            <td  valign="top"  width="50%" align="center"  class="ui-state-default" >	
            	CONCILIACI&Oacute;N GPS+P  vs ESSBASE
             
                </td>
	        </tr>
	        
	    	<tr>
	   			<td valign="top" width="50%" align="center" style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">	
                <div id="divDireccionConcGPS"     style="font-size:11px;width:100%;"></div>	
                </td>
	            <td  valign="top"  width="50%" align="center" style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">	
                <div id="divDireccionConcESB"  style="font-size:11px;width:100%;" ></div>	  
              
                </td>
	        </tr>
	            
	        <tr>
	            <td  valign="top"  width="50%" align="center" style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">
                <div id="divSubDireccionConcGPS"  style="font-size:11px;width:100%;"></div>	</td>
	            <td  valign="top"  width="50%" align="center"  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">
                <div id="divSubDireccionConcESB"  style="font-size:11px;width:100%;"></div>	</td>
	         </tr>
	            
	         <tr>
	            <td  valign="top"  width="50%" align="center"  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">	
                <div id="divDepartamentoConcGPS"  style="font-size:11px;width:100%;"></div>	</td>
	            <td  valign="top"  width="50%" align="center"  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px" >	
                <div id="divDepartamentoConcESB"  style="font-size:11px;width:100%;"></div>	</td>
	         </tr>
             <tr>
             <td>
             	<table border="0" width="100%" cellpadding="2" cellspacing="2">
                <tr>
                <td align="left" valign="top">
                <div id="divFacturasCSI"  style="font-size:11px;width:100%;"></div>
                </td>
                <td align="left" valign="top">
                <div id="divFacturasGPS"  style="font-size:11px;width:100%;"></div>
                </td>
                </tr>
                </table>
             </td>
             <td>
             </td>
             </tr>
             
	    </table>
	    </div>    
	</div>
	        










	<div id="mytabs-2">
	 	<div id="div_conciliacion2">
	  
	  
	       <table border="0"  cellpadding="4" cellspacing="4">
	       <tr  valign="top">
	       
	       
	              <td nowrap="nowrap" class="ui-state-default">A&ntilde;o-Mes 
	                    		<select name="anoQueryFac" id="anoQueryFac" onchange="javascript:recargaCombosP2();"> 
	                    			<?								
									for ($i = 0; $i < count($TheCatalogoAnio); $i++)
									{	         			 
										$id_anio=$TheCatalogoAnio[$i]['id_anio'];		
										$tx_anio=$TheCatalogoAnio[$i]['tx_anio'];			  
										echo "<option value=$id_anio>$tx_anio</option>";
									}						 
									?>
					      		</select>
					      				
					      		<select name="mesQueryFac" id="mesQueryFac">
					      			<option value="0">-Seleccione-</option>  
		                    		<option value="1">Enero</option> 
		                    		<option value="2">Febrero</option> 
		                    		<option value="3">Marzo</option>
		                    		<option value="4">Abril</option>
		                    		<option value="5">Mayo</option>
		                    		<option value="6">Junio</option>
		                    		<option value="7">Julio</option>
		                    		<option value="8">Agosto</option>
		                    		<option value="9">Septiembre</option>
		                    		<option value="10">Octubre</option>
		                    		<option value="11">Noviembre</option>
		                    		<option value="12">Diciembre</option>
	                    		</select>  
	               </td>
	               
	               
	               
	               
	              
	               <td nowrap="nowrap" class="ui-state-default" >
	               			Estatus CSI 
	                    	<select name="estatusFac" id="estatusFac"> 
	                    			<option value="0">-Seleccione-</option> 
	                    			<?								
									for ($i = 0; $i < count($TheCatalogoStatusFac); $i++)
									{	         			 
										$id_factura_estatus=$TheCatalogoStatusFac[$i]['id_factura_estatus'];		
										$tx_estatus=$TheCatalogoStatusFac[$i]['tx_estatus'];			  
										echo "<option value=$id_factura_estatus >$tx_estatus</option>";
									}						 
									?>
					      		</select>
					      		&nbsp;
           
					      		Proveedor CSI <select name="proveedorFac"  id="proveedorFac"> 
	            		
				            		<option value="0" >-Seleccione-</option>
			           				<?								
									for ($i = 0; $i < count($TheCatalogoProveedor); $i++)
									{	         			 
														
											$id_proveedor =$TheCatalogoProveedor[$i]['id_proveedor'];		
											$tx_proveedor_corto =substr($TheCatalogoProveedor[$i]['tx_proveedor'],0,10)."..";			  
											echo "<option value=$id_proveedor >$tx_proveedor_corto</option>";
									}						 
									?>
      					</select>
      					&nbsp;
                        
           
           
					     Factura CSI <input type="text" name="theFacturaCSI"  id="theFacturaCSI" size=8 >
                   </td>
                             <td nowrap="nowrap" class="ui-state-default" >
                             Factura GPS:&nbsp;<input type="text" name="theFacturaGPS" id="theFacturaGPS" size=8 >
                             </td>
                                
                                
                                
                                    
	            	<td nowrap="nowrap" class="ui-state-default" id="divtheGPSQueryFileFac">
	            		Archivo GPS <select name="theGPSQueryFileFac"  id="theGPSQueryFileFac" > 
						            	<option value="0">-Seleccione-</option> 
					           				<?								
											for ($i = 0; $i < count($TheCatalogoGPS); $i++)
											{	         			 
																
													$id_archivo=$TheCatalogoGPS[$i]['id_archivo'];		
													$tx_archivo=$TheCatalogoGPS[$i]['tx_archivo'];			  
													echo "<option value=$id_archivo>$tx_archivo</option>";
											}						 
											?>
					      					</select>
					      				
	            						
	          		</td>

                	
	            	
	            	
	            	
	                        
	            	
	            
	            
	       
	      
	       <td align="right" nowrap="nowrap">
	               		<a id="btnBuscaFactura"  class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Conciliar" >
	               			Buscar
	              			 <span class="ui-icon ui-icon-search"> </span>
	               		</a>
	               		<a id="btnLimpiaFac"  class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Limpiar" >
                 			Limpiar
                 			<span class="ui-icon ui-icon-refresh"> </span>
                 		</a>
	                        
                   		
                   	</td>
                   	
                   	
	       </tr>
	       </table>
	       <table border=0 width="100%">
	       <tr><td align="right">
	        <div id="errcaptura" style="float:right;"></div>
	       </td></tr>
	       </table>
	       
	       
	       
	       
	      
	            	
	            
	            
	            
	            <br>
	            
 			<table border=0 cellpadding="2" cellspacing="2"  width="100%">
	    	<tr>
	    		<td  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px" >
	        		
	           		<div id="divResultsetFacturas" ></div>
                
            	</td>
	        </tr>
	        <tr>
	    		<td  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">
	        
	            	 
	            	 <div id="divResultsetFacturasCR" ></div>
                
            	</td>
	        </tr>
	        <tr>
	    		<td  style="border-bottom:dotted;border-bottom-color:#CCCCCC;border-bottom-width:1px">
	        
	            	 
	            	 <div id="divResultsetFacturasCRDerrama" ></div>
                
            	</td>
	        </tr>
	        </table>
	        
	        	  
	  
	  
	  
	  
	    </div>
	</div>
		
		
		<div id="mytabs-3">
			<div id="div_conciliacion3">
			
			
			<br>
		   <table border="0"  cellpadding="0" cellspacing="0" width="100%">
		   <tr>
		   <td>

		   		<table border="0"  cellpadding="3" cellspacing="3" >
		         <tr  valign="top">
	              <td nowrap="nowrap" class="ui-state-default">A&ntilde;o &nbsp; 
	                    		<select name="anoQueryRep" id="anoQueryRep"> 
	                    			<?								
									for ($i = 0; $i < count($TheCatalogoAnio); $i++)
									{	         			 
										$id_anio=$TheCatalogoAnio[$i]['id_anio'];		
										$tx_anio=$TheCatalogoAnio[$i]['tx_anio'];			  
										echo "<option value=$tx_anio>$tx_anio</option>";
									}						 
									?>
					      		</select>
					</td>
					</tr>
				</table>
			</td>
			<td align="right">
			
							<a id="btnBuscaReporte"  class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Buscar" >
	               			Buscar
	              			 <span class="ui-icon ui-icon-search"> </span>
	               			</a>
	              			
	           </td>
	         	</tr>
	         </table>
	          
	          <br>
			  <br>
			 <div id="divResultsetReporte" align="center" ></div>	
			 <br><br>
             <div id="divListadoFacturas" align="center" ></div>	
            
            
            </div>
            
		</div>
</div>
</form>

<? 
	mysqli_close($mysql);
}
?>
