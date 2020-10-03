<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{	
	include("includes/funciones.php"); 
	include_once  ("Bitacora.class.php"); 
	$id_login = $_SESSION['sess_iduser']; 
	$mysql=conexion_db();	

	$fl_tipo_cambio = 140000;	
	
	# =============================
	# Carga el combo de direcciones
	# =============================
	
	$sql = "   SELECT R.id_direccion, R.tx_nombre_corto ";
	$sql.= "     FROM tbl_direccion R";

	//SEGURIDAD: ACCESO A SUS DIRECCIONES
	$sql.="    inner join tbl_perfil_direccion PDIR on  ( R.id_direccion = PDIR.id_direccion and PDIR.tx_indicador='1' and  PDIR.id_perfil = (select id_perfil from tbl_usuario  where  id_usuario = $id_login )) ";
	$sql.= "    WHERE R.id_entidad = 1" ;
	$sql.= " ORDER BY tx_nombre ";
		
	

  
	


	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	{	
		$TheCatalogoDireccion[] = array(
			'id_direccion'		=>$row["id_direccion"],
			'tx_nombre_corto'	=>$row["tx_nombre_corto"]
		);
	} 
	
?>  	
	<script type="text/javascript">
                
        $(function() {   
            $('#tabs').tabs();	
			$('input:text').setMask();					
        });			
		
		var id="id_direccion=0";	
		var id1="&fl_tipo_cambio=14.0000";	
		var id2="&tx_moneda=USD";	
		
        var url1="inf_inventario_direccion.php?"+id+id1+id2;   		
        var url2="inf_inventario_proveedor.php?"+id+id1;          
    
        loadHtmlAjax(true, $('#divDireccion'), url1);				
        loadHtmlAjax(true, $('#divProveedor'), url2);
		
		function fieldsRequired(){		
			var error = true;
			validNumeric($("#cap_tc"), $("#errcap_tc"));
			if( !validNumeric($("#cap_tc"), $("#errcap_tc"))) error = false;        		 
			return error;		
		}	
		
		$("#sel_direccion").change(function () {
			$("#sel_direccion option:selected").each(function () {				
				var id="id_direccion="+$(this).val();
				var id1="&fl_tipo_cambio="+$("#cap_tc").val();
				var id2="&tx_moneda="+$("#sel_moneda").val();
				$("#divDireccion").html("");
				//alert ("Entre"+id+id1+id2);	
				loadHtmlAjax(true, $("#divDireccion"), "inf_inventario_direccion.php?"+id+id1+id2); 
			});
    	});
		
		$("#cap_tc").focus(function() {
     		$(this).addClass('ui-state-focus');
    	});

    	$("#cap_tc").blur(function() {
    		$(this).removeClass('ui-state-focus');
    	});
		
		$("#btnValuar").click(function(){   
	
			if(fieldsRequired()){				
				var id="id_direccion="+$("#sel_direccion").val();
				var id1="&fl_tipo_cambio="+$("#cap_tc").val();
				var id2="&tx_moneda="+$("#sel_moneda").val();
				$("#divDireccion").html("");	
				//alert ("Datos"+id+id1+id2);
				loadHtmlAjax(true, $("#divDireccion"), "inf_inventario_direccion.php?"+id+id1+id2); 			
			}
		   
    	}).hover(function(){
    		$(this).addClass("ui-state-hover")
    	},function(){
    		$(this).removeClass("ui-state-hover")
    	});
		
		$("#btnNewRefre").click(function(){  	
	
			if(fieldsRequired()){				
				var id="id_direccion="+$("#sel_direccion").val();
				var id1="&fl_tipo_cambio="+$("#cap_tc").val();
				var id2="&tx_moneda="+$("#sel_moneda").val();
				$("#divDireccion").html("");	
				//alert ("Datos"+id+id1+id2);
				loadHtmlAjax(true, $("#divDireccion"), "inf_inventario_direccion.php?"+id+id1+id2); 			
			}
			
		}).hover(function(){
			$(this).addClass("ui-state-hover")
		},function(){
			$(this).removeClass("ui-state-hover")
		});
		
    </script>
   	
    <table cellspacing="1px" border="0" cellpadding="0" width="100%">         
   		<tr>
        	<td colspan="2"> 
            	<div id="tabs">
                	<ul>
        				<li><a href="#tabs-1">Por Direcci&oacute;n Corporativa</a></li>
                      	<li><a href="#tabs-2">Por Proveedor</a></li>                     						
                    </ul>
                    <div id="tabs-1">
                    	<div class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;">                     	                                     	
                        <fieldset>
            			<legend class="ui-state-default"><b><em>CONFIGURACION ...</em></b></legend>
                        	<br/>
               	  			<table cellspacing="1px" border="0" cellpadding="0" width="100%">         
           		  				<tr>
                                	<td width="8%" class="ui-state-default">Direcci&oacute;n:</td>
                               	  	<td width="18%">
                                	<select id="sel_direccion" name="sel_direccion">              
                                    <option value = 0>TODAS</option>              
                                	<?
                                	for ($i=0; $i < count($TheCatalogoDireccion); $i++)	{         			 
                                    	while ($elemento = each($TheCatalogoDireccion[$i]))					  		
											$id_direccion		= $TheCatalogoDireccion[$i]['id_direccion'];										
                                        	$tx_nombre_corto	= $TheCatalogoDireccion[$i]['tx_nombre_corto'];										
                                        	echo "<option value = $id_direccion>$tx_nombre_corto</option>";	
                                	}
                                	?>
                                	</select>
                                    </td>    
                                	<td width="8%" class="ui-state-default">Moneda:</td>
                                	<td width="10%">
                                    <select id="sel_moneda" name="sel_moneda">
                                    	<option value="USD">USD</option>
                                      	<option value="MXN">MXN</option> 
                                    </select>
                                    </td>     
                                	<td width="8%" class="ui-state-default">Tipo de Cambio:</td>
                                	<td width="10%">
                                    	<input name="cap_tc" id="cap_tc" type="text" size="15" alt="decimal" title="Tipo de Cambio" value="<? echo $fl_tipo_cambio ?>"/>
                                    </td>
                                    <td width="20%"><div id="errcap_tc" style="float:left;"></div></td>
                               	  	<td width="18%">
                                    <a id="btnValuar" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Valuar">
                                	Valuar
                                	<span class="ui-icon ui-icon-calculator"></span>
                                    </a>
                                    <a id="btnNewRefre" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:void(0)" style="font-size:smaller;" title="Refrescar">
                    				Refrescar
                    				<span class="ui-icon ui-icon-refresh"></span></a>
                                    </td>                                	
                            	</tr>                         
                        	</table> 
                    		<br/> 
                            </fieldset>                                 
                     	</div>
                        <div id="divDireccion" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>    
                    </div>
                    <div id="tabs-2">
                    	<div id="divProveedor" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>
                    </div>
                </div>               
          </td>
      	</tr>           
   	</table>      
<?
	mysqli_close($mysql);	
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>  