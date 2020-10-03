<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 

session_start();
if 	(isset($_SESSION['sess_user']))
{
?>

<div id="menuBarContainer" style="width:99%">
</div>

<script type="text/javascript">

$("#divContent").html("");	

//myLayout.close('west');
myLayout.open('west');

// First menu
//\u00e1 -> á \u00e9 -> é \u00ed -> í \u00f3 -> ó \u00fa -> ú \u00c1 -> Á \u00c9 -> É \u00cd -> Í \u00d3 -> Ó \u00da -> Ú \u00f1 -> ñ \u00d1 -> Ñ 

var menuModel = new DHTMLSuite.menuModel();
DHTMLSuite.commonObj.setCssCacheStatus(false);

menuModel.addItem(10,'Cat\u00e1logos','','',false,'Men\u00fa para mantenimiento de catalogos','');
menuModel.setSubMenuWidth(10,100);
menuModel.addItem(101,'A&ntilde;os','','',10,'A&ntilde;os','loadHtmlAjax(true, $("#divContent"), "cat_anios.php")');
menuModel.addItem(102,'Usuarios','','',10,'Usuarios','loadHtmlAjax(true, $("#divContent"), "cat_usuarios.php")');
menuModel.addItem(103,'Perfiles','','',10,'Perfiles','loadHtmlAjax(true, $("#divContent"), "cat_perfiles.php")');
menuModel.addItem(104,'Entidad','','',10,'Entidad','loadHtmlAjax(true, $("#divContent"), "cat_entidades.php")');
menuModel.addItem(105,'Direcciones','','',10,'Direcci\u00f3nes','loadHtmlAjax(true, $("#divContent"), "cat_direcciones.php")');
menuModel.addItem(106,'Subdirecciones','','',10,'Subdirecciones','loadHtmlAjax(true, $("#divContent"), "cat_subdirecciones.php")');
menuModel.addItem(107,'Departamentos','','',10,'Departamentos','loadHtmlAjax(true, $("#divContent"), "cat_departamentos.php")');
menuModel.addItem(108,'Opciones del Men&uacute;','','',10,'Opciones del Men&uacute;','loadHtmlAjax(true, $("#divContent"), "cat_opciones.php")');
menuModel.addItem(109,'Configuraci\u00f3n Perfil','','',10,'Configuraci\u00f3n Perfil','loadHtmlAjax(true, $("#divContent"), "cat_perfiles_config.php")');
menuModel.addItem(110,'Centro de Costos','','',10,'Centro de Costos','loadHtmlAjax(true, $("#divContent"), "cat_centro_costos.php")');

//menuModel.addItem(108,'Configuraci\u00f3n Perfil - 1','','',10,'Configuraci\u00f3n Perfil - 1','loadHtmlAjax(true, $("#divContent"), "cat_perfiles_config_direcciones.php")');

menuModel.addSeparator();

menuModel.addItem(20,'Gesti\u00f3n del Inventario','','',false,'Men\u00fa de Gesti\u00f3n del Inventario','');
menuModel.setSubMenuWidth(20,200);
menuModel.addItem(201,'Servicios','','',20,'Inventario','loadHtmlAjax(true, $("#divContent"), "Inventario.php")');
menuModel.addSeparator();

menuModel.addItem(30,'Gesti\u00f3n del Gasto','','',false,'Men\u00fa de Gesti\u00f3n del Gasto','');
menuModel.setSubMenuWidth(30,300);
menuModel.addItem(301,'Facturaci\u00f3n','','',30,'Facturaci\u00f3n','loadPageHTML("Facturas.php","",$("#contenido"));');
//menuModel.addItem(302,'Informes','','',30,'Informes','loadPage("FAResumen.php?dispatch=insert&amp;sid=$sid","contenido",showContent);');
menuModel.addSeparator();

menuModel.addItem(40,'Informes','','',false,'Men\u00fa de Informes','');
menuModel.setSubMenuWidth(40,400);
menuModel.addItem(401,'Servicios de Informaci\u00f3n','','',40,'Servicios de Informaci\u00f3n','loadPageHTML("IResumenInventario.php","",$("#contenido"));');
menuModel.addItem(402,'Gasto y Facturaci\u00f3n','','',40,'Gasto y Facturaci\u00f3n','loadPageHTML("IResumenGasto.php","",$("#contenido"));');
menuModel.addItem(403,'Matriz de Equipamiento','','',40,'Matriz de Equipamiento','loadPageHTML("IResumenMatriz.php","",$("#contenido"));');
menuModel.addSeparator();

menuModel.addItem(50,'Motor de Consulta','','',false,'Men\u00fa de Informes Ejecutivos','');
menuModel.setSubMenuWidth(50,500);
menuModel.addItem(501,'Servicios de Informaci\u00f3n','','',50,'Servicios de Informaci\u00f3n','loadPageHTML("IEjecutivoInventario.php","",$("#contenido"));');
menuModel.addItem(502,'Equipamiento','','',50,'Equipamiento','loadPageHTML("IEjecutivoEquipamiento.php","",$("#contenido"));');

menuModel.addSeparator();
    
menuModel.addItem(70,'Ayuda','','',false,'Men\u00fa para consulta ayuda en linea','');
menuModel.setSubMenuWidth(70,700);
menuModel.addItem(701,'Acerca de ...','','',70,'Acerca de la aplicaci\u00fan','loadPage("Prueba.php?","contenido",validLoad)');
menuModel.addSeparator();

menuModel.addItem(80,'Salir','','',false,'Salir de la aplicaci\u00f3n','logout()');
//menuModel.addItem(80,'Salir','','',false,'Salir de la aplicaci\u00f3n','window.close();');
//menuModel.addItem(80,'Salir','','',false,'Salir de la aplicaci\u00f3n','window.open("inicio.php", "_self");');
//menuModel.addItem(80,'Salir','','',false,'Salir de la aplicaci\u00f3n','salir()');

menuModel.init();

var menuBar = new DHTMLSuite.menuBar();
menuBar.addMenuItems(menuModel);
menuBar.setTarget('menuBarContainer');
menuBar.init();
</script>
<div id="divEspacios"><br/><br/><br/><br/>
<div id="divTitleApp" class="ui-state-default" style="font-family:Georgia,'Times New Roman',times,serif;">Bienvenido !</div>
</div>
<?
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>

