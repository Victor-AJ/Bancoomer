<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

//$dispatch=$_GET["dispatch"];
//$dispatch="perfil";


include("includes/funciones.php");  
$mysql=conexion_db();
echo " prueba ";

//Carga el combo de perfiles
//===========================
if ($dispatch=="perfil") {
	echo "dispatch";

	$sql = "   SELECT id_perfil, tx_nombre ";
	$sql.= "     FROM tbl_perfil ";
	$sql.= " ORDER BY tx_nombre ";
	
	$result = mysqli_query($mysql, $sql);	
	//while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	//echo $result;
	
	while ($row = mysqli_fetch_array($result))
   	{	
		$TheCatalogo[] = array(	'id_perfil'=>$row["id_perfil"], 'tx_nombre'=>$row["tx_nombre"]	);
		echo $row["tx_nombre"];
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_perfil=$TheCatalogo[$i]['id_perfil'];		
			$tx_nombre=$TheCatalogo[$i]['tx_nombre'];	
			echo "<option value=$id_perfil>$tx_nombre</option>";	
	}
	echo "</select>"; 
} 
//Carga el combo de entidades
//===========================
else if ($dispatch=="entidad") {

	$sql = "   SELECT id_entidad, tx_nombre ";
	$sql.= "     FROM tbl_entidad ";
	$sql.= " ORDER BY tx_nombre ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
   	{	
		$TheCatalogo[] = array(
	   		'id_entidad'=>$row["id_entidad"],
	  		'tx_nombre'=>$row["tx_nombre"]
		);
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_entidad=$TheCatalogo[$i]['id_entidad'];		
			$tx_nombre=$TheCatalogo[$i]['tx_nombre'];	
			echo "<option value=$id_entidad>$tx_nombre</option>";	
	}
	echo "</select>"; 

}	

//Carga el combo de direcciones
//=============================
else if ($dispatch=="direccion") {

	$sql = "   SELECT id_direccion, tx_nombre ";
	$sql.= "     FROM tbl_direccion ";
	$sql.= " ORDER BY tx_nombre ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
   	{	
		$TheCatalogo[] = array(
	   		'id_direccion'=>$row["id_direccion"],
	  		'tx_nombre'=>$row["tx_nombre"]
		);
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_direccion=$TheCatalogo[$i]['id_direccion'];		
			$tx_nombre=$TheCatalogo[$i]['tx_nombre'];	
			echo "<option value=$id_direccion>$tx_nombre</option>";	
	}
	echo "</select>"; 

}	

//Carga el combo de opcion
//=============================
else if ($dispatch=="opcion") {

	$sql = "   SELECT id_opcion, tx_nombre ";
	$sql.= "     FROM tbl_opcion ";
	$sql.= " ORDER BY tx_nombre ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
   	{	
		$TheCatalogo[] = array(
	   		'id_opcion'=>$row["id_opcion"],
	  		'tx_nombre'=>$row["tx_nombre"]
		);
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_opcion=$TheCatalogo[$i]['id_opcion'];		
			$tx_nombre=$TheCatalogo[$i]['tx_nombre'];	
			echo "<option value=$id_opcion>$tx_nombre</option>";	
	}
	echo "</select>"; 
}	
	
//Carga el combo de direcciones
//=============================
else if ($dispatch=="subdireccion") {

	$sql = "   SELECT id_subdireccion, tx_subdireccion ";
	$sql.= "     FROM tbl_subdireccion ";
	$sql.= " ORDER BY tx_subdireccion ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
   	{	
		$TheCatalogo[] = array(
	   		'id_subdireccion'=>$row["id_subdireccion"],
	  		'tx_subdireccion'=>$row["tx_subdireccion"]
		);
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_subdireccion=$TheCatalogo[$i]['id_subdireccion'];		
			$tx_subdireccion=$TheCatalogo[$i]['tx_subdireccion'];	
			echo "<option value=$id_subdireccion>$tx_subdireccion</option>";	
	}
	echo "</select>"; 

}	

//Carga el combo de anio
//=============================
else if ($dispatch=="anio") {

	$tx_anio = 2000;
	$anio_fin = 2020;
		
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=$tx_anio; $i < $anio_fin ; $i++)
	{   
		$tx_anio = $tx_anio + 1;      			 
		echo "<option value=$tx_anio>$tx_anio</option>";	
	}
	echo "</select>"; 
	
}	

//Carga el combo de paises
//=============================
else if ($dispatch=="pais") {

	$sql = "   SELECT id_pais, tx_pais ";
	$sql.= "     FROM tbl_pais ";
	$sql.= " ORDER BY tx_pais ";
	
	$result = mysqli_query($mysql, $sql);	
	while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
   	{	
		$TheCatalogo[] = array(
	   		'id_pais'=>$row["id_pais"],
	  		'tx_pais'=>$row["tx_pais"]
		);
   	} 	
	
	echo "<select>";
	echo "<option value='0'></option>";  
	for ($i=0; $i < count($TheCatalogo); $i++)
	{         			 
		while ($elemento = each($TheCatalogo[$i]))					  		
			$id_pais=$TheCatalogo[$i]['id_pais'];		
			$tx_pais=$TheCatalogo[$i]['tx_pais'];	
			echo "<option value=$id_pais>$tx_pais</option>";	
	}
	echo "</select>"; 
}	
else
	echo "sin dispatch";
				
mysqli_close($mysql);	
?>