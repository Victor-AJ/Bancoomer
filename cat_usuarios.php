<?
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Pragma: no-cache"); 
	
	$tx_p1	= $_GET['tx_p1']; 
	$tx_p2	= $_GET['tx_p2']; 
	$tx_p3	= $_GET['tx_p3'];
	$tx_p4	= $_GET['tx_p4']; 	
?>	
<script type="text/javascript">

	$("#divEspacios").html("");	
    // Variables locales para el grid
    var lastsel;
    var adding = false;

     function edicion(editing){	 	
	 	
		var p2= $('#tx_p2').val();
	 	var p3= $('#tx_p3').val();
	 	var p4= $('#tx_p4').val();
	 
	 
        if(editing){
            $("#btnSave").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnUndo").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnNew").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnEdit").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnDelete").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnExport").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnPin").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#search_list1").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#refresh_list1").addClass('ui-state-disabled').attr("disabled","disabled");			
   			$("#view_list1").addClass('ui-state-disabled').attr("disabled","disabled");
        }else{
            $("#btnSave").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnUndo").addClass('ui-state-disabled').attr("disabled","disabled");
			if (p2==0) $("#btnNew").addClass('ui-state-disabled').attr("disabled","disabled");
            else $("#btnNew").removeClass('ui-state-disabled').removeAttr("disabled");
            if (p3==0) $("#btnEdit").addClass('ui-state-disabled').attr("disabled","disabled");
			else $("#btnEdit").removeClass('ui-state-disabled').removeAttr("disabled");			
            if (p4==0) $("#btnDelete").addClass('ui-state-disabled').attr("disabled","disabled");
			else $("#btnDelete").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnExport").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnPin").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnSearch").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnRefresh").removeClass('ui-state-disabled').removeAttr("disabled");
			$("#search_list1").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#refresh_list1").removeClass('ui-state-disabled').removeAttr("disabled");
			$("#view_list1").removeClass('ui-state-disabled').removeAttr("disabled");			
        }  
    }

    function createButtons(){
        var cadena = "<table class='ui-pg-table navtable' cellspacing='0' cellpadding='0' border='0' style='float: left; table-layout: auto;padding:2px;'><tbody><tr>";
        cadena += addButton("btnNew","ui-icon-plus","Agregar nueva fila","Agregar");
        cadena += addButton("btnEdit","ui-icon-pencil","Modificar fila seleccionada","Modificar");
        cadena += addButton("btnSave","ui-icon-disk","Guardar fila seleccionada","Guardar");
        cadena += addButton("btnUndo","ui-icon-arrowreturnthick-1-w","Descartar los cambios","Deshacer");
        cadena += addButton("btnDelete","ui-icon-trash","Eliminar fila seleccionada","Eliminar");
        cadena += addButton("btnExport","ui-icon-suitcase","Exportar los datos de la tabla","Exportar");
        cadena += "</tr></tbody></table>";
        return cadena;
    }

    function addButton(idbtn,icon,title,name){
        return "<td id='"+idbtn+"' class='ui-pg-button ui-corner-all border-button' title='"+title+"' style='cursor: pointer;'><div class='ui-pg-div'><span class='ui-icon "+icon+"'/>"+name+"</div></td>";
    }
	
	function fechaCambioPass(){	

		var currentTime = new Date();
		var month = parseInt(currentTime.getMonth() + 2);
		month = month <= 9 ? "0"+month : month;
		var day = currentTime.getDate();
		day = day <= 9 ? "0"+day : day;
		var year = currentTime.getFullYear();
		//return day+"/"+ month + "/"+year;
		return year+"/"+ month + "/"+day;
	}	

    function addButtonEvents(){
        $("#btnNew").click(function(){
            if(!editing){			
			 	var datehoy = fechaCambioPass();
                var datarow = {id_usuario:"0",tx_usuario:"M",tx_nombre:"",tx_perfil:"",tx_indicador:"ACTIVO",tx_conectado:"NO",tx_bloqueado:"NO",tx_expira:"SI",fh_ultimoacceso:"",fh_cambiopsw:datehoy,fh_mod:"",id_usuariomod:"",fh_alta:"",id_usuarioalta:""};
                var su=jQuery("#list1").addRowData(0,datarow);
                jQuery("#list1").setSelection(0,false);
                jQuery("#list1").editRow(0,true);
                lastsel = 0;
                editing = true;
                adding = true;
                edicion(editing);
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        // Funcion click Editar
        $("#btnEdit").click(function(){
            if(!editing){
                var gr = jQuery("#list1").getGridParam('selrow');
                if(gr != null){
                    jQuery("#list1").editRow(gr,false);
                    lastsel = gr;
                    editing = true;
                    adding = false;
                    edicion(editing);
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
		
		function fieldsReq(){			
		
			var id = jQuery("#list1").getGridParam('selrow');
			if (id==null) id="0";
			
			var va_usuario='#'+id+'_tx_usuario';
			var va_nombre='#'+id+'_tx_nombre';
			var va_perfil='#'+id+'_tx_perfil';
			//alert($(va_usuario).val());	
			var error = true;
			validText(false, $(va_usuario), $("#divError"), 1);
			validText(false, $(va_nombre), $("#divError"), 1);
			validSelect($(va_perfil), $("#divError"));        
			//validNumeric($("#plazo"), $("#errplazo"));		
			
			if ( !validSelect($(va_perfil), $("#divError")) || !validText(false, $(va_usuario), $("#divError"), 1) || !validText(false, $(va_nombre), $("#divError"), 1)) error=false;
			return error;
		}

        // Funcion click Guardar
        $("#btnSave").click(function(){
			
			if(fieldsReq()){	
			 	if(editing){
					var id = jQuery("#list1").getGridParam('selrow');
					if (id) {
            			var ret = jQuery("#list1").getRowData(id);            		
        			}				
				
					if (adding) var dispatch ="insert";
					else var dispatch ="save";	
							
                	var url = "process_usuarios.php?dispatch="+dispatch+"&id="+id+"&";
                	url += $("#catalogForm").serialize();	                
				
					//alert(url);
				
					jQuery("#list1").saveRow(lastsel,false,'clientArray');
                	editing = false;
                	adding = false;
                	edicion(editing);
					
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
								jQuery("#list1").trigger("reloadGrid");
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
        $("#btnUndo").click(function(){
            if(editing){
				$("#divError").hide();
                jQuery("#list1").restoreRow(lastsel);
                editing = false;
                adding = false;
                edicion(editing);
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });

        // Funcion click Borrar
        $("#btnDelete").click(function(){
            if(!editing){        
                var gr = jQuery("#list1").getGridParam('selrow');
                var fAceptar = function(){
					
                    var gr = jQuery("#list1").getGridParam('selrow');									
					if (gr) {
						var ret = jQuery("#list1").getRowData(gr);						     			
        			}						
					
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
								jQuery("#list1").trigger("reloadGrid");
							}
						}	
					}	
					
					//alert (gr);									
					var url = "process_usuarios.php?dispatch=delete&id="+gr;					
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
        $("#btnExport").click(function(){
            if(!editing){
                alert("Click en exportar");
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });
    }

</script>
<div class="ui-widget-header align-center">CATALOGOS</div>
    <form id="catalogForm" method="" action="">
        <div id="divError"></div>        
        <div id="divGrid" style="padding:1%;width:98%" class="ui-widget ui-widget-content ui-corner-all">
            <div id="errorGrid" style="display:none">
                <div id="errorContent" class="ui-corner-all" style="padding: 0pt 0.7em;"></div>
            </div>
            <table id="list1" class="scroll" cellpadding="0" cellspacing="0">
                <tr><td style="border:1px black solid"></td></tr> 
                <!--<tr><td></td></tr> -->
            </table>
            <div id="pager1" class="scroll" style="text-align:center;"></div>

            <script type="text/javascript">
                jQuery(document).ready(function(){					
                    var mygrid = jQuery("#list1").jqGrid({
                        caption:"Catálogo de Usuarios",
                        mtype: "GET",                       
						url:'process_usuarios.php?dispatch=load',
						//alert ("Envio"+url);
                        editurl:'process_usuarios.php?dispatch=delete&id',
                        datatype: "json",
                        colNames:['Edo.','Id','Usuario','Nombre','Perfil','Indicador','Conectado','Bloqueado','Expira','Fecha ultimo acceso','Fecha cambio password','Fecha modifica', 'Usuario modifica','Fecha alta','Usuario alta'],
                        colModel:[   
                            {name: 'edo', index: 'edo', width: 30, align:"center", sortable: false },
                            {name:'id_usuario',index:'id_usuario',width:30, align:"center", editable:false,
								formoptions:{rowpos:1},
                                editoptions:{readonly:true,size:10},
                                searchoptions:{sopt:['eq','ne','lt','le','gt','ge']}
                            },							
                            {name:'tx_usuario',index:'a.tx_usuario',width:70, align:"left", editable:true,
								formoptions:{rowpos:2}, 
                                editoptions:{size:15,maxlength: 15},
                                editrules:{required:true},                                
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },   
							{name:'tx_nombre',index:'a.tx_nombre',width:250, align:"left", editable:true, 
								formoptions:{rowpos:3},
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},                                
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            }, 
							{name:'tx_perfil',index:'d.tx_nombre',width:140, align:"left", editable:true, 
								formoptions:{rowpos:4},
							 	edittype:'select',								
								editoptions:{dataUrl:'cat_carga.php?dispatch=perfil', defaultValue:'id_perfil'},								
                    			searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                			},  
							{name:'tx_indicador',index:'tx_indicador',width:80, align:"center", editable:true,
							 	formoptions:{rowpos:5 },
                                edittype:"select",
                                editoptions:{value:"1:ACTIVO;0:INACTIVO", size:10},
                                editrules:{required:true},                               
                                searchoptions:{sopt:['eq']}
                            },         
							{name:'tx_conectado',index:'tx_conectado',width:70, align:"center", editable:true,
								formoptions:{rowpos:6},
                                edittype:"select",
                                editoptions:{value:'0:NO;1:SI', size:10},
                                editrules:{required:true},                               
                                searchoptions:{sopt:['eq']}
                            }, 
							{name:'tx_bloqueado',index:'tx_bloqueado',width:70, align:"center", editable:true,                                
								formoptions:{rowpos:7},
								edittype:"select",
                                editoptions:{value:"0:NO;1:SI", size:10},
                                editrules:{required:true},                               
                                searchoptions:{sopt:['eq']}
                            }, 
							{name:'tx_expira',index:'tx_expira',width:40, align:"center", editable:true,
                                formoptions:{rowpos:8},
                                edittype:"select",
                                editoptions:{value:"0:NO;1:SI", size:10},
                                editrules:{required:true},
                                searchoptions:{sopt:['eq']}
                            },                                                                     
						  	{name:'fh_ultimoacceso',index:'fh_ultimoacceso',width:130, align:"center", editable:false,
								formoptions:{rowpos:9},
                                editoptions:{readonly:true,size:20},
                                searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'dd/mm/yy'});} }
                            },						
							{name:'fh_cambiopsw',index:'fh_cambiopsw',width:130, align:"center",editable:true,
								formoptions:{rowpos:10}, 
								editrules:{required:true},								
                    			editoptions:{size:12,
                        		dataInit:function(e1){
                            		$(e1).datepicker({dateFormat:'yy/mm/dd'});
                        			},
								defaultValue: function(){
									var currentTime = new Date();
									var month = parseInt(currentTime.getMonth() + 1);
									month = month <= 9 ? "0"+month : month;
									var day = currentTime.getDate();
									day = day <= 9 ? "0"+day : day;
									var year = currentTime.getFullYear();
									//return day+"/"+ month + "/"+year;
									return year+"/"+ month + "/"+day;
									} 
								}
							},
                            {name:'fh_mod',index:'fh_mod',width:130, align:"center", editable:false,
							 	formoptions:{rowpos:11},
                                editoptions:{readonly:true,size:10},
                                searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'dd/mm/yy'});} }
                            },
                            {name:'id_usuariomod',index:'usuario_mod',width:140, align:"left", editable:false,
								formoptions:{rowpos:12},
                                editoptions:{readonly:true, size:70}
                            },
                            {name:'fh_alta',index:'fh_alta',width:130, align:"center", editable:false,
								formoptions:{rowpos:13},
                                editoptions:{readonly:true,size:60},
                                searchoptions:{dataInit:function(el2){$(el2).datepicker({dateFormat:'dd/mm/yy'});} }
                            },
                            {name:'id_usuarioalta',index:'usuario_alta',width:140, editable: false,
								formoptions:{rowpos:14},
                                editoptions:{readonly:true,size:10}
                            }
                        ],
                        pager: '#pager1', // Nombre del paginador
                        altRows: true, // Activa la visualizacion de zebra en las filas
                        imgpath: "/css/ui-personal/images",
                        toolbar: [true,"bottom"], // Si cuenta con barra de herramienta y posicion de la misma
                        rowNum:100,  // numero de filas por pagina
                        rowList:[100,150,200],  // opciones de filas por pagina
                        autowidth: true,    // Ancho automatico para columnas
						//width:$("#gview_list1").width(),
                        //height:360,
						height:$("#CenterPane").height()-$("#NorthPane").height()-140,
						shrinkToFit :false,
            			sortable: true,
                        gridview: true,     // Mejora rendimiento para mostrar datos. Algunas funciones no estan disponibles
                        rownumbers: true,   // Muestra los numeros de linea en el grid
                        viewrecords: true,  //
                        viewsortcols: [true,'vertical',true], // Muestra las columnas que pueden ser ordenadas dinamicamente
                        sortname: 'tx_nombre', // primer columna de ordenacion
                        sortorder: 'asc',       // tipo de ordenacion inicial     
						footerrow : true,     
						userDataOnFooter : true,              
                        onSelectRow: function(id){
                            if(!editing){
                                lastsel = id;								
                            }
                        },
                        loadComplete: function(){							
                            var ids = jQuery("#list1").getDataIDs();
                            for(var i=0;i<ids.length;i++){
								// Indicador
                                indica = jQuery("#list1").getCell(ids[i],"tx_indicador");
                                if(indica == '1'){
                                    be = "<img style='cursor:pointer' border='none' src='images/greenball.png'>";
									ind = "ACTIVO";
                                }else{
                                    be = "<img style='cursor:pointer' border='none' src='images/redball.png'>";
									ind = "INACTIVO";
                                }
                                jQuery("#list1").setRowData(ids[i],{edo:be})
								jQuery("#list1").setRowData(ids[i],{tx_indicador:ind})
								
								// Conectado
								conec = jQuery("#list1").getCell(ids[i],"tx_conectado");
                                if(conec == '0'){                                    
									con = "NO";
                                }else{
									con = "SI";
                                }
								jQuery("#list1").setRowData(ids[i],{tx_conectado:con})
								
								// Bloqueado
								bloq = jQuery("#list1").getCell(ids[i],"tx_bloqueado");
                                if(bloq == '0'){                                    
									blo = "NO";
                                }else{
									blo = "SI";
                                }
								jQuery("#list1").setRowData(ids[i],{tx_bloqueado:blo})
								
								// Expira
								expi = jQuery("#list1").getCell(ids[i],"tx_expira");
                                if(expi == '0'){                                    
									expir = "NO";
                                }else{
									expir = "SI";
                                }
								jQuery("#list1").setRowData(ids[i],{tx_expira:expir})
								
                            }
                        },
                        loadError : function(xhr,st,err) {
                            var fAceptar = function(){
                                $('#dialogMain').dialog("close");
                            }
                            jAlert(true,true,"Error cargando grid Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText,fAceptar);
                        }
                    }).navGrid('#pager1',
                    {add:false,edit:false,view:true,del:false,search:true, refresh:true}, //options
                    {}, // edit options
                    {}, // add options
                    {reloadAfterSubmit:false,jqModal:false, closeOnEscape:true}, // del options
                    {closeOnEscape:true,multipleSearch:true}, // search options
                    {height:300,width:600,jqModal:false,closeOnEscape:true} // view options
                ).navButtonAdd('#pager1',{
                        id:"btnPin",
                        caption:"Mostrar",
                        title:"Mostrar/ocultar columnas",
                        buttonicon :"ui-icon-pin-s",
                        position:"first",
                        onClickButton:function(){
                            if(!editing){
                                jQuery("#list1").setColumns();
                            }
                        }
                    });

                    $("#btnPin").addClass("border-button");     // Se agrega borde a los botones                  
					$("#search_list1").addClass("border-button");
        			$("#view_list1").addClass("border-button");
        			$("#refresh_list1").addClass("border-button");
                    $("#t_list1").addClass("ui-jqgrid-pager");  // Se agrega estilo para que puedan funcionar estilos por default del grid button
                    $("#t_list1").height(22);                   // Se cambia alto toolbar
                    $("#t_list1").append(createButtons());      // Se agregan los botones al toolbar
                    addButtonEvents();
                    edicion(false);
                });
                
            </script>
        </div>
    </form>