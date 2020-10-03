<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
	$id_login = $_SESSION['sess_iduser'];
?>
<script type="text/javascript">	
	
	//alert ("Entre");
	var id="id="+$("#id_factura").val();
	var id6="&fl_precio_usd_cabecera="+$("#fl_precio_usd_cabecera").val();	
	var id7="&fl_precio_mxn_cabecera="+$("#fl_precio_mxn_cabecera").val();	
	var id8="&fl_precio_eur_cabecera="+$("#fl_precio_eur_cabecera").val();	
	loadHtmlAjax(true, $("#divCapturaDetalleLista"), "inf_facturas_lista_detalle.php?"+id+id6+id7+id8); 
	loadHtmlAjax(true, $("#divCapturaDetalleListaCR"), "cat_facturas_lista_detalle_carta.php?"+id+id6+id7+id8); 	
		 
	$(function(){
    	$('input:text').setMask();
    });	 
	
	$("#btnGrafica").click(function() {   			

		var id	= "id="+$("#id_factura").val();
		var url = "gra_fac_detalle.php?"+id;
		var windowprops ="top=40, left=60, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1100, height=700";
		var winName='_blank';  			
		window.open(url,winName,windowprops); 
		
	}).hover(function(){
		$(this).addClass("ui-state-hover")
	},function(){
		$(this).removeClass("ui-state-hover")
	});
	
	$("#btnExportar").click(function(){   
		
		var id="id="+$("#id_factura").val();		
	   	var url = "excel_carta_aceptacion.php?"+id;			
		//alert("url"+url);
		window.open( url,"_blank");		
		
	}).hover(function(){
		$(this).addClass("ui-state-hover")
	},function(){
		$(this).removeClass("ui-state-hover")
	});	
	
	$("#verCabecera").find("tr").hover(		 
       	function() { $(this).addClass('ui-state-hover'); },
       	function() { $(this).removeClass('ui-state-hover'); }
    );
			
</script>
<?
	include("includes/funciones.php");  
	include_once  ("Bitacora.class.php");	
	$mysql=conexion_db();	

	$id_factura	= $_GET['id'];			
		
	$titulo	= "DETALLE DE FACTURAS";		
	
	$sql = "   SELECT id_factura, tx_anio, b.id_proveedor, tx_proveedor_corto, c.id_cuenta, tx_cuenta, tx_factura, fh_factura, fh_inicio, fh_final, fl_precio_usd, fl_precio_mxn, fl_precio_eur, fl_tipo_cambio, tx_mes, tx_estatus ";
	$sql.= "     FROM tbl_factura a, tbl_proveedor b, tbl_cuenta c, tbl_mes d, tbl_factura_estatus e ";
	$sql.= "  	WHERE id_factura 			= $id_factura ";
	$sql.= "      AND a.id_proveedor		= b.id_proveedor ";
	$sql.= "      AND a.id_cuenta 			= c.id_cuenta ";
	$sql.= "      AND a.id_mes 				= d.id_mes ";
	$sql.= "      AND a.id_factura_estatus	= e.id_factura_estatus ";
	
	//echo "aaa", $sql;
			
	$result = mysqli_query($mysql, $sql);		
		
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{	
		$TheCatalogo[] = array(
			'id_factura'		=>$row["id_factura"],
			'tx_anio'			=>$row["tx_anio"],
	  		'id_proveedor'		=>$row["id_proveedor"],
	  		'tx_proveedor_corto'=>$row["tx_proveedor_corto"],
	  		'id_cuenta'			=>$row["id_cuenta"],
	  		'tx_cuenta'			=>$row["tx_cuenta"],
	  		'tx_factura'		=>$row["tx_factura"],
	  		'fh_factura'		=>$row["fh_factura"],
			'fh_inicio'			=>$row["fh_inicio"],
	  		'fh_final'			=>$row["fh_final"],
	  		'fl_precio_usd'		=>$row["fl_precio_usd"],
	  		'fl_precio_mxn'		=>$row["fl_precio_mxn"],
	  		'fl_precio_eur'		=>$row["fl_precio_eur"],
	  		'fl_tipo_cambio'	=>$row["fl_tipo_cambio"],
  			'tx_mes'			=>$row["tx_mes"],	  		
  			'tx_estatus'		=>$row["tx_estatus"]	  		
		);
	} 	
		
	for ($i=0; $i < count($TheCatalogo); $i++)	{ 	        			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_factura			=$TheCatalogo[$i]['id_factura'];
			$tx_anio			=$TheCatalogo[$i]['tx_anio'];
			$id_proveedor		=$TheCatalogo[$i]['id_proveedor'];
			$tx_proveedor_corto	=$TheCatalogo[$i]['tx_proveedor_corto'];
			$tx_cuenta			=$TheCatalogo[$i]['tx_cuenta'];
			$id_cuenta			=$TheCatalogo[$i]['id_cuenta'];
			$tx_factura			=$TheCatalogo[$i]['tx_factura'];
			$fh_factura			=$TheCatalogo[$i]['fh_factura'];
			$fh_inicio			=$TheCatalogo[$i]['fh_inicio'];
			$fh_final			=$TheCatalogo[$i]['fh_final'];
			$fl_precio_usd		=$TheCatalogo[$i]['fl_precio_usd'];
			$fl_precio_mxn		=$TheCatalogo[$i]['fl_precio_mxn'];
			$fl_precio_eur		=$TheCatalogo[$i]['fl_precio_eur'];
			$fl_tipo_cambio		=$TheCatalogo[$i]['fl_tipo_cambio'];	  		
			$tx_mes				=$TheCatalogo[$i]['tx_mes'];
			$tx_estatus			=$TheCatalogo[$i]['tx_estatus'];
	}
	
	$fh_perido = $fh_inicio."   ".$fh_final;		
	$fl_precio_usd_cabecera=$fl_precio_usd;	
	$fl_precio_mxn_cabecera=$fl_precio_mxn;	
	$fl_precio_eur_cabecera=$fl_precio_eur;	
	
	$fl_precio_usd=number_format($fl_precio_usd,2);	
	$fl_precio_mxn=number_format($fl_precio_mxn,2);		
	$fl_precio_eur=number_format($fl_precio_eur,2);		
	
	if ($fl_precio_usd==0) $fl_precio_usd="-";
	if ($fl_precio_mxn==0) $fl_precio_mxn="-";
	if ($fl_precio_eur==0) $fl_precio_eur="-";
	if ($fh_factura==NULL) $fh_factura="-";
	
	# ==========================================
	# Busca si ya esta la derrama de la factura
	# ==========================================		
	$sql = "   SELECT * ";
	$sql.= "     FROM tbl_factura_detalle ";
	$sql.= "  	WHERE id_factura = $id_factura ";
		
	//echo "aaa", $sql;
	
	$result = mysqli_query($mysql, $sql);
	$num_rows = mysqli_num_rows($result);	
	
	if ($num_rows > 0) $captura = 1;
	else $captura = 0;

?>
<br>
    <form id="opFacturaDetalle" action="">
        <input id="id_factura" name="id_factura" type="hidden" value="<? echo $id_factura ?>" />
    	<input id="id_proveedor" name="id_proveedor" type="hidden" value="<? echo $id_proveedor ?>" />
        <input id="tx_proveedor_corto" name="tx_proveedor_corto" type="hidden" value="<? echo $tx_proveedor_corto ?>" />
        <input id="id_cuenta" name="id_cuenta" type="hidden" value="<? echo $id_cuenta ?>" />
        <input id="tx_cuenta" name="tx_cuenta" type="hidden" value="<? echo $tx_cuenta ?>" />
    	<input id="fl_precio_usd_cabecera" name="fl_precio_usd_cabecera" type="hidden" value="<? echo $fl_precio_usd_cabecera ?>" />        
        <input id="fl_precio_mxn_cabecera" name="fl_precio_mxn_cabecera" type="hidden" value="<? echo $fl_precio_mxn_cabecera ?>" />        
        <input id="fl_precio_eur_cabecera" name="fl_precio_eur_cabecera" type="hidden" value="<? echo $fl_precio_eur_cabecera ?>" />        
	  	<table cellspacing="1px" border="0" cellpadding="0" width="100%">   		
   			<tr>
                <td class="ui-state-highlight" align="center" style="font-family:Verdana,Arial,sans-serif;font-size: 12px;font-weight:bold;"><? echo $titulo ?></td>  
         	</tr>           
            <tr>
            	<td>
                	<br/>
              		<fieldset>
            		<legend class="ui-state-default"><b><em>CABECERA DE LA FACTURA ...</em></b></legend>
                    	<br/>
             			<table id="verCabecera" width="100%"  cellspacing="1px" border="1" cellpadding="1">
                        	<tr>
                            	<td width="4%" class="ui-state-highlight align-center">A&ntilde;o</td>
                                <td width="12%" class="ui-state-highlight align-center">Proveedor</td>
                                <td width="13%" class="ui-state-highlight align-center">Cuenta</td>
                                <td width="8%" class="ui-state-highlight align-center">Factura</td>                                
                                <td width="7%" class="ui-state-highlight align-center">Monto USD</td>
                                <td width="7%" class="ui-state-highlight align-center">Monto MXN</td>
                                <td width="7%" class="ui-state-highlight align-center">Monto EUR</td>
                                <td width="8%" class="ui-state-highlight align-center">Tipo Cambio</td>
                                <td width="9%" class="ui-state-highlight align-center">Fecha Factura</td>
                                <td width="11%" class="ui-state-highlight align-center">Periodo</td>
                                <td width="7%" class="ui-state-highlight align-center">Amortizaci&oacute;n</td>
                                <td width="7%" class="ui-state-highlight align-center">Estatus</td>
                            </tr>
                            <tr>
                            	<td class="align-center"><? echo "$tx_anio" ?></td>
                                <td class="align-center"><? echo "$tx_proveedor_corto" ?></td>
                                <td class="align-center"><? echo "$tx_cuenta" ?></td>
                                <td class="align-center"><? echo "$tx_factura" ?></td>
                                <td class="align-right"><? echo "$fl_precio_usd" ?></td>
                                <td class="align-right"><? echo "$fl_precio_mxn" ?></td>
                                <td class="align-right"><? echo "$fl_precio_eur" ?></td>
                                <td class="align-right"><? echo "$fl_tipo_cambio" ?></td>
                                <td class="align-center"><? echo "$fh_factura" ?></td>
                                <td class="align-center"><? echo "$fh_perido" ?></td>
                                <td class="align-center"><? echo "$tx_mes" ?></td>
                                <td class="align-center"><? echo "$tx_estatus" ?></td>
                            </tr>                            
                        </table>  
                        <br/>                  
          			</fieldset>
            	</td>
            </tr>
            <? 
			if ($captura==0) {
			?>
				<table id="verCabecera" width="100%" cellspacing="1px" border="1" cellpadding="1">
                	<br/>
                	<tr>
                    	<td class='align-center'><em><b>No se a realizado la Derrama ...</b></em></td>
                    </tr>
                    <br/>
               	</table>     
			<?
			} else {         	                          
			?>
            <tr>
           		<td colspan="6">&nbsp;</td>
            </tr>    
            <tr>
            	<td class="align-center">
                    <a id="btnGrafica" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Gr&aacute;fica">
                    Gr&aacute;fica
                    <span class="ui-icon ui-icon-signal"></span>
                    </a>
                    <a id="btnExportar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Exportar">
                    Exportar
                    <span class="ui-icon ui-icon-extlink"></span>
                </a>
                </td>
            </tr>
 			<tr>
           		<td colspan="6">
                	<fieldset>
            			<legend class="ui-state-default"><b><em>DETALLE DERRAMA POR EMPLEADO ...</em></b></legend>
                        <table width="100%" cellspacing="1px" border="0" cellpadding="0">
                        	<tr>
                            	<td><div id="divCapturaDetalleLista"></div></td>
                            </tr>
                        </table>    
                     	<br/>    
                    </fieldset>
                </td>                      
            </tr>
            <tr>
           		<td colspan="6">
                	<br/>
                	<fieldset>
            			<legend class="ui-state-default"><b><em>DETALLE DERRAMA POR CR ...</em></b></legend>
                        <table width="100%" cellspacing="1px" border="0" cellpadding="0">
                        	<tr>
                            	<td><div id="divCapturaDetalleListaCR"></div></td>
                            </tr>
                        </table>    
                    <br/>    
                    </fieldset>                    
                </td>                      
            </tr>
            <?
			}
			?>
           	<tr>
           		<td colspan="6" class="ui-state-highlight">&nbsp;</td>                      
            </tr>        
    	</table>     
  </form>
<?
		//<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($mysql, "CONSULTA" , "TBL_FACTURA_DETALLE" , "$id_login" ,   "id_factura=$id_factura" ,"" ,"inf_facturas_detalle.php");
	 //<\BITACORA>

	 
mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  