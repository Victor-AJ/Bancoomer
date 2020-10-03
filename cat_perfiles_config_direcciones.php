<?
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Pragma: no-cache"); 
	
	$tx_p1	= $_GET['tx_p1']; //consultar
	$tx_p2	= $_GET['tx_p2']; //insertar
	$tx_p3	= $_GET['tx_p3']; //actualziar
	$tx_p4	= $_GET['tx_p4']; //borrar
	$tx_p5	= $_GET['tx_p5']; //exportar


?>	
<script type="text/javascript">

	//$("#divEspacios").html("");	
    // Variables locales para el grid
    var lastsel2;
    var adding2 = false;
	var editing2 = false;
	
	//$('#tabs').tabs();	

     function edicion2(editing2)
	 
	 {
	 
		//alert("f edicion2 (" + editing2  + ")");
		var pdir2= $('#tx_pd2').val();
	 	var pdir3= $('#tx_pd3').val();
	 	var pdir4= $('#tx_pd4').val();
	 	var pdir5= $('#tx_pd5').val();
		
		//alert("pdirs:" + pdir2 + "," + pdir3 + "," + pdir4 + "," + pdir5);
		
        if(editing2)
		{
            $("#btnSave2").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnUndo2").removeClass('ui-state-disabled').removeAttr("disabled");
			
            $("#btnNew2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnEdit2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnDelete2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnExport2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnPin2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#search_list2").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#refresh_list2").addClass('ui-state-disabled').attr("disabled","disabled");			
   			$("#view_list2").addClass('ui-state-disabled').attr("disabled","disabled");
        }
		else
		{
			editing=false;
			
			$("#btnSave2").addClass('ui-state-disabled').attr("disabled","disabled");
			$("#btnUndo2").addClass('ui-state-disabled').attr("disabled","disabled");
			
			if (pdir2==0)
	            $("#btnNew2").addClass('ui-state-disabled').attr("disabled","disabled");
			else
				$("#btnNew2").removeClass('ui-state-disabled').removeAttr("disabled");
			
			if (pdir3==0)
				$("#btnEdit2").addClass('ui-state-disabled').attr("disabled","disabled");
			else
				$("#btnEdit2").removeClass('ui-state-disabled').removeAttr("disabled");
			
			if (pdir4==0)
    	        $("#btnDelete2").addClass('ui-state-disabled').attr("disabled","disabled");
			else
	            $("#btnDelete2").removeClass('ui-state-disabled').removeAttr("disabled");
			
	
	           $("#btnExport2").removeClass('ui-state-disabled').removeAttr("disabled");
    
	        $("#btnPin2").removeClass('ui-state-disabled').removeAttr("disabled");
			$("#search_list2").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#refresh_list2").removeClass('ui-state-disabled').removeAttr("disabled");
			$("#view_list2").removeClass('ui-state-disabled').removeAttr("disabled");			
        }  
    }

    function createButtons2(){
        var cadena = "<table class='ui-pg-table navtable' cellspacing='0' cellpadding='0' border='0' style='float: left; table-layout: auto;padding:2px;'><tbody><tr>";
        cadena += addButton2("btnNew2","ui-icon-plus","Agregar nueva fila","Agregar");
        cadena += addButton2("btnEdit2","ui-icon-pencil","Modificar fila seleccionada","Modificar");
        cadena += addButton2("btnSave2","ui-icon-disk","Guardar fila seleccionada","Guardar");
        cadena += addButton2("btnUndo2","ui-icon-arrowreturnthick-1-w","Descartar los cambios","Deshacer");
        cadena += addButton2("btnDelete2","ui-icon-trash","Eliminar fila seleccionada","Eliminar");
        cadena += addButton2("btnExport2","ui-icon-suitcase","Exportar los datos de la tabla","Exportar");
        cadena += "</tr></tbody></table>";
        return cadena;
    }

    function addButton2(idbtn,icon,title,name){
        return "<td id='"+idbtn+"' class='ui-pg-button ui-corner-all border-button' title='"+title+"' style='cursor: pointer;'><div class='ui-pg-div'><span class='ui-icon "+icon+"'/>"+name+"</div></td>";
    }

    function addButtonEvents2(){
        $("#btnNew2").click(function(){
            if(!editing2){
                var datarow = {id:"0",perfil:"",direccion:"",tx_indicador:"ACTIVO",fecha_mod:"",idusuario_mod:"",fecha_alta:"",idusuario_alta:""};
                var su=jQuery("#list2").addRowData(0,datarow);
                jQuery("#list2").setSelection(0,false);
                jQuery("#list2").editRow(0,true);
                lastsel2 = 0;
                editing2 = true;
                adding2 = true;
                edicion2(editing2);
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        // Funcion click Editar
        $("#btnEdit2").click(function(){
            if(!editing2){
                var gr = jQuery("#list2").getGridParam('selrow');
                if(gr != null){
                    jQuery("#list2").editRow(gr,false);
                    lastsel2 = gr;
                    editing2 = true;
                    adding2 = false;
                    edicion2(editing2);
                }else {
                    var fAceptar = function(){
                        $('#dialogMain').dialog("close");
                    }
                    jAlert(true,true,"Debe seleccionar una fila",fAceptar);
                }
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });
		
		function fieldsReq2(){			
		
			var id = jQuery("#list2").getGridParam('selrow');
			if (id==null) id="0";
			
			var va_perfil='#'+id+'_perfil';
			var va_direccion='#'+id+'_direccion';			
			
			//alert($(va_usuario).val());	
			var error = true;
			validSelect($(va_perfil), $("#divError2")); 
			validSelect($(va_direccion), $("#divError2")); 			
			
			
			if ( !validSelect($(va_perfil), $("#divError2")) || !validSelect($(va_direccion), $("#divError2"))) error=false;			
			
			return error;
		}
		
		$("#btnSave2").click(function(){
			
			if(fieldsReq2()){	
			 	if(editing2){
					var id = jQuery("#list2").getGridParam('selrow');
					if (id) {
            			var ret = jQuery("#list2").getRowData(id);            		
        			}				
				
					if (adding2) var dispatch ="insert";
					else var dispatch ="save";								
                	
				 	var url = "process_perfil_direcciones.php?dispatch="+dispatch+"&id="+id+"&";
                	url += $("#catalogForm2").serialize();	
					//alert(url);
				
					jQuery("#list2").saveRow(lastsel2,false,'clientArray');
                	editing2 = false;
                	adding2 = false;
                	edicion2(editing2);
					
					var func = function(data){					   			
						var fAceptar = function(){
							$('#dialogMain').dialog("close");
						}
						if(data.error == true){						
							if(data.message != null){							
								jAlert(true,true,data.message,fAceptar);
							}else{
								logout();
							}
						} else {						
						 	if(data.message != null){							
								jAlert(true,false,data.message,fAceptar);
								jQuery("#list2").trigger("reloadGrid");
							}
						}	
					}	
					//alert (url);						
					executeAjax("post", false ,url, "json", func);					
            	}
			} else {
           		var fAceptar = function(){
                	$('#dialogMain').dialog("close");
                }
                jAlert(true,true,"Existen campos obligatorios vac&iacute;os",fAceptar);
           }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        }); 

        // Funcion click Deshacer
        $("#btnUndo2").click(function()	{
											$("#divError2").hide();
								            if(editing2)	
												{
								                	jQuery("#list2").restoreRow(lastsel2);
													if (adding2) 
														jQuery("#list2").trigger("reloadGrid");
														
													$(this).removeClass("ui-state-hover");
													
								                	editing2 = false;
								                	adding2 = false;
								                	edicion2(editing2);
									            }
								        }
							).hover(function(){ $(this).addClass("ui-state-hover");  },function(){  $(this).removeClass("ui-state-hover"); });
							
							

        // Funcion click Borrar
        $("#btnDelete2").click(function(){
            if(!editing2){        
				//alert("Click para Borrar");        
                var gr = jQuery("#list2").getGridParam('selrow');
                var fAceptar = function(){
					
                    var gr = jQuery("#list2").getGridParam('selrow');									
						
					
                    //jQuery("#list2").delRowData(gr);
                    //$('#dialogMain').dialog("close");
					
					var func = function(data){					   			
						var fAceptar = function(){
							$('#dialogMain').dialog("close");
						}
						if(data.error == true){
						
							if(data.message != null){							
								jAlert(true,true,data.message,fAceptar);
							}else{
								logout();
							}
						}else{
						 //alert (data.message);	
						 	if(data.message != null){							
								jAlert(true,false,data.message,fAceptar);
								jQuery("#list2").trigger("reloadGrid");
							}
						}	
					}	
															
					var url = "process_perfil_direcciones.php?dispatch=delete&id="+gr;
					//alert (url);
					executeAjax("post", false ,url, "json", func);
					
                }
                var fCancelar = function(){
                    $('#dialogMain').dialog("close");
                }
                if( gr != null ){
                    jConfirm(true,"\u00bfDesea eliminar el registro "+ gr +" seleccionado", fAceptar, fCancelar);
                }else{
                    jAlert(true,true,"Por favor.. Seleccione una fila",fCancelar)
                };
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        // Funcion click Exportar
        $("#btnExport2").click(function(){
            if(!editing2){
                alert("Exportar pendiente");
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });
    }

</script>
<form id="catalogForm2" method="" action="">

 	<input id="tx_pd1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
	<input id="tx_pd2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
    <input id="tx_pd3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
    <input id="tx_pd4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
    <input id="tx_pd5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
    
    
    
	<div id="divError2"></div>         
    <div id="errorGrid2" style="display:none">
    	<div id="errorContent2" class="ui-corner-all" style="padding: 0pt 0.7em;" ></div>
   	</div>
    <table id="list2" class="scroll" cellpadding="0" cellspacing="0">
    	<tr><td style="border:1px black solid"></td></tr>
    </table>
    <div id="pager2" class="scroll" style="text-align:center;"></div>
    <script type="text/javascript">
    	//jQuery(document).ready(function(){
		(function($){				
        	//var mygrid = jQuery("#list2").jqGrid({
			jQuery("#list2").jqGrid({
            	caption:"Configuraci&oacute;n del Perfil con las Direcciones Corporativas",
                mtype: "GET",                       
				url:'process_perfil_direcciones.php?dispatch=load',
				//alert ("Envio"+url);
                editurl:'process_perfil_direcciones.php?dispatch=delete&id',
                datatype: "json",
                colNames:['Edo.','Id','Perfil','Direcci&oacute;n','Indicador','Fecha modifica', 'Usuario modifica','Fecha alta','Usuario alta'],
                colModel:[   
                	{name: 'edo', index: 'edo', width: 20, align:"center", sortable: false,editable: false , search: false },
                    {name:'id',index:'id_perfil_direccion',width:20, align:"center", editable:false, editoptions:{readonly:true,size:10}},
					{name:'perfil',index:'b.tx_nombre',width:200, align:"left", editable:true, formoptions:{label: "Perfil", rowpos:1},
						edittype:'select',
   						editoptions:{dataUrl:'cat_carga.php?dispatch=perfil', defaultValue:'id_perfil'},	
						searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}},   
                    {name:'direccion',index:'c.tx_nombre',width:250, align:"left", editable:true, formoptions:{label: "Direcci&oacute;n", rowpos:2},
						edittype:'select',
   						editoptions:{dataUrl:'cat_carga.php?dispatch=direccion', defaultValue:'id_direccion'},	
						searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}},   						
					{name:'tx_indicador',index:'a.tx_indicador',width:80, align:"center",sortable: true, editable:true, formoptions:{label: "Indicador", rowpos:7},
                        
                        edittype:"select",
						stype:"select",
						search: true,
						searchtype: "number" ,
                        editoptions:{value:"1:ACTIVO;0:INACTIVO", size:10},
                        editrules:{required:true},                                
                        searchoptions:{ 
										value : "1:ACTIVO;0:INACTIVO"
										,
			  							sopt:['eq','ne']
										}
																												

						
					},                                                                          
                    {name:'fh_mod',index:'a.fh_mod',width:110, align:"center", editable:false, editoptions:{readonly:true,size:10},
                        searchoptions: { dataInit:function(el){$(el).datepicker({dateFormat:'yy/mm/dd'});}
											,
   										 sopt: ['eq','ne','lt','le','gt','ge']
									 	} 
					},
                    {name:'id_usuariomod',index:'d.tx_nombre',width:140, align:"left", editable:false, editoptions:{readonly:true, size:60} , searchoptions:{sopt:['eq','ne','cn','nc']}},
                    {name:'fh_alta',index:'a.fh_alta',width:110, align:"center", editable:false, editoptions:{readonly:true,size:60},
                        searchoptions:{dataInit:function(el2){$(el2).datepicker({dateFormat:'yy/mm/dd'});} 
										,
   										 sopt: ['eq','ne','lt','le','gt','ge']
										}
						},
                    {name:'id_usuarioalta',index:'e.tx_nombre',width:140, editable: false, editoptions:{readonly:true,size:10}, searchoptions:{sopt:['eq','ne','cn','nc']} }
                ],
                pager: '#pager2', // Nombre del paginador
                altRows: true, // Activa la visualizacion de zebra en las filas
                imgpath: "/css/ui-personal/images",
                toolbar: [true,"bottom"], // Si cuenta con barra de herramienta y posicion de la misma
                rowNum:30,  // numero de filas por pagina
                rowList:[30,50,100],  // opciones de filas por pagina
                autowidth: true,    // Ancho automatico para columnas
				//width:$("#gview_list2").width(),
                //height:360,
				height:$("#CenterPane").height()-$("#NorthPane").height()-190,
				shrinkToFit :false,
            	sortable: true,
                gridview: true,     // Mejora rendimiento para mostrar datos. Algunas funciones no estan disponibles
                rownumbers: true,   // Muestra los numeros de linea en el grid
                viewrecords: true,  //
                viewsortcols: [true,'vertical',true], // Muestra las columnas que pueden ser ordenadas dinamicamente
                sortname: 'b.tx_nombre', // primer columna de ordenacion
                sortorder: 'asc',       // tipo de ordenacion inicial                        
                onSelectRow: function(id){
                	if(!editing2){
                    	lastsel2 = id;								
                    }
                },
                loadComplete: function(){
                	var ids = jQuery("#list2").getDataIDs();
                    for(var i=0;i<ids.length;i++){
                    	indica = jQuery("#list2").getCell(ids[i],"tx_indicador");
                        if(indica == '1'){
                        	be = "<img style='cursor:pointer' border='none' src='images/greenball.png'>";
							ind = "ACTIVO";
                        }else{
                        	be = "<img style='cursor:pointer' border='none' src='images/redball.png'>";
							ind = "INACTIVO";
                        }
                        jQuery("#list2").setRowData(ids[i],{edo:be})
						jQuery("#list2").setRowData(ids[i],{tx_indicador:ind})
                    }
               	},
               	loadError : function(xhr,st,err) {
                	var fAceptar = function(){
                    	$('#dialogMain').dialog("close");
                    }
                    jAlert(true,true,"Error cargando grid Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText,fAceptar);
                }
                }).navGrid('#pager2',                  
				{add:false,edit:false,view:true,del:false,search:true, refresh:true}, //options
                {}, // edit options
                {}, // add options
                {reloadAfterSubmit:false,jqModal:false, closeOnEscape:true}, // del options
                {closeOnEscape:true,multipleSearch:true,closeAfterSearch:true}, // search options                    
				{height:200,width:400,jqModal:false,closeOnEscape:true} // view options
                ).navButtonAdd('#pager2',{
                id:"btnPin2",
                   caption:"Mostrar",
                   title:"Mostrar/ocultar columnas",
                   buttonicon :'ui-icon-pin-s',
                   position:"first",
                   onClickButton:function(){
                   if(!editing2){
                    	jQuery("#list2").setColumns();
                   }
                }
                });

                $("#btnPin2").addClass("border-button");     // Se agrega borde a los botones
               	$("#search_list2").addClass("border-button");
					$("#view_list2").addClass("border-button");
                    $("#refresh_list2").addClass("border-button");
                    $("#t_list2").addClass("ui-jqgrid-pager");  // Se agrega estilo para que puedan funcionar estilos por default del grid button
                    $("#t_list2").height(22);                   // Se cambia alto toolbar
                    $("#t_list2").append(createButtons2());      // Se agregan los botones al toolbar
                    addButtonEvents2();
                    edicion2(false);
    	})(jQuery);                
	</script>
</form>    