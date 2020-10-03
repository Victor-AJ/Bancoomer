
<script type="text/javascript">
	$("#divContent").html("");	
</script>

<div id="MainMenu">
    <div id="tab">
        <ul>
            <li><a href="#" onMouseover="cssdropdown.dropit(this,event,'dropmenu_101')"><span>Cat&aacute;logos</span></a></li>
            <li><a href="#" onMouseover="cssdropdown.dropit(this,event,'dropmenu_102')"><span>Gesti&oacute;n del Inventario</span></a></li>
        </ul>
    </div>
</div>

<div id="dropmenu_101" class="dropmenudiv">
    <ul>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_usuarios.php')" title="Usuarios"><span>Usuarios</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_perfiles.php')" title="Perfiles"><span>Perfiles</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_entidades.php')" title="Perfiles"><span>Entidad</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_direcciones.php')" title="Perfiles"><span>Direccion</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_opciones.php')" title="Perfiles"><span>Opciones</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_perfiles_config.php')" title="Perfiles"><span>Configuracion</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_perfiles_config2.php')" title="Perfiles 2"><span>Configuracion 2</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'tabs.php')" title="tabs"><span>Tabs</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_departamentos.php')" title="Departamento"><span>Departamentos</span></a></li>
        <li><a href="#" onclick="loadHtmlAjax(true, $('#divContent'), 'cat_centro_costos.php')" title="Centro de Costos"><span>Centro de Costos</span></a></li>
        <li><a href="#" onclick='logout()' title="Centro de Costos"><span>Salir</span></a></li>
    </ul>
</div>

<div id="dropmenu_102" class="dropmenudiv">
    <ul>
        <li><a href="#" onclick="" title="Consulta en hist&oacute;rico del pipeline"><span>Histórico</span></a></li>
        <li><a href="#" onclick="" title="Bit&acora de auditor&iacute;a"><span>Bitácora</span></a></li>
    </ul>
</div>
