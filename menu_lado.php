<?php
		include("includes/funciones.php");
		$mysql = conexion_db();	
		session_start();
		if 	( !isset($_SESSION["sess_user"]))		echo "Sessi&oacute;n Invalida";
		else
		{
 			$txtlogin = $_SESSION["sess_user"];		
			$sql = "select tbl_opcion.id_opcion AS catOpcion, tbl_opcion.tx_nombre as catTxtOpcion, tbl_opcion.tx_cve_padre , tbl_opcion.tx_vinculo , ";
			$sql .= "tbl_perfil_opcion.id_opcion as confOpcion, tbl_perfil_opcion.id_perfil as confPerfil ,tbl_perfil_opcion.tx_p1 ,tbl_perfil_opcion.tx_p2 ,tbl_perfil_opcion.tx_p3 ,tbl_perfil_opcion.tx_p4 ,tbl_perfil_opcion.tx_p5 ,  tbl_perfil_opcion.tx_p6 ,tbl_perfil_opcion.tx_p7 ,tbl_perfil_opcion.tx_p8 ,tbl_perfil_opcion.tx_p9 ,tbl_perfil_opcion.tx_p10 , ";
			$sql .= "tbl39_menu.tx_menu as txPadre, tbl39_menu.in_precedencia  from tbl_opcion "; 
			$sql .= "left join  tbl_perfil_opcion  on  (tbl_opcion.id_opcion = tbl_perfil_opcion.id_opcion and tbl_perfil_opcion.id_perfil= (SELECT ID_PERFIL FROM TBL_USUARIO WHERE TX_USUARIO='".$txtlogin."')  ) " ;
			$sql .= "inner join tbl39_menu on tbl_opcion.tx_cve_padre=tbl39_menu.tx_cve_padre ";
			$sql .= "  where  tbl_opcion.tx_indicador=1  order by tbl39_menu.in_precedencia, tbl_opcion.id_opcion "; 
			$result = mysqli_query($mysql, $sql);
			$num_rows = mysqli_num_rows($result);
?>
			<script type="text/javascript">
			$("#divContent").html("");	

			myLayout.open("west");
			myLayout.open("north");
			
		    $(function()
		    	    {	$("#divSecondMenu").accordion({ header: "h3"});
		        		$('a, button, li').hover(function() { $(this).addClass('ui-state-hover'); },function() { $(this).removeClass('ui-state-hover'); } );
		    		});
			</script>
			<div class="ui-widget-header align-center">MENU</div>
			<div id="divSecondMenu" class="ui-accordion ui-widget ui-helper-reset">
<?php 
				$cvePadreAnt="";
				$act_row=1;
				while($row = mysqli_fetch_array($result))
				{  	
					$id_menu	=$row["catOpcion"];						
					$tx_opcion	=$row["catTxtOpcion"]; //texto del vinculo
					$cve_padre  =$row["tx_cve_padre"]; //clave llave del menu de 3 letras
					$tx_vinculo =$row["tx_vinculo"];
					$conf_opcion=$row["confOpcion"];
					$conf_Perfil=$row["confPerfil"];
					$tx_p1		=($row["tx_p1"]==null)?"0":$row["tx_p1"];
					$tx_p2		=($row["tx_p2"]==null)?"0":$row["tx_p2"];
					$tx_p3		=($row["tx_p3"]==null)?"0":$row["tx_p3"];
					$tx_p4		=($row["tx_p4"]==null)?"0":$row["tx_p4"];
					$tx_p5		=($row["tx_p5"]==null)?"0":$row["tx_p5"];
					$tx_p6		=($row["tx_p6"]==null)?"0":$row["tx_p6"];
					$tx_p7		=($row["tx_p7"]==null)?"0":$row["tx_p7"];
					$tx_p8		=($row["tx_p8"]==null)?"0":$row["tx_p8"];
					$tx_p9		=($row["tx_p9"]==null)?"0":$row["tx_p9"];
					$tx_p10		=($row["tx_p10"]==null)?"0":$row["tx_p10"];
					
					$tx_padre =$row["txPadre"];   //Nombre del Menu padre
		   			
					//CAMBIOALCANCE MENU PARA SOPORTE A CATALOGO DE CATALOGOS
					//$url=$tx_vinculo.'.php?tx_p1='.$tx_p1.'&tx_p2='.$tx_p2.'&tx_p3='.$tx_p3.'&tx_p4='.$tx_p4.'&tx_p5='.$tx_p5;
					$url=$tx_vinculo.'tx_p1='.$tx_p1.'&tx_p2='.$tx_p2.'&tx_p3='.$tx_p3.'&tx_p4='.$tx_p4.'&tx_p5='.$tx_p5;
					$url.="&tx_p6=".$tx_p6."&tx_p7=".$tx_p7."&tx_p8=".$tx_p8."&tx_p9=".$tx_p9."&tx_p10=".$tx_p10;
					
					
					
				   if ($tx_p1 =="0" && $tx_p2=="0" && $tx_p3=="0" && $tx_p4=="0" && $tx_p5=="0" && $tx_p6 =="0" && $tx_p7=="0" && $tx_p8=="0" && $tx_p9=="0" && $tx_p10=="0" )
					   {
					   $estadoCalculado="ui-state-disabled fontMedium";
					   $styleCalculado="border:none";
					   $cadFunciones="border:none";
					   }
				   else
					   {
					   $estadoCalculado= "ui-state-default fontMedium" ;
					   $cadFunciones=" onclick=\"myLayout.close('west'); myLayout.close('north'); loadHtmlAjax(true, $('#divContent'), '".$url. "')\"";
					   $styleCalculado="border:none";
					   }
					
				    if ($cvePadreAnt!=$cve_padre)
				    {
				      		if ($cvePadreAnt!="")  
				       			echo ("				</ul></div>");
?>
					<h3 class="ui-accordion-header ui-helper-reset ui-state-default fontMedium ui-corner-all">
		        		<a href="#"> <?  echo $tx_padre ?> </a>
		    		</h3>
		    		<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
		        	<ul class="ui-reset ui-clearfix ui-component ui-hover-state none">
<?php 
				    }
?>    
		    		<li title="<?echo $tx_opcion?>">
		   			<a  class="<?echo $estadoCalculado?> " style ="<? echo $styleCalculado?>" href="#"  <? echo $cadFunciones?>  > <? echo $tx_opcion ?></a>
		   			</li>
<?php   
				    $cvePadreAnt=$cve_padre;
				     if ($act_row==$num_rows)
					      echo ("</ul></div>");
					$act_row++;
				 } //while
				 echo ("</div>");
		}// else
?>   
<h3 class="ui-accordion-header ui-helper-reset ui-state-default fontMedium ui-corner-all"><a href="#">Salir</a></h3>
    <div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
        <ul class="ui-reset ui-clearfix ui-component ui-hover-state none">
            <li title="Salir">
                <a class="ui-state-default fontMedium" style="border:none" href="#"
                	onclick="logout()">Salir</a>
            </li>
        </ul>
    </div>  