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
	
	var id= $('#id').val();
	
    var lastsel;
    var adding = false;

     function edicion(editing){
	 
	 	//var p2= $('#tx_p2').val();
	 	//var p3= $('#tx_p3').val();
	 	//var p4= $('#tx_p4').val();
		//var p5= $('#tx_p5').val();
		
		var p2= 1;
	 	var p3= 1;
	 	var p4= 1;
		var p5= 1;
		
		//alert ("p5"+p5);
		
        if(editing){
            $("#btnSave").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnUndo").removeClass('ui-state-disabled').removeAttr("disabled");
            $("#btnNewLic").addClass('ui-state-disabled').attr("disabled","disabled");
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
            if (p2==0) $("#btnNewLic").addClass('ui-state-disabled').attr("disabled","disabled");
            else $("#btnNewLic").removeClass('ui-state-disabled').removeAttr("disabled");
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
        cadena += addButton("btnNewLic","ui-icon-plus","Agregar nueva fila","Agregar");
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
	
		$("#btnNewLic").click(function(){
			var dispatch ="insert";
        	if(!editing){					
				jQuery("#list1").setGridState("hidden");
				loadHtmlAjax(true, $("#divAlta"), "cat_licencias_m.php?dispatch="+dispatch);
        	}
		});
		
		$("#btnEdit").click(function(){		
			var dispatch ="save";				
            if(!editing){
                var gr = jQuery("#list1").getGridParam('selrow');
                if(gr != null){
                    jQuery("#list1").editRow(gr,false);
                    lastsel = gr;
					jQuery("#list1").setGridState("hidden");
					loadHtmlAjax(true, $("#divAlta"), "cat_productos_m.php?dispatch="+dispatch+"&id="+gr);
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
			
			var va_pais='#'+id+'_tx_pais';	
			var va_estado='#'+id+'_tx_estado';				
			//alert($(va_usuario).val());	
			
			var error = true;
			
			validSelect($(va_pais), $("#divError")); 
			validText(false, $(va_estado), $("#divError"), 1);
			
			if ( !validSelect($(va_pais), $("#divError")) || !validText(false, $(va_estado), $("#divError"), 1)) error=false;		
									
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
                	
				 	var url = "process_licencias.php?dispatch="+dispatch+"&id="+id+"&";
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
															
					var url = "process_licencias.php?dispatch=delete&id="+gr;
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
            	var url = "excel_productos.php";
				window.open( url,"_blank");				
            }
        }).hover(function(){
            $(this).addClass("ui-state-hover")
        },function(){
            $(this).removeClass("ui-state-hover")
        });
    }
	
	///////////////// DEFINICION DE EVENTOS //////////////////////
	$("#sel_proveedor").change(function () {
     	$("#sel_ubicacion option:selected").each(function () {
		
			var url = "busca_ubicacion.php?id="+$("#sel_ubicacion").val();			  
			var func = function(data){					   			
	 			if(data.pasa == true){							
					$("#cap_estado").val(data.data1);	
					$("#cap_pais").val(data.data2);	
				}				
			} 
			executeAjax("post", false ,url, "json", func); 						
        });
     });

</script>
    <form id="catalogForm" method="" action="">
    	<input id="tx_p1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
        <input id="tx_p2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
        <input id="tx_p3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
        <input id="tx_p4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
        <input id="tx_p5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
      	<!-- <div id="divError"></div> -->
        <!-- <div id="divGrid" style="padding:1%;width:98%" class="ui-widget ui-widget-content ui-corner-all"> -->
        <!--    <div id="errorGrid" style="display:none"> -->
        <!--        <div id="errorContent" class="ui-corner-all" style="padding: 0pt 0.7em;" ></div> -->
        <!--    </div> -->
            <table id="list1" class="scroll" cellpadding="0" cellspacing="0">
                <tr><td style="border:1px black solid"></td></tr>
            </table>
            <div id="pager1" class="scroll" style="text-align:center;"></div>
            <div id="divAlta"></div> 
            <script type="text/javascript">
				(function($){
					jQuery("#list1").jqGrid({
                        caption:"Mantenimiento a Licencias",
                        mtype: "GET",                       
						url:'process_licencias.php?dispatch=load&id='+id,
                        datatype: "json",
                        colNames:['Edo.','ID','Proveedor','Producto','Cuenta','Descripci&oacute;n','Licencia','Precio','Moneda','Concepto Contable','SID Terminal','Login','Serial Number','Indicador'],
                        colModel:[   
                            {name: 'edo', index: 'edo', width: 30, align:"center", sortable: false, formoptions:{label: "Estado", rowpos:1 } },
                            {name:'id',index:'id_licencia',width:20, align:"center", editable:false, editoptions:{readonly:true,size:10}, 
								formoptions:{label: "Id", rowpos:2 }                               
                            },
                            {name:'tx_proveedor_corto',index:'tx_proveedor_corto',width:200, align:"left", editable:false, formoptions:{label: "Proveedor", rowpos:3 }
							},  
                            {name:'tx_producto',index:'tx_producto',width:250, align:"left", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Producto", rowpos:4 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_cuenta',index:'tx_cuenta',width:100, align:"left", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Cuenta", rowpos:5 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_descripcion',index:'tx_descripcion',width:300, align:"left", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Descripci&oacute;n", rowpos:6 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'in_licencia',index:'in_licencia',width:50, align:"left", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Licencia", rowpos:7 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'fl_precio',index:'fl_precio',width:100, align:"right", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Precio", rowpos:8 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_moneda',index:'tx_moneda',width:50, align:"center", editable:false, 
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Moneda", rowpos:9 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_concepto_contable',index:'tx_concepto_contable',width:120, align:"center", editable:false,
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Concepto Contable", rowpos:10 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },   
							{name:'tx_login',index:'tx_login',width:80, align:"center", editable:false,
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Login", rowpos:11 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },                                                                       
                            {name:'tx_sid_terminal',index:'tx_sid_terminal',width:80, align:"center", editable:false,
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "SID Terminal", rowpos:12 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },							
                            {name:'tx_serial_number',index:'tx_serial_number',width:80, align:"center", editable:false,
                                editoptions:{size:255,maxlength: 255},
                                editrules:{required:true},
                                formoptions:{label: "Serial Number", rowpos:13 },
								searchoptions:{sopt:['eq','ne','in','ni','bw','bn','ew','en','cn','nc']}
                            },
							{name:'tx_indicador',index:'tx_indicador',width:80, align:"center", editable:false,
                                edittype:"select",
                                editoptions:{value:"1:ACTIVO;0:INACTIVO", size:10},
                                editrules:{required:true},
                                formoptions:{label: "Indicador", rowpos:14 },
                                searchoptions:{sopt:['eq']}
                            }
                        ],
                        pager: '#pager1', // Nombre del paginador
                        altRows: true, // Activa la visualizacion de zebra en las filas
                        imgpath: "/css/ui-personal/images",
                        toolbar: [true,"bottom"], // Si cuenta con barra de herramienta y posicion de la misma
                        rowNum:50,  // numero de filas por pagina
                        rowList:[50,100,200],  // opciones de filas por pagina
                        autowidth: true,    // Ancho automatico para columnas
						//width:$("#gview_list1").width(),
                        height:120,
						//height:$("#CenterPane").height()-$("#NorthPane").height()-130,
						shrinkToFit :false,
            			sortable: true,
                        gridview: true,     // Mejora rendimiento para mostrar datos. Algunas funciones no estan disponibles
                        rownumbers: true,   // Muestra los numeros de linea en el grid
                        viewrecords: true,  //
                        viewsortcols: [true,'vertical',true], // Muestra las columnas que pueden ser ordenadas dinamicamente
                        sortname: 'tx_proveedor_corto, tx_producto',  // primer columna de ordenacion
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
					{height:400,width:800,jqModal:false,closeOnEscape:true} // view options
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
                })(jQuery); ;
                
            </script>
       <!-- </div> -->
    </form>
<?
} else {
	echo "Sessi&oacute;n Invalida";
}	
?>