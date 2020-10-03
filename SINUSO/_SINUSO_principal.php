<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	
<?
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Pragma: no-cache"); 
   	header("Content-type: text/html; charset=ISO-8859-1");  
	
	include("includes/funciones.php");
	//require_once("includes/JSON.php");
	conexion_db();	
	//conexion_db_objeto();	
	
	//$json = new Services_JSON;
	
	//Recibe variables
  	//================
	
	//$txtlogin = trim($_POST['txtlogin']);	
	//$txtpassword = trim($_POST['txtpassword']);
	
	$txtlogin = trim($_GET['txtlogin']);
	$txtpassword = trim($_GET['txtpassword']);
	
	//echo "login",$txtlogin;
	//echo "<br>";
	//echo "password",$txtpassword; 
	
	$sql = " SELECT * ";
	$sql.= "   FROM usuario ";
	$sql.= "  WHERE usuario = '$txtlogin' ";		
	$sql.= "    AND contrasena = '$txtpassword' ";   
	
	//echo "sql",$sql;
					
	$busqueda = @mysql_query($sql);
	$num_busqueda = @mysql_num_rows($busqueda);
	
	if ($num_busqueda<1) 
	{							
		$data = "La información proporcionado no existe, Verique";				
		echo $data;  	
	} else {				
		//include("menu.php");	
		include("menu_nuevo.php");	
	}

mysql_close();
	
?> 
<br/><br/><br/><br/><br/>
<div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">Bienvenido !</div>
