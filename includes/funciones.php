<?

function conexion_db()
{
	include_once("acceso_ini.php");
	
	$mysql = mysqli_connect("$host","$login","$pass");	
	if (!$mysql) {
		echo "Problemas con la Conexión.  Notifiquelo al Administrador del Sistema";
		exit;
	}
	
	$mysql_bd = mysqli_select_db($mysql,"$db");	
	if (!$mysql_bd) {
		echo "Problemas con la Base de Datos.  Notifiquelo al Administrador del Sistema";
		exit;
	}	
	
	return $mysql;
}  

function conexion_db_a()
{
	require("acceso_ini.php");
	
    mysql_connect("$host","$login","$pass") OR DIE
    	("Problemas con la Conexión.  Notifiquelo al Administrador del Sistema." .mysqli_error());
    mysql_select_db("$db") OR DIE
     	("Problemas con la Base de Datos.  Notifiquelo al Administrador del Sistema." .mysqli_error()); 
}  

function conexion_db_objeto()
{
	require("acceso_ini.php");	
	
	@ $db = new mysqli("$host", "$login", "$pass", "$db");

  	if (mysqli_connect_errno()) {
     	echo 'Error: Problemas con la Conexión.  Notifiquelo al Administrador del Sistema.';
     	exit;
  	}
}
 
function ElColor($i)
{		  
	if ($i==0) $TheColor='#FFFFFF';
	else if ($i % 2 == 0) $TheColor='#eeeeee';
	else $TheColor='#FFFFFF';
		  
	return $TheColor;
}	
  
function cambiaf_a_normal($fecha)
{ 
	ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha); 
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1]; 
    return $lafecha; 
}
   
function cambiaf_a_mysql($fecha)
{ 
   	ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha); 
    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1]; 
    return $lafecha; 
}
  
function check_email($email) 
{     
	$check = preg_match("/^[A-z0-9]+@[a-z0-9]*(\.[A-z]{2,6})$/", $email);     
	if(!$check) 
	{return FALSE;} 
	else {return TRUE;} 
}  

/*! 
  @function num2letras () 
  @abstract Dado un numero lo devuelve escrito. 
  @param $num number - Numero a convertir. 
  @param $fem bool - Forma femenina (true) o no (false). 
  @param $dec bool - Con decimales (true) o no (false). 
  @result string - Devuelve el numero escrito en letra. 

*/ 
function num2letras($num, $mon, $fem = false, $dec = false) 
{ 
//if (strlen($num) > 14) die("El n?mero introducido es demasiado grande"); 
   $matuni[2]  = "DOS"; 
   $matuni[3]  = "TRES"; 
   $matuni[4]  = "CUATRO"; 
   $matuni[5]  = "CINCO"; 
   $matuni[6]  = "SEIS"; 
   $matuni[7]  = "SIETE"; 
   $matuni[8]  = "OCHO"; 
   $matuni[9]  = "NUEVE"; 
   $matuni[10] = "DIEZ"; 
   $matuni[11] = "ONCE"; 
   $matuni[12] = "DOCE"; 
   $matuni[13] = "TRECE"; 
   $matuni[14] = "CATORCE"; 
   $matuni[15] = "QUINCE"; 
   $matuni[16] = "DIECISEIS"; 
   $matuni[17] = "DIECISIETE"; 
   $matuni[18] = "DIECIOCHO"; 
   $matuni[19] = "DIECINUEVE"; 
   $matuni[20] = "VEINTE"; 
   $matunisub[2] = "DOS"; 
   $matunisub[3] = "TRES"; 
   $matunisub[4] = "CUATRO"; 
   $matunisub[5] = "QUIN"; 
   $matunisub[6] = "SEIS"; 
   $matunisub[7] = "SETE"; 
   $matunisub[8] = "OCHO"; 
   $matunisub[9] = "NOVE"; 

   $matdec[2] = "VEINT"; 
   $matdec[3] = "TREINTA"; 
   $matdec[4] = "CUARENTA"; 
   $matdec[5] = "CINCUENTA"; 
   $matdec[6] = "SESENTA"; 
   $matdec[7] = "SETENTA"; 
   $matdec[8] = "OCHENTA"; 
   $matdec[9] = "NOVENTA"; 
   $matsub[3]  = 'MILL'; 
   $matsub[5]  = 'BILL'; 
   $matsub[7]  = 'MILL'; 
   $matsub[9]  = 'TRILL'; 
   $matsub[11] = 'MILL'; 
   $matsub[13] = 'BILL'; 
   $matsub[15] = 'MILL'; 
   $matmil[4]  = 'MILLONES'; 
   $matmil[6]  = 'BILLONES'; 
   $matmil[7]  = 'DE BILLONES'; 
   $matmil[8]  = 'MILLONES DE BILLONES'; 
   $matmil[10] = 'TRILLONES'; 
   $matmil[11] = 'DE TRILLONES'; 
   $matmil[12] = 'MILLONES DE TRILLONES'; 
   $matmil[13] = 'DE TRILLONES'; 
   $matmil[14] = 'BILLONES DE TRILLONES'; 
   $matmil[15] = 'DE BILLONES DE TRILLONES'; 
   $matmil[16] = 'MILLONES DE BILLONES DE TRILLONES'; 
   
   $num = trim((string)@$num); 
   if ($num[0] == '-') { 
      $neg = 'menos '; 
      $num = substr($num, 1); 
   }else 
      $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ 
            $punt = true; 
            continue; 
         } 

      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { 
            if ($n != '0') $zeros = false; 
            $fra .= $n; 
         }else 
            $ent .= $n; 
      }else 
         break; 
   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' coma'; 
      for ($n = 0; $n < strlen($fra); $n++) { 
         if (($s = $fra[$n]) == '0') 
            $fin .= ' CERO'; 
         elseif ($s == '1') 
            $fin .= $fem ? ' UNA' : ' UN'; 
         else 
            $fin .= ' ' . $matuni[$s]; 
      } 
   }else 
      $fin = ''; 
   if ((int)$ent === 0) return 'CERO ' . $fin; 
   $tex = ''; 
   $sub = 0; 
   $mils = 0; 
   $neutro = false; 
   while ( ($num = substr($ent, -3)) != '   ') { 
      $ent = substr($ent, 0, -3); 
      if (++$sub < 3 and $fem) { 
         $matuni[1] = 'UNA'; 
         $subcent = 'AS'; 
      }else{ 
         $matuni[1] = $neutro ? 'UN' : 'UNO'; 
         $subcent = 'OS'; 
      } 
      $t = ''; 
      $n2 = substr($num, 1); 
      if ($n2 == '00') { 
      }elseif ($n2 < 21) 
         $t = ' ' . $matuni[(int)$n2]; 
      elseif ($n2 < 30) { 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = 'I' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      }else{ 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = ' Y ' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      } 
      $n = $num[0]; 
      if ($n == 1) { 
         $t = ' CIENTO' . $t; 
      }elseif ($n == 5){ 
         $t = ' ' . $matunisub[$n] . 'IENT' . $subcent . $t; 
      }elseif ($n != 0){ 
         $t = ' ' . $matunisub[$n] . 'CIENT' . $subcent . $t; 
      } 
      if ($sub == 1) { 
      }elseif (! isset($matsub[$sub])) { 
         if ($num == 1) { 
            $t = ' MIL'; 
         }elseif ($num > 1){ 
            $t .= ' MIL'; 
         } 
      }elseif ($num == 1) { 
         $t .= ' ' . $matsub[$sub] . '?N'; 
      }elseif ($num > 1){ 
         $t .= ' ' . $matsub[$sub] . 'ONES'; 
      }   
      if ($num == '000') $mils ++; 
      elseif ($mils != 0) { 
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
         $mils = 0; 
      } 
      	$neutro = true; 
    	$tex = $t . $tex; 
   	} 
   
   	if ($mon=="MXN") {
   		$mon_letra =" PESOS "; 
		$mon_letra_res = " M.N.";
	} else if ($mon=="USD") {
		$mon_letra =" DOLARES "; 
		$mon_letra_res = " USD";
	} else if($mon=="EUR") {
		$mon_letra =" EUROS "; 
		$mon_letra_res = " EUR";
    }

	if ($fra =='') $fra="00";
	else if ($fra < 10) $fra=$fra."0";
	else  {
		$fra = number_format($fra,0);	
		//$fra = substr($fra,0,2);	
	}	
	  
   	$tex = $neg . substr($tex, 1) . $fin; 
   	$pes = $mon_letra.$fra."/100".$mon_letra_res;      
   	$tex = $tex.$pes;
   
   	return ucfirst($tex); 
} 

function mes_nombre($i) {		  

	if ($i==1) 		$tx_mes='ENERO';
	elseif ($i==2) 	$tx_mes='FEBRERO';
	elseif ($i==3) 	$tx_mes='MARZO';
	elseif ($i==4) 	$tx_mes='ABRIL';
	elseif ($i==5) 	$tx_mes='MAYO';
	elseif ($i==6) 	$tx_mes='JUNIO';
	elseif ($i==7) 	$tx_mes='JULIO';
	elseif ($i==8) 	$tx_mes='AGOSTO';
	elseif ($i==9) 	$tx_mes='SEPTIEMBRE';	
	elseif ($i==10)	$tx_mes='OCTUBRE';
	elseif ($i==11)	$tx_mes='NOVIEMBRE';
	elseif ($i==12)	$tx_mes='DICIEMBRE';	
		  
	return $tx_mes;
}	
?>