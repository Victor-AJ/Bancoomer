<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{	
	include("includes/funciones.php");  
	$mysql=conexion_db();	
?>  	
	<script type="text/javascript">
                
        $(function() {   
            $('#tabs').tabs();						
        });	

		var id="par_direccion="+$("#sel_direccion").val();	
		var id1="&par_subdireccion="+$("#sel_subdireccion").val();	
		var id2="&par_departamento="+$("#sel_departamento").val();	
		var id3="&par_equipo="+$("#sel_equipo").val();	
		var id4="&par_status="+$("#sel_status").val();	
		
		var par_equipo=+$("#sel_equipo").val();	

		//alert ("Equipo "+par_equipo);	
		
		if (par_equipo==1) var url1="inf_equipamiento_matriz.php?"+id+id1+id2+id3+id4;   		
		else var url1="inf_equipamiento_matriz_tel.php?"+id+id1+id2+id3+id4;   		
			   
        loadHtmlAjax(true, $("#divDireccionEqui"), url1);				

        
    </script>
   	
    <table cellspacing="1px" border="0" cellpadding="0" width="100%">         
   		<tr>
        	<td colspan="2"> 
            	<div id="tabs">
                	<ul>
        				<li><a href="#tabs-1">Por Direcci&oacute;n Corporativa</a></li>
                    </ul>
                    <div id="tabs-1">
                    	<div id="divDireccionEqui" class="ui-widget ui-widget-content ui-helper-clearfix" style="font-size:11px;width:100%;"></div>                    
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