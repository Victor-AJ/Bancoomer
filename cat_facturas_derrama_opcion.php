<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>
<script type="text/javascript">	
	
	$(function(){
    	$('input:text').setMask();
    });
	 
	var id0="id_cuenta="+$("#id_cuenta").val();
	var id1="&id_proveedor="+$("#id_proveedor").val();
	var id2="&sel_pagos="+$("#sel_pagos").val();
	var id3="&cap_gasto="+$("#cap_gasto").val();
	var id4="&fl_precio_usd="+$("#fl_precio_usd_cabecera").val();
	var id5="&fl_precio_mxn="+$("#fl_precio_mxn_cabecera").val();							
	var id6="&fl_precio_eur="+$("#fl_precio_eur_cabecera").val();	
	loadHtmlAjax(true, $("#divSimular"), "cat_facturas_derrama_simular.php?"+id0+id1+id2+id3+id4+id5+id6); 
	 
	$("#btnSimular").click(function(){   
	
		var id0="id_cuenta="+$("#id_cuenta").val();
		var id1="&id_proveedor="+$("#id_proveedor").val();
		var id2="&sel_pagos="+$("#sel_pagos").val();
		var id3="&cap_gasto="+$("#cap_gasto").val();
		var id4="&fl_precio_usd="+$("#fl_precio_usd_cabecera").val();
		var id5="&fl_precio_mxn="+$("#fl_precio_mxn_cabecera").val();							
		var id6="&fl_precio_eur="+$("#fl_precio_eur_cabecera").val();		
		loadHtmlAjax(true, $("#divSimular"), "cat_facturas_derrama_simular.php?"+id0+id1+id2+id3+id4+id5+id6); 
		
    }).hover(function(){
    	$(this).addClass("ui-state-hover")
    },function(){
    	$(this).removeClass("ui-state-hover")
    });
	 			
</script>
<?
	include("includes/funciones.php");  
	$mysql=conexion_db();
	
	# ============================================
	# Recibo variables
	# ============================================
	$id_factura				= $_GET['id_factura'];	
	$id_cuenta				= $_GET['id_cuenta'];		
	$id_proveedor			= $_GET['id_proveedor'];
	
	//echo "<br>";
	//echo "ac_moneda",$ac_moneda;
?>
<br>
    <table cellspacing="1px" border="0" cellpadding="0" width="100%">
      	<tr>
	      	<td></td>
           	<td colspan="3" class="ui-state-highlight align-center">SIMULACION PAGOS VARIABLES</td>                  
       	</tr>
        <tr>
           	<td width="35%">&nbsp;</td>
           	<td width="15%" class="ui-state-default">Pagos:</td>
        	<td width="15%">
            <select id="sel_pagos" name="sel_pagos">
           	<?
				for ($i=1; $i < 48; $i++)	{         			 
					echo "<option value=$i>$i</option>";
				}
			?>
        	</select>
            </td>
            <td width="35%"></td>   
       	</tr>
        <tr>
           	<td></td>
           	<td class="ui-state-default">Gasto Compartido:</td>
            <td><input id="cap_gasto" name="cap_gasto" type="text" class="textbox" size="15" alt="signed-decimal-us" value="<? echo $fl_precio ?>" title="Precio"/></td>
           	<td class="align-center"></td>   
        </tr>                          
        <tr>
           	<td>&nbsp;</td>
       	  	<td colspan="3"><div id="divSimular"></div></td>
      </tr>  
        <tr>
        	<td></td>
            <td colspan="3" class="align-center">            	
            	<a id="btnSimular" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Simular">
                Simular
                <span class="ui-icon ui-icon-contact"></span></a>
           </td>
        </tr>          
    </table>     
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  