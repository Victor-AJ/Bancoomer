<?php
session_start();
include_once  ("struct.class.php");
include_once  ("Bitacora.class.php");
//conexion BD
include("includes/funciones.php");  
$mysql=conexion_db();

	$tx_p1	= $_POST['tx_p1']; //consultar
	$tx_p2	= $_POST['tx_p2']; //insertar
	$tx_p3	= $_POST['tx_p3']; //actualziar
	$tx_p4	= $_POST['tx_p4']; //borrar
	$tx_p5	= $_POST['tx_p5']; //exportar
	$tx_p6	= $_POST['tx_p6']; //UPLOADING
	$tx_p7	= $_POST['tx_p7']; 
	$tx_p8	= $_POST['tx_p8']; 
	$tx_p9	= $_POST['tx_p9']; 
	$tx_p10	= $_POST['tx_p10']; 
	$urlpermisos='tx_p1='.$tx_p1.'&tx_p2='.$tx_p2.'&tx_p3='.$tx_p3.'&tx_p4='.$tx_p4.'&tx_p5='.$tx_p5."&tx_p6=".$tx_p6."&tx_p7=".$tx_p7."&tx_p8=".$tx_p8."&tx_p9=".$tx_p9."&tx_p10=".$tx_p10;
	
if 	(!isset($_SESSION["sess_user"]))   
	 echo "Sessi&oacute;n Invalida";

else
{	
	
$id_login = $_SESSION['sess_iduser'];

	
	
//inicializaon variables 
$cadenatexto= $_POST["dispatch"];
$tipofile = $_POST["tipofile"];
$tipoInf = $_POST["tipoInf"];
$anioInf = $_POST["anioInf"];
//extraccion de parametros BD

$anioCalcGPS=0;
$cuentaCabeceraEBS="";

//siempre no quizo el usuario las validaciones

/*
$cuentaGps_par2="";
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-1' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$cuentaGps_par2 = $row[0];	
    
$cuentaAlt_par3="";
$sql = "   select tx_valor from tbl45_catalogo_global where tx_clave='PARAMETRO-2' and tx_indicador=1 " ;
$result = mysqli_query($mysql, $sql);
$row = mysqli_fetch_row($result);
$cuentaAlt_par3 = $row[0];	
*/

$OK="<font color='blue'>Correcto</font>";
$NOK="<font color='red'>Incorrecto</font>";
$error=true;

//inicializaciòn para upload
$nombre_archivo = $HTTP_POST_FILES['userfile']['name']; //ejemplo: gpsprueba.xls
$tipo_archivo = $HTTP_POST_FILES['userfile']['type'];   //ejemplo: application/vnd.ms-excel
$tamano_archivo = $HTTP_POST_FILES['userfile']['size'];  //ejemplo: 634
$SafeFile = $HTTP_POST_FILES['userfile']['name'];   //ejemplo: gpsprueba.xls
$uploaddir="uploads/";
$path= $uploaddir.$SafeFile;    //ejemplo: uploads/gpsprueba.xls

//inicializacion para desglosado de xls
set_time_limit(0); 
require_once 'Excel/reader.php'; 
$data = new Spreadsheet_Excel_Reader(); 
$data->setOutputEncoding('CP1251'); 

//funciones
function  isValidFile ($file,$anioInf , $tipofile)
{
	$retorno=true;
	$detalle="";
	$lon=strlen($file);
	$ext=substr ($file,$lon-3,$lon);
		
	if (  $file=="")
	{
		$retorno=false;
		$detalle.="nombre de archivo invalido";
	}elseif ( strlen($file) >= 50 )
			{
			$retorno=false;
			$detalle.=" el nombre debe ser menor a 50 caracteres. ";
			}
			elseif ( strtolower($ext)!="xls")
			{
				$retorno=false;
				$detalle.=" extensi&oacute;n invalida  ".$ext;
			}
				elseif ( $tipofile == 1 && $anioInf == 0 )  //essbase con opcion 0
				{
					$retorno=false;
					$detalle.=" No se eligió un año para ESSBASE  ";
				}	
				else 
				{
					$retorno=true;
					$detalle.="extensi&oacute;n valida ".$ext;
				}
		
	
$obj= new StructStatus();
$obj->setStatus($retorno);
$obj->setInformeDetalle($detalle);
	
return $obj;
}

//verificar que no exista en tabla de control de archivos 
function isFileLoaded($conection, $file)
{
	$sql = "   SELECT count(*)  from tbl40_archivos_upload where tx_archivo='$file' and tx_status='OK'"  ;
	$result = mysqli_query($conection, $sql);
	$row = mysqli_fetch_row($result);
	$count = $row[0];	
    return ($count > 0)? true: false;
}

function isUpLoadedOK($objeto,$mpath)
{
	$retorno= false;
	$detalle= "";
	
	try 
	{	
	   if (move_uploaded_file($objeto , $mpath))
		       $retorno= true; 
		    else
				$retorno= false;
	}
	catch (Exception  $e)
		{
		echo 'EXCEPTION';
		$detalle= "exception :".$e->getMessage();
		$retorno= false;
		}
	
$obj= new StructStatus();
$obj->setStatus($retorno);
$obj->setInformeDetalle($detalle);

return $obj;
}

function actualizarArchivoOK($conection,$id_file,$tipofile,$anioInf) 
{
	
	global $anioCalcGPS;
	$retorno=true;
	//Se crean variables por bitacora
	global 	$id_login;
	global $nombre_archivo;
	global $tipofile;

	
	
	
	if ($tipofile==1)
		$anio=$anioInf;
	else 
		$anio= $anioCalcGPS; 
	
	
	$sql = "   UPDATE  tbl40_archivos_upload SET tx_status='OK' , in_anio=$anio  WHERE id_archivo =$id_file";
	
	if (mysqli_query($conection, $sql))
	{
	 $retorno =true;  
	 //<BITACORA>
	 $myBitacora = new Bitacora();
	 $myBitacora->anotaBitacora ($conection, "CARGA ARCHIVO" , "TBL40_ARCHIVOS_UPLOAD" , "$id_login" ,  " tx_archivo=$nombre_archivo in_tipo= $tipofile tx_status=OK in_anio=$anio"  , "$id_file" , "process_upload_file.php");
	 //<\BITACORA>
	 }
	else
	 $retorno=false;
	
	
	 return $retorno;
	
}
function registrarArchivo($conection,$SafeFile, $tFile,$id_login) 
{
//retorno 0   ->   error
//retorno >0  ->   id del archivo insertado
	$retorno=0;
	
	$sql = "   INSERT INTO  tbl40_archivos_upload  (tx_archivo,in_tipo ,fh_carga, id_user_carga, tx_status) VALUES ('".$SafeFile."', ".$tFile." ,CURRENT_TIMESTAMP,$id_login ,'PEN')";
	
	if (mysqli_query($conection, $sql))
		
	{
		$sql = "   select ifnull(max(id_Archivo),0) as count from tbl40_archivos_upload where tx_archivo='$SafeFile'  and tx_status='PEN' and id_user_carga=$id_login ";
		$result = mysqli_query($conection, $sql);	
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$cantidad = $row['count'];
		
		if ($cantidad>0 )
			$retorno = $cantidad;	
			else	
			$retorno = 0;
	
	}
	 else
		$retorno= 0;

		
return $retorno;		
}


function limpiarTablaPaso($conection) 
{
	$sql = "   delete from  tbl41_paso_archivos ";
	if (mysqli_query($conection, $sql))
		return true;
	 else
		return false;
}

					   
function processInTablePass($Conn, $objData, $SafeFile, $tipoLayout)
{

	$retorno= true;
	//global $cuentaGps_par2;
	//global $cuentaAlt_par3;
	global $cuentaCabeceraEBS;
	
try 
{
	
	$errores_inserts=0;
	$registros_procesados=0;
	$pathfile="uploads\\".$SafeFile;
	$objx=$objData->read($pathfile);
	error_reporting(E_ALL ^ E_NOTICE);
	$detalle="";
	$detalleValida1="";
	$detalleValida2="";
	$detalleValida3="";
	
	$columnasFile=1;
	
	//Si es tipo gps barrer hasta 27 columnas si es essbase barrer hasta 13 columnas
	if ($tipoLayout==0)
		$columnasFile=27;
	else
		$columnasFile=13;
	
	
	//ciclo para barrer fila por fila de hoja 0, fila j=1 a j=count filas
	for($j=1;$j<= count($objData->sheets[0]['cells']) ; $j++) 
	{ 
			
			//creacion de: "insert into table (campos) values ('1'"
			$cadena="";
			$sql= " insert into tbl41_paso_archivos  (id_consecutivo   ";
			$cantidad=$columnasFile;
			
			for ( $n=1; $n<=$cantidad; $n++)
			$cadena .= " , tx_campo".$n ;
 			$cadena .= " ) values ( '".$j. " ' " ;
			$sql.= $cadena;
			
			
			//creacion de 2a parte: " ,campo2,campo3,campon)"
			for($z=1; $z<=  $cantidad ; $z++) 
			{ 
				$valorcelda=$objData->sheets[0]['cells'][$j][$z];
				//si el dato tiene apostrofo cambiar por space.
				$valorcelda = str_replace("'"," ",$valorcelda);
				$valorcelda = str_replace("\""," ",$valorcelda);
				$sql.= " , '".$valorcelda."' ";    
			} 
			$sql.= "  )  "; 
			
			
			
			if ($j==1 &&  $tipoLayout==0)  //validar campos de la primer fila con respecto a tipo de archivo GPS
			{
				$valorcelda1=strtolower(trim($objData->sheets[0]['cells'][$j][1]));
				$valorcelda2=strtolower(trim($objData->sheets[0]['cells'][$j][2]));  // debe ser fijo 32533003 (cuentaGps_par2)
				$valorcelda3=strtolower(trim($objData->sheets[0]['cells'][$j][3]));  // debe ser fijo 51090400000004 ($cuentaAlt_par3)
				$valorcelda4=strtolower(trim($objData->sheets[0]['cells'][$j][4]));
				$valorcelda5=strtolower(trim($objData->sheets[0]['cells'][$j][5]));
				$valorcelda6=strtolower(trim($objData->sheets[0]['cells'][$j][6]));
				$valorcelda7=strtolower(trim($objData->sheets[0]['cells'][$j][7]));
				$valorcelda8=strtolower(trim($objData->sheets[0]['cells'][$j][8]));
				$valorcelda9=strtolower(trim($objData->sheets[0]['cells'][$j][9]));  // debe ser 40  o 50
				$valorcelda10=substr( strtolower(trim($objData->sheets[0]['cells'][$j][10])),0,3);
				$valorcelda11=strtolower(trim($objData->sheets[0]['cells'][$j][11]));
				$valorcelda12=strtolower(trim($objData->sheets[0]['cells'][$j][12]));
				$valorcelda13=strtolower(trim($objData->sheets[0]['cells'][$j][13]));
			
				
				
					if( $valorcelda2  == "cuenta gps" && $valorcelda3  == "cuenta altamira" &&  $valorcelda4  == "centro responsable" &&
							$valorcelda5  == "fecha contable" && $valorcelda6  == "clase" &&  $valorcelda8  == "referencia" &&
							$valorcelda9  == "ct" &&  $valorcelda10 == "mon"  && $valorcelda11 == "importe en md" && $valorcelda12 == "importe en ml" )
							{
									$detalle="Cabecera GPS OK. ";
							}				
					else
					{
					
					
						$detalle="Cabecera GPS Incorrecta. ";
						$obj= new StructStatus();
						$obj->setStatus(false);
						$obj->setInformeDetalle($detalle);
						return $obj;
					}
			}
			
			
			if ($j==1 &&  $tipoLayout==1)  //validar cuenta ESB
			{
				$valorcelda=trim($objData->sheets[0]['cells'][$j][7]);
				//buscar el ultimo espacio y cortar ahi
				$lastSpace= strpos($valorcelda," ");
				$valorcelda=substr($valorcelda,0,$lastSpace);		
				//buscar equivalente cuenta en CSI
				
				$sql = " select id from tbl45_catalogo_global WHERE ";
				$sql.= " substr( tx_clave,1,instr(tx_clave ,'-')-1) = 'CUENTA_CONTABLE'  ";
				$sql.= " AND TX_INDICADOR=1 AND TX_VALOR_COMPLEMENTARIO = '$valorcelda' ";

				
				$result = mysqli_query($Conn, $sql);
				$row = mysqli_fetch_row($result);
				$cuentaGPS = $row[0];
				if ($cuentaGPS == null )
				{
						$detalle="No se encontro la cuenta de cabcera gps, en catálogo para: $valorcelda " ;
						$obj= new StructStatus();
						$obj->setStatus(false);
						$obj->setInformeDetalle($detalle);
						return $obj;
					
				}	
				else
				{
					$cuentaCabeceraEBS=$cuentaGPS;
					
				}
			    
				
				
			}
			
			if ($j==2 && $tipoLayout==1) //validar campos de la segunda  fila con respecto a tipo de archivo ESB
				{
					
					
				$valorcelda2=strtolower(trim($objData->sheets[0]['cells'][$j][2])); 
				$valorcelda3=strtolower(trim($objData->sheets[0]['cells'][$j][3]));  
				$valorcelda4=strtolower(trim($objData->sheets[0]['cells'][$j][4]));
				$valorcelda5=strtolower(trim($objData->sheets[0]['cells'][$j][5]));
				$valorcelda6=strtolower(trim($objData->sheets[0]['cells'][$j][6]));
				$valorcelda7=strtolower(trim($objData->sheets[0]['cells'][$j][7]));
				$valorcelda8=strtolower(trim($objData->sheets[0]['cells'][$j][8]));
				$valorcelda9=strtolower(trim($objData->sheets[0]['cells'][$j][9]));  
				$valorcelda10=strtolower(trim($objData->sheets[0]['cells'][$j][10]));
				$valorcelda11=strtolower(trim($objData->sheets[0]['cells'][$j][11]));
				$valorcelda12=strtolower(trim($objData->sheets[0]['cells'][$j][12]));
				$valorcelda13=strtolower(trim($objData->sheets[0]['cells'][$j][13]));
					
					if( $valorcelda2 =="enero" &&  $valorcelda3 == "febrero" &&  $valorcelda4 == "marzo" && $valorcelda5 ="abril" &&  $valorcelda6 ="mayo" &&  $valorcelda7 ="junio" &&  
					 	$valorcelda8 =="julio" &&  $valorcelda9 == "agosto" &&  $valorcelda10 == "septiembre" && $valorcelda11 =="octubre" &&  $valorcelda12 == "noviembre" &&  $valorcelda13 == "diciembre"  )
					{
						$detalle="Cabecera ESB OK. ";
					}
					else
					{
						$detalle="Cabecera ESB Incorrecta. ";
						$obj= new StructStatus();
						$obj->setStatus(false);
						$obj->setInformeDetalle($detalle);
						return $obj;
					}
				}
			
			
			if ($j<>1 && $tipoLayout==0)  //ignorar la primer fila para gps  
			{
				$valorcelda2=trim($objData->sheets[0]['cells'][$j][2]);  // debe ser fijo 32533003 ($cuentaGps_par2) SE QUITAN VALIDACIONES (03/08/11)
				$valorcelda3=trim($objData->sheets[0]['cells'][$j][3]);  // debe ser fijo 51090400000004 ($cuentaAlt_par3) SE QUITAN VALIDACIONES (03/08/11)
				
				$valorcelda9=trim($objData->sheets[0]['cells'][$j][9]);  // debe ser 40  o 50
			//validar campos fijos 

				//if ( $valorcelda2 ==	$cuentaGps_par2 )
					//if ( $valorcelda3 ==  $cuentaAlt_par3 )
			
							
							if ( $valorcelda9 == "40"  ||  $valorcelda9 == "50" )
								{
									if (mysqli_query($Conn, $sql))
										$registros_procesados ++;
						 			else
										$errores_inserts ++ ;
								}
								else
								{
									$detalleValida3="(regs con CT <> 40 o 50)";
									$errores_inserts ++ ;
								}
						
					//	else {$detalleValida2="(regs con cta Altam <> a param $cuentaAlt_par3)";	$errores_inserts ++ ;}
				//else {$detalleValida1="(regs con cta GPS <> a param $cuentaGps_par2 )";	$errores_inserts ++ ;}
			
			}
			
			
	        if ($j<>1 && $j<>2 && $tipoLayout==1)  //ignorar la primery segunda fila para ESB  
			{
					
					$crESB="";
					$valorcelda4=strtolower(trim($objData->sheets[0]['cells'][$j][1]));
					$crESB=substr($valorcelda4,0,4);
				
					if ($crESB <> "9900")
					{
					if (mysqli_query($Conn, $sql))  //solo si el CR <> 9900
						$registros_procesados ++;
					else
						$errores_inserts ++ ;
					}
			}

			
	}//for q barre todas las filas 
	
	$detalle.= " <font color=blue > regs. procesados: ".$registros_procesados." </font>, <font color=red >regs. sin procesar:".$errores_inserts." </font> ".$detalleValida1.$detalleValida2.$detalleValida3 ;
		
}
catch (Exception  $e)
{
	echo 'EXCEPTION';
	$detalle= "se produjo excepcion al procesar archivo:".$e->getMessage();
	$retorno= false;
}
	
	
$obj= new StructStatus();
$obj->setStatus($retorno);
$obj->setInformeDetalle($detalle);

return $obj;

} 

function pasaValidacionesPorTipo($Conn, $SafeFile, $tFile, $comboGPS, $comboESB)
{

$ret = true;
$detalle="";
//0: GPS, 1:ESSBASE
global $anioCalcGPS;

if ($tFile == 0 )
{
	if ($comboGPS=="M") //Mensual
	{
	//contar meses distintos de archivo si es mayor o igual a 2 regresar error  , EN EL CAMPO 5 VIENE LA FECHA
	$sql = "   select count(distinct(substr(tx_campo5, 4,2))) from tbl41_paso_archivos  " ;
	$result = mysqli_query($Conn, $sql);
	$row = mysqli_fetch_row($result);
	$count = $row[0];	
    $ret= ($count >=2)? false: true ;
    $detalle= ($count >=2)? "el archivo posee mas de un mes, y selecciono tipo mensual": " ok " ;
	}

	$sql = "   select count(distinct(substr(tx_campo5, 7,4))) from tbl41_paso_archivos  " ;
	$result = mysqli_query($Conn, $sql);
	$row = mysqli_fetch_row($result);
	$count = $row[0];	
	if ($count >=2)
	{
    $ret=  false ;
    $detalle= "el archivo GPS posee informacion de mas de un año (Solo debe contener un año) ";
	}
	else
	{
		$ret=  true ;
		$detalle= "el archivo GPS posee informacion de  $count año ";
		$sql = "   select distinct(substr(tx_campo5, 7,4)) from tbl41_paso_archivos  " ;
		$result = mysqli_query($Conn, $sql);
		$row = mysqli_fetch_row($result);
		$anio = $row[0];
    	$anioCalcGPS = floor($anio);
		  
	}
	
}

	
	
	
	
$obj= new StructStatus();
$obj->setStatus($ret);
$obj->setInformeDetalle($detalle);
return $obj;
}


function processInTableFinal ($Conn, $SafeFile, $tFile, $anioInf)
{
	
	global $cuentaCabeceraEBS;
	$retorno= true;
	$detalle="";

try 
{
	
	//obtener Id Archivo de tabla
	$sql = " SELECT id_archivo  FROM tbl40_archivos_upload where tx_archivo = '$SafeFile'"; 
	$result = mysqli_query($Conn, $sql);
	$row = mysqli_fetch_row($result);
	$idArchivo = $row[0];	
		
	//Si es tipo GPS
	if ($tFile==0)
	{

		
		$subquery= " select id from tbl45_catalogo_global  WHERE ";  
 		$subquery.=" substr( tx_clave,1,instr(tx_clave ,'-')-1) = 'CUENTA_CONTABLE'   AND TX_INDICADOR=1 AND "; 
 		$subquery.=" TX_VALOR = tx_campo2 "; 
 
		//ejecuta insert masivo , tomar todo lo de la tabla y colocarlo en tbl_gps
		$sql2= "insert into tbl42_gps ";
		$sql2.="        (           in_anio,             id_mes           ,  tx_nombre , tx_cta_gps, tx_cuenta_local,       tx_cr                , tx_fecha_contable, tx_clase, tx_numero_doc,       tx_referencia, tx_ct  , tx_moneda, im_monto_destino, im_monto_local , tx_indicador, id_archivo , id_cuenta_contable)";
		$sql2.=" select   substr(tx_campo5,7,4),    substr(tx_campo5,4,2),  tx_campo1, tx_campo2    , tx_campo3         , substr(tx_campo4,7,4 ), tx_campo5          , tx_campo6  ,  tx_campo7      , tx_campo8       , tx_campo9, tx_campo10  , tx_campo11         , tx_campo12        ,  tx_campo13     , ".$idArchivo." , ( $subquery ) " ;  
		$sql2.= " from  tbl41_paso_archivos ";

		if (mysqli_query($Conn, $sql2))
		{
			$retorno= true;
			$detalle="insert masivo exitoso";
		}
	 	else
	 	{
			$retorno= false;
			$detalle="no se pudo ejecutar query masivo";
	 	}
	}
	else //Tipo ESSBASE 
	{
		$meses_procesados=0;
		$meses_sin_procesar=0;
	
		for($index=1; $index<=12; $index++)
		{
		$mes=$index;
		$campo=$index+1;
		$sql2="insert into tbl43_essbase(in_anio,  id_mes , tx_cr  , tx_descripcion , im_monto, fh_alta, id_usuario_alta, id_archivo, id_cuenta_contable) ";
		$sql2.=" select ".$anioInf." , ".$index.",  substr(ltrim(tx_campo1),1,4),  substr( tx_campo1,5, CHAR_LENGTH(tx_campo1)-4 ) ,  -1*tx_campo".$campo." , CURRENT_TIMESTAMP ,1 ,".$idArchivo." , $cuentaCabeceraEBS    from tbl41_paso_archivos ";
		
		
		if (mysqli_query($Conn, $sql2))
			$meses_procesados++;
	 	else
			$meses_sin_procesar++;
			
		}
		
		$retorno= true;
		$detalle= " <font color=blue > meses procesados: ".$meses_procesados." </font>, <font color=red >meses sin procesar:".$meses_sin_procesar." </font> ";
	}
}
catch (Exception  $e)
{
	echo 'EXCEPTION';
	$detalle= "se produjo excepcion al procesar archivo:".$e->getMessage();
	$retorno= false;
}

$obj= new StructStatus();
$obj->setStatus($retorno);
$obj->setInformeDetalle($detalle);

return $obj;

}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
<title> Upload File</title>
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


$(document).ready(function() 
							{

	 							$("#btnBack").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
	 							$("#btnAyuda").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
							}
				);

function enviarPopUp(sDestinoURL)
{
window.open(sDestinoURL,"Ayuda","toolbar=no,menubar=no,location=no,directories=no,menubar=no,resizable=yes,scrollbars=yes,width=500,height=650");
}

</script>
</head>
<body>


<table border="0"  cellpadding="1" cellspacing="1" align="center" width="100%">


		<tr><td colspan="3"> <div class="ui-state-default align-center" >INFORME DE CARGA</div>	</td></tr>
        
        
<tr><td valign="top" width="90%" align="right">

<a id="btnBack" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="cat_upload_file.php?<?php ECHO $urlpermisos?>" style="font-size:smaller;" title="Cargar" >
		                    	Ir a Carga
		                    	<span class="ui-icon ui-icon-disk"></span>
		                        </a>

<a id="btnAyuda" class="fm-button ui-widget-header ui-corner-all fm-button-icon-left" href="javascript:enviarPopUp('html/help_load.htm')"  style="font-size:smaller;" title="Ayuda" >
                Ayuda
                <span class="ui-icon ui-icon-info"></span>                </a>	
                
</td></tr>
<tr><td valign="top" width="90%" align="center">


		<table border="0"  cellpadding="2" cellspacing="2" align="center" width="60%" >
		<tr><td colspan="3">&nbsp; 	</td></tr>
		<tr>
		  <td colspan="2" align="left"> <!--    tipo file:  <? echo $tipofile?> anioInf:   <? echo $anioInf?>  tipoInf : <? echo $tipoInf?>  -->  </td>
		</tr>
		
		<?php 
		
							$obj0=new StructStatus();
							$obj0=isValidFile($SafeFile, $anioInf , $tipofile );
							$ret=$obj0->getStatus();
							$myDetalle=$obj0->getInformeDetalle();
														
							if (!$ret)
							{
								echo "<tr><td align='left' bgcolor='#dddddd'> Validando nombre archivo ... ...$myDetalle ...</td><td bgcolor='#dddddd'>$NOK</td></tr>";
								$aviso="EL ARCHIVO NO PASA LAS VALIDACIONES ";
							}
							else
							{
								echo "<tr><td align='left' bgcolor='#dddddd'> Validando nombre archivo  ... ...$myDetalle ... </td><td bgcolor='#dddddd'>$OK</td></tr>";
								if (isFileLoaded($mysql,$SafeFile))
								{
									echo "<tr><td  align='left' bgcolor='#dddddd'> Validando unicidad de archivo ... ...Archivo existente ... </td><td bgcolor='#dddddd'>$NOK</td></tr>";
									$aviso="ESTE ARCHIVO YA ESTA CARGADO EN EL SERVIDOR, CARGUE OTRO Ó CAMBIE EL NOMBRE";
								}
								else
								{
									echo "<tr><td  align='left' bgcolor='#dddddd'> Validando unicidad de archivo ......</td><td bgcolor='#dddddd'>$OK</td></tr>";
									
									$obj0=isUpLoadedOK($HTTP_POST_FILES['userfile']['tmp_name'] , $path);
									$ret=$obj0->getStatus();
									$myDetalle=$obj0->getInformeDetalle();
							
									
									if (!$ret)
									{
										echo "<tr><td  align='left' bgcolor='#dddddd'> Enviando archivo por HTTP a servidor remoto ... ..$myDetalle ... </td><td bgcolor='#dddddd'>$NOK</td></tr>";
										$aviso="EL ARCHIVO NO PUDO ENVIARSE AL SERVIDOR ";
									}
									else
									{
										echo "<tr><td  align='left' bgcolor='#dddddd'> Enviando archivo por HTTP a servidor remoto ... ...$myDetalle ... </td><td bgcolor='#dddddd'>$OK</td></tr>";
										$idArchivo=registrarArchivo($mysql,$SafeFile,$tipofile,$id_login);
										
										if ( $idArchivo==0)
										{
											echo "<tr><td  align='left' bgcolor='#dddddd'> Registrando archivo en servidor remoto ... ...Error ... </td><td bgcolor='#dddddd'>$NOK</td></tr>";
											$aviso="El archivo no pudo registrarse en servidor remoto. ";
										}
										else
										{
											echo "<tr><td  align='left' bgcolor='#dddddd'> Registrando archivo en servidor remoto ... ...registrado ... </td><td bgcolor='#dddddd'>$OK</td></tr>";
												if (!limpiarTablaPaso ($mysql))
												{
													echo "<tr><td  align='left' bgcolor='#dddddd'>Limpiando &aacute;rea temporal de carga ...... </td><td bgcolor='#dddddd'>$NOK</td></tr>";
													$aviso="NO SE PUDO LIMPIAR LA TABLA DE PASO, REINTENTE ...  ";
												}
												else
												{
													echo "<tr><td  align='left' bgcolor='#dddddd'>Limpiando &aacute;rea temporal de carga ... ... </td><td bgcolor='#dddddd'>$OK</td></tr>";
														
														$obj2=new StructStatus();
														$obj2=processInTablePass($mysql,$data,$SafeFile,$tipofile );
														$ret=$obj2->getStatus();
														$myDetalle=$obj2->getInformeDetalle();
														
													if ( !$ret )
													{
														echo "<tr><td valign='top'  align='left' bgcolor='#dddddd'>Procesando a paso... $myDetalle</td><td valign='top' bgcolor='#dddddd'>$NOK</td></tr>";
														$aviso="EL ARCHIVO NO PUDO PROCESARSE POR INCOMPATIBILIDAD DE DATOS ";
													}
													else {
															echo "<tr><td  valign='top'  align='left' bgcolor='#dddddd'>Procesando ... $myDetalle</td><td valign='top' bgcolor='#dddddd'>$OK</td></tr>";
															$textoTipo=($tipofile==0)?"GPS":"ESSBASE";
															
															$obj2=new StructStatus();
															$obj2=pasaValidacionesPorTipo($mysql,$SafeFile, $tipofile, $tipoInf , $anioInf) ;
															$ret=$obj2->getStatus();
															$myDetalle=$obj2->getInformeDetalle();
														
															if (!$ret )
															{
																echo "<tr><td  align='left' bgcolor='#dddddd'>Validando Layout  $textoTipo ... $myDetalle ...</td><td bgcolor='#dddddd'>$NOK</td></tr>";
																$aviso=" EL ARCHIVO NO PASA VALIDACIONES PROPIAS DEL LAYOUT $textoTipo .";
															}
															
															else
																{		echo "<tr><td  align='left' bgcolor='#dddddd'>Validando Layout  $textoTipo ... $myDetalle ...</td><td bgcolor='#dddddd'>$OK</td></tr>";
																
																			$obj2=new StructStatus();
																			$obj2= processInTableFinal($mysql,$SafeFile, $tipofile, $anioInf) ;
																			$ret=$obj2->getStatus();
																			$myDetalle=$obj2->getInformeDetalle();
															
																		if (! $ret )
																		{
																			echo "<tr><td  align='left' bgcolor='#dddddd'> Subiendo archivo a tabla final  ... $myDetalle...</td><td bgcolor='#dddddd'>$NOK</td></tr>";
																			$aviso="EL ARCHIVO NO PUDO DESGLOSARSE A LA TABLA FINAL. ";
																		}
																		else
																		{	
																			echo "<tr><td  align='left' bgcolor='#dddddd'> Subiendo archivo a tabla final  ...$myDetalle ...</td><td bgcolor='#dddddd'>$OK</td></tr>";
																			
																			if (!actualizarArchivoOK($mysql,$idArchivo,$tipofile,$anioInf))
																			{
																				echo "<tr><td  align='left' bgcolor='#dddddd'> Actualizando archivo a procesado  ... </td><td bgcolor='#dddddd'>$NOK</td></tr>";
																				$aviso="EL ARCHIVO NO PUDO ACTUALIZARSE A ESTATUS PROCESADO, REINTENTE ... ";
																			}
																			else
																			{
																				$aviso="SE HA COMPLETADO LA CARGA EXITOSAMENTE";
																				$error=false;
																				
																			} 
																			
																			
																		}
																}		
														}
												}
										
										}	
									}
								}
							}
							
					
			
		
		
		
		if ($error==true)
		$color="red";
		else
		$color="blue";
		
			$vinculoXls="javascript: enviarPopUp('inf_viewfilexls.php?idArchivo=$idArchivo&tipofile=$tipofile'); ";
		?>
		
			<tr><td colspan="3">&nbsp;   </td></tr>
			<tr><td colspan="3" align="center" bgcolor="#dddddd">		<b><font color="<?echo $color ?>" >	
			 <a href='#' style='cursor:pointer' onclick="<?echo $vinculoXls?>"  >
             	<img src="images/iconxls.gif" border="0">	
             </a>
			<?php echo $aviso ?>	</font></b>	</td>	</tr>
		
		
		                       
		</table>
		
		</td>

</tr>
</table>
	
</body>
</html>

<?php 
}
mysqli_close($mysql);
?>
	