<?
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
session_start();
if 	(isset($_SESSION['sess_user']))
{
	$tx_p1	= $_GET['tx_p1']; 
	$tx_p2	= $_GET['tx_p2']; 
	$tx_p3	= $_GET['tx_p3'];
	$tx_p4	= $_GET['tx_p4']; 
	$tx_p5	= $_GET['tx_p5']; 
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
		var p5= $('#tx_p5').val();
		
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
            if (p5==0) $("#btnExport").addClass('ui-state-disabled').attr("disabled","disabled");
			else $("#btnExport").removeClass('ui-state-disabled').removeAttr("disabled");
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

    function addButtonEvents(){
        $("#btnNew").click(function(){
            if(!editing){
				var datarow = {id_direccion:"0",tx_entidad:"",tx_nombre:"",tx_nombre_corto:"",tx_indicador:"ACTIVO",fh_ultimoacceso:"",fh_mod:"",id_usuariomod:"",fh_alta:"",id_usuarioalta:""};
				
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
			
			var va_entidad='#'+id+'_tx_entidad';	
			var va_nombre='#'+id+'_tx_nombre';		
			//alert($(va_usuario).val());	
			var error = true;
			validSelect($(va_entidad), $("#divError")); 
			validText(false, $(va_nombre), $("#divError"), 1);
			
			if ( !validSelect($(va_entidad), $("#divError")) || !validText(false, $(va_nombre), $("#divError"), 1)) error=false;			
			
			return error;
		}		
		
		$("#btnSave").click(function(){
			
			if(fieldsReq()){	
			 	if(editing){
					var id = jQuery("#list1").getGridParam('selrow');
					if (id) {
            			var ret = jQuery("#list1").getRowData(id);            		
        			}				
				
					if (adding) var dispatch ="insert";
					else var dispatch ="save";								
                	
				 	var url = "process_direcciones.php?dispatch="+dispatch+"&id="+id+"&";
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
			$("#divError").hide();
            if(editing){
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
				//alert("Click para Borrar");        
                var gr = jQuery("#list1").getGridParam('selrow');
                var fAceptar = function(){
					
                    var gr = jQuery("#list1").getGridParam('selrow');									
					
					
                    //jQuery("#list1").delRowData(gr);
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
								jQuery("#list1").trigger("reloadGrid");
							}
						}	
					}	
															
					var url = "process_direcciones.php?dispatch=delete&id="+gr;
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
<br>
<div class="ui-widget-header align-center">CATALOGOS</div>
<div>
    <form id="catalogForm" method="" action="">
    	<input id="tx_p1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
        <input id="tx_p2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
        <input id="tx_p3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
        <input id="tx_p4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
        <input id="tx_p5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
    	<div id="divError"></div> 
        <div id="divGrid" style="padding:1%;width:100%" class="ui-widget ui-widget-content ui-corner-all">
            <div id="errorGrid" style="display:none">
                <div id="errorContent" class="ui-corner-all" style="padding: 0pt 0.7em;" ></div>
            </div>
            <table id="list1" class="scroll" cellpadding="0" cellspacing="0">
                <tr><td style="border:1px black solid"></td></tr>
            </table>
            <div id="pager1" class="scroll" style="text-align:center;"></div>

            <script type="text/javascript">
                jQuery(document).ready(function(){
                    var mygrid = jQuery("#list1").jqGrid({
                        caption:"Catálogo de Direcciones Corporativas",
                        mtype: "GET",                       
						url:'process_direcciones.php?dispatch=load',
						//alert ("Envio"+url);
                        editurl:'process_direcciones.php?dispatch=delete&id',
                        datatype: "json",
                        colNames:['Edo.','Id','Entidad','Dirección Corporativa','Nombre Corto','Agrupación','Indicador','Fecha modifica', 'Usuario modifica','Fecha alta','Usuario alta'],
                        colModel:[   
                            {name: 'edo', index: 'edo', width: 30, align:"center", sortable: false, formoptions:{label: "Estado", rowpos:1 } },
                            {name:'id',index:'id_grupo',width:30, align:"center", editable:false, editoptions:{readonly:true,size:10},                                
								formoptions:{label: "Id", rowpos:2 }                               
                            },
							{name:'tx_entidad',index:'d.tx_nombre',width:100, align:"left", editable:true, 
								formoptions:{rowpos:3},
							 	edittype:'select',								
								editoptions:{dataUrl:'cat_carga.php?dispatch=entidad', defaultValue:'id_entidad'},								
                    			searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                			},  
                            {name:'tx_nombre',index:'a.tx_nombre',width:300, align:"left", editable:true, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{rowpos:4 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },   
							{name:'tx_nombre_corto',index:'tx_nombre_corto',width:300, align:"left", editable:true, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:false},
                                formoptions:{rowpos:5 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_agrupacion',index:'tx_agrupacion',width:100, align:"left", editable:true, 
                                edittype:"select",
                                editoptions:{value:"0:BANCOMER;1:WBAM", size:10},
                                editrules:{required:true},
                                formoptions:{rowpos:6 },
                                searchoptions:{sopt:['eq']}
                            }, 
							{name:'tx_indicador',index:'a.tx_indicador',width:80, align:"center", editable:true,
                                edittype:"select",
                                editoptions:{value:"1:ACTIVO;0:INACTIVO", size:10},
                                editrules:{required:true},
                                formoptions:{rowpos:7 },
                                searchoptions:{sopt:['eq']}
                            },                                                                          
                            {name:'fh_mod',index:'a.fh_mod',width:110, align:"center", editable:false,
                                editoptions:{readonly:true,size:10},
                                searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'dd/mm/yy'});} }
                            },
                            {name:'id_usuariomod',index:'usuario_mod',width:140, align:"left", editable:false,
                                editoptions:{readonly:true, size:60}
                            },
                            {name:'fh_alta',index:'fh_alta',width:110, align:"center", editable:false,
                                editoptions:{readonly:true,size:60},
                                searchoptions:{dataInit:function(el2){$(el2).datepicker({dateFormat:'dd/mm/yy'});} }
                            },
                            {name:'id_usuarioalta',index:'usuario_alta',width:140, editable: false,
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
                        onSelectRow: function(id){
                            if(!editing){
                                lastsel = id;								
                            }
                        },
                        loadComplete: function(){
                            var ids = jQuery("#list1").getDataIDs();
                            for(var i=0;i<ids.length;i++){
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
								
								// Direccion
								agr = jQuery("#list1").getCell(ids[i],"tx_agrupacion");
                                if(agr == '1'){                                    
									agr_r = "WB&AM";
                                }else{
									agr_r = "BANCOMER";
                                }
								jQuery("#list1").setRowData(ids[i],{tx_agrupacion:agr_r})			
																
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
					{height:400,width:600,jqModal:false,closeOnEscape:true} // view options
                ).navButtonAdd('#pager1',{
                        id:"btnPin",
                        caption:"Mostrar",
                        title:"Mostrar/ocultar columnas",
                        buttonicon :'ui-icon-pin-s',
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
</div>
<?
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>