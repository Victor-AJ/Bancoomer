<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
//for run
session_start();
if 	(!isset($_SESSION['sess_user']))
{
	echo "Sessi&oacute;n Invalida";
}
else
{
include("includes/funciones.php");  
$mysql=conexion_db();

$tx_p1	= $_GET['tx_p1']; //consultar
$tx_p2	= $_GET['tx_p2']; //insertar
$tx_p3	= $_GET['tx_p3']; //actualziar
$tx_p4	= $_GET['tx_p4']; //borrar
$tx_p5	= $_GET['tx_p5']; //exportar

$tx_catalogo	= $_GET['tx_catalogo']; //catalogo a desplegar
$tx_catalogo_minus = str_replace('_', ' ', $tx_catalogo);
$tx_catalogo_minus = strtolower($tx_catalogo_minus );

if ($tx_catalogo == "CUENTA_CONTABLE")
	{
	$etiquetaValor = "Cuenta GPS"; 
	$etiquetaValorComp = "Cuenta Contable";
	}
else 
	{
	$etiquetaValor = "Valor de  $tx_catalogo_minus "; 
	$etiquetaValorComp = "Valor Complementario";
	}

?>

<script type="text/javascript">

$("#divEspacios").html("");	
// Variables locales para el grid
var valLastSel;		  //indica el ultimo reg seleccionado
var adding = false;  //indica el modo de adicion
var editing = false;  //indica el modo de edicion


//Funciones genericas
//Despliega el dialogo del resultado de alguna afectacion a BD
var funcPostAjaxCatGlobal = function(data)
{					   			

	
	
		if(data.error == true)
			{						
				if(data.message != null)
					jAlert(true,true,data.message,function() {	$('#dialogMain').dialog("close"); 	});
				else
					logout();
			} 
		else 
			{						
				if(data.message != null)
					jAlert(true,false,data.message,function() {	$('#dialogMain').dialog("close"); 	});
			}	

	jQuery("#list1").trigger("reloadGrid"); //implicitamente corre php de carga
};




//En caso que se confirme la eliminacion del registro se manda a ejecutar el proceso
var funcAjaxAceptarEliminar = function()
{
	var valRegistro=0;
	var url="";

	var catalogo= "<? echo $tx_catalogo?>";

	valRegistro = jQuery("#list1").getGridParam('selrow');	
	if (valRegistro)
			var arrValRet = jQuery("#list1").getRowData(valRegistro);						     			
	
		url = "process_global.php?dispatch=delete&id="+valRegistro+"&tx_catalogo="+catalogo;
		
	
	
		executeAjax("post", false ,url, "json", funcPostAjaxCatGlobal);
};


//valida q los campos introducidos del registro nuevo sean adecuados 
function isFieldCorrect()
{			

	var id = jQuery("#list1").getGridParam('selrow');
	
	if ( adding == true)
		id="0";
	
	
	if (id==null)
		id="0";
		
	
	var obj_clave='#'+id+'_tx_clave';
	var obj_valor='#'+id+'_tx_valor';
	var obj_valorcomp='#'+id+'_tx_valor_complementario';
	var obj_obs='#'+id+'_tx_observaciones';
	var obj_indicador='#'+id+'_indicador';

//	$('#'+id+'_tx_valor').val();
	
	
	//alert("variables : " + obj_clave + "  , " + obj_valor + " ," +  obj_valorcomp + " ," + obj_obs + " ," + obj_indicador );			
	//alert("valores: " +  $(obj_clave).val() + "  , " + $(obj_valor).val() + " ," +  $(obj_valorcomp).val() + " ," +  $(obj_obs).val() + " ," + $(obj_indicador).val()  );

	 //validText(false, $(obj_clave), $("#divError"), 15) ;
	 validText(false, $(obj_valor), $("#divError"), 1) ;
	 validText(false, $(obj_obs), $("#divError"), 5) ;
	 
	var resultado = true;
	if (  validText(false, $(obj_valor), $("#divError"), 1) && validText(false, $(obj_obs), $("#divError"), 5) )
		resultado = true;
	else 
		resultado = false;

	return resultado;
	
}

//Agrega las imagenes de activo/inactivo
function maquillaGrid()
{
	   var ids = jQuery("#list1").getDataIDs();
	   	   
       for(var i=0;i<ids.length;i++)
           {
    	   cellContInd = jQuery("#list1").getCell(ids[i],"indicador");
           if(cellContInd == '1')
               {
                be = "<img style='cursor:pointer' border='none' src='images/greenball2.gif'>";
				ind = "ACTIVO";
           		}
           else
               {
                be = "<img style='cursor:pointer' border='none' src='images/redball2.gif'>";
				ind = "INACTIVO";
           		}
           jQuery("#list1").setRowData(ids[i],{edo:be});
		   jQuery("#list1").setRowData(ids[i],{indicador:ind});
       	  }

}

	

//De acuerdo al modo de edicion (true,false) cambia los botones
  function edicion(editando){	 	

	 	//alert("In edicion CON EDITING:" + editing);
		var p2= $('#tx_p2').val();
	 	var p3= $('#tx_p3').val();
	 	var p4= $('#tx_p4').val();
	 	var p5= $('#tx_p5').val();
	 

		
        if(editando==true)
            {	
            editing=true;
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

   		
   			
        	}
    	else{
        	
    		editing=false;
            $("#btnSave").addClass('ui-state-disabled').attr("disabled","disabled");
            $("#btnUndo").addClass('ui-state-disabled').attr("disabled","disabled");
            
			if (p2==0) 
				$("#btnNew").addClass('ui-state-disabled').attr("disabled","disabled");
            else 
                $("#btnNew").removeClass('ui-state-disabled').removeAttr("disabled");
            
            if (p3==0) 
                $("#btnEdit").addClass('ui-state-disabled').attr("disabled","disabled");
			else 
				$("#btnEdit").removeClass('ui-state-disabled').removeAttr("disabled");			

            if (p4==0) 
                $("#btnDelete").addClass('ui-state-disabled').attr("disabled","disabled");
			else 
				$("#btnDelete").removeClass('ui-state-disabled').removeAttr("disabled");

            if (p5==0) 
                $("#btnExport").addClass('ui-state-disabled').attr("disabled","disabled");
			else 
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
      cadena += addButton("btnDelete","ui-icon-trash","Inactivar fila seleccionada","Inactivar");
      cadena += addButton("btnExport","ui-icon-suitcase","Exportar los datos de la tabla","Exportar");
      cadena += "</tr></tbody></table>";
      return cadena;
  }

  function addButton(idbtn,icon,title,name){
      return "<td id='"+idbtn+"' class='ui-pg-button ui-corner-all border-button' title='"+title+"' style='cursor: pointer;'><div class='ui-pg-div'><span class='ui-icon "+icon+"'/>"+name+"</div></td>";
  }
    function addButtonEvents()
    {
    	$("#btnNew").hover   (function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
    	$("#btnSave").hover  (function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
    	$("#btnEdit").hover  (function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
    	$("#btnUndo").hover  (function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
    	$("#btnDelete").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
    	$("#btnExport").hover(function(){$(this).addClass("ui-state-hover");},function(){$(this).removeClass("ui-state-hover");});
   	
   	
		
        $("#btnNew").click(function(){
						            if(editing==false)
							            		{
									            //generar el registro en blanco
								                var datarow = {edo:"-", id:"0",tx_clave:"",tx_valor:"",tx_valor_complementario:"",tx_observaciones:"",tx_indicador:"ACTIVO",fh_fecha_modifica:"",id_usuario_modifica:"",fh_fecha_alta:"",id_usuario_alta:""};
								                var su=jQuery("#list1").addRowData(0,datarow);
								                jQuery("#list1").setSelection(0,false);
								                jQuery("#list1").editRow(0,true);
								                valLastSel = 0;
								                adding = true;				
								                edicion(true);
								         		}
						        	}
    						);

	

        
        $("#btnSave").click( function () 
                						{
        									var dispatch="";
        									var url = "process_global.php";
        									var catalogo = "<?php echo $tx_catalogo ?>";
        									//var encontrado= false;
//											alert("Se debera salvar el registro global:" + valLastSel );
											var regSel = jQuery("#list1").getGridParam('selrow');
//												alert("El registro seleccionado es:" + regSel );
											var coinciden=false;
											if ( valLastSel == regSel )
											{
											coinciden=true;
											}
											else
											{
											jAlert(true,true,"El registro seleccionado no coincide con el que se edito inicialmente, seleccione el registro a guardar",function(){$('#dialogMain').dialog("close");});
											}
												

										if ( coinciden == true )
										{
											if(isFieldCorrect()==true  )
											{
												var id = jQuery("#list1").getGridParam('selrow');
												
												var cadValor= $('#'+id+'_tx_valor').val();

												//alert("comparar [" + id + "]: " + cadValor + " con resto grid" );
												//var arrayValores = jQuery("#list1").getCol('tx_valor', true);
												//var longitud=arrayValores.length;
												//for (i=0;i<arrayValores.length;i++)
												//{
													//alert( "elemento ["+i+"]: id:(" + arrayValores[i].id +")  value:(" + arrayValores[i].value +  ")");
													//if ( id != arrayValores[i].id && cadValor==arrayValores[i].value )
													//	encontrado= true;
												//}
												
														if (id) 
											    			var ret = jQuery("#list1").getRowData(id);
														
													 	dispatch= (adding==true)?"insert":"update"; 
														url = "process_global.php?dispatch="+dispatch+"&id="+id+"&tx_catalogo="+ catalogo + "&";
											        	url += $("#catalogForm").serialize();	
	
											        	jQuery("#list1").saveRow(valLastSel,false,'clientArray');
	
											        	$(this).removeClass("ui-state-hover");
											        	$("#btnNew").removeClass("ui-state-hover");
											        	$("#btnEdit").removeClass("ui-state-hover");
											        	 
											        	
											        	adding = false; //global
											        	edicion(false);
														executeAjax("post", false ,url, "json", funcPostAjaxCatGlobal);
												
											}
											else
											{
												jAlert(true,true,"Existen campos obligatorios vac&iacute;os",function(){$('#dialogMain').dialog("close");});
												
	        								}
										}	
                					}
							);


	

		$("#btnEdit").click(  function ()   
										{
										var valRegistro=0;
										//alert("editing is:" + editing);
										if (editing == false)
											{
											//obtener registro selecionado
											valRegistro=jQuery("#list1").getGridParam('selrow');

											
												if ( valRegistro != null)
												{
													//$("#list1").editRow(valRegistro,false,funcionEditCell,funcionSuccesCell,'clientArray' , '',funcionAfterSaveCell);
													$("#list1").editRow(valRegistro,false );
													//alert("Registro seleccionado:" + valRegistro + " se asignara a global");
													valLastSel=valRegistro;
													
													adding=false;
													edicion(true);
												}
												else
													jAlert(true,true,"Debe seleccionar una fila",function(){$('#dialogMain').dialog("close");});
												
											}
										}
							);

        
		$("#btnUndo").click(function(){
										
									    if (editing == true )
										    {
								            		jQuery("#list1").restoreRow(valLastSel);
													if (adding) 
														jQuery("#list1").trigger("reloadGrid");

													$(this).removeClass("ui-state-hover");
								            		
								            		adding = false;
								            		edicion(false);
							      			}
									}
							);
							
							
		$("#btnDelete").click(function()
										{
												var valRegistro=0;
												
												if (editing == false)
													{
													valRegistro=jQuery("#list1").getGridParam('selrow');
													//alert(String(valRegistro));
		
													if( valRegistro != null )
					                    				jConfirm(true,"\u00bfDesea INACTIVAR el registro  seleccionado", funcAjaxAceptarEliminar, function(){$('#dialogMain').dialog("close");});
					                				else
					                					jAlert(true,true,"Por favor.. Seleccione una fila",function(){$('#dialogMain').dialog("close");});
				                					
												}
										}
							);

		


		// Funcion click Exportar
	        $("#btnExport").click(function()
					                {
					            	if(editing == false)
					                	{
					            		var url = "excel_cat_global.php?tx_catalogo=<?php echo $tx_catalogo?>";
										window.open( url,"_blank");				
					            		}
					        		}
								);
			
		
    } //function AddButtonEvents
    
		
		
</script>
    
    
<div class="ui-widget-header align-center">CATALOGO</div>

<form id="catalogForm"   action="">   
   	<input id="tx_p1" name="tx_p1" type="hidden" value="<? echo $tx_p1 ?>" />
	<input id="tx_p2" name="tx_p2" type="hidden" value="<? echo $tx_p2 ?>" />
    <input id="tx_p3" name="tx_p3" type="hidden" value="<? echo $tx_p3 ?>" />
    <input id="tx_p4" name="tx_p4" type="hidden" value="<? echo $tx_p4 ?>" />
    <input id="tx_p5" name="tx_p5" type="hidden" value="<? echo $tx_p5 ?>" />
    
    <div id="divError"></div> 
    <div id="divGrid" style="padding:1%;width:98%" class="ui-widget ui-widget-content ui-corner-all">
    	<div id="errorGrid" style="display:none">
        	<div id="errorContent" class="ui-corner-all" style="padding: 0pt 0.7em;" ></div>
        </div>
        <table id="list1" class="scroll" cellpadding="3" cellspacing="3" border=4>
        	<tr><td style="border:1px black solid">XXXXXXXXX</td></tr>
        </table>
        <div id="pagerCat" class="scroll" style="text-align:center;"></div>
        
<script type="text/javascript">

jQuery(document).ready( function ()
					{
						var mygrid = $("#list1").jqGrid({
														caption: "<table border=0 cellspacing=0 cellpadding=0> <tr><td valign='middle'><img src='images/<? echo $tx_catalogo ?>.gif'> </td><td valign='middle'>&nbsp; CATALOGO <? echo str_replace('_', ' ', $tx_catalogo) ?></td></tr></table>" ,  
														mtype: "GET" ,  
														url:'process_global.php?dispatch=load&tx_catalogo=<? echo $tx_catalogo ?>',
														datatype: "json",
														colNames:[  'Edo.','Id','Clave','<?php echo $etiquetaValor ?>','<?php echo $etiquetaValorComp ?>','Descripci&oacute;n' , 'Indicador' ,'Fecha modifica', 'Usuario modifica','Fecha alta','Usuario alta'],
														colModel:[   
						                   							{
							                   						 name: 'edo', 
							                   						 index: 'edo',
							                   						 //hidden
							                   						 width: 30, 
							                   						 align:"center",
							                   						 sortable: false,
							                   						 editable: false ,
							                   					     //edittype:
							                   					     //stype:no
							                   						 search: false , 
							                   						 //editoptions: no
							                   						 //edirules: no
							                   						 formoptions:{label: "Estado", rowpos:1 }
							                   						 //searchoptions:no 
							                   						 }
							                   						,
								                   						{
										                   				name:'id',
									                   					index:'id',
									                   					hidden: true,
									                   					width:30, 
									                   					align:"center", 
									                   					sortable: true,
									                   					editable:false,
									                   				    //edittype: 
									                   				    //stype:no
									                   					search: true,
									                   					editoptions:{readonly:true,size:10},
									                   					//editrules:no
									                   					formoptions:{label: "Id", rowpos:2 } , 
									                   					searchoptions:{sopt:['eq','ne'] }  
									                   					}
								                   						,
																        	{
										    						        name:'tx_clave',
										    						        index:'tx_clave',
										    						        //hidden:no
										    						        width:200, 
										    						        align:"left", 
										    						        sortable: true,
										    						        editable:false,
										    						        //edittype:
										    						        //stype:no
										    						        search: true,
										    						        editoptions:{readonly:false, maxlength:30},
										    						        editrules:{required:true},
										    				                formoptions:{label: "Clave", rowpos:3 },
										    								searchoptions:{sopt:['eq','ne','in','ni','cn','nc']}
									                            			}
								                            				,   
									                            				{
											    						        name:'tx_valor',
											    						        index:'tx_valor',
											    						        //hidden:no
											    						        width:120, 
											    						        align:"left",
											    						        sortable: true, 
											    						        editable:true,
											    						        //edittype:
											    						        //stype:no
											    						        search: true,
											    						        editoptions:{readonly:false, maxlength:30},
											    						        editrules:{required:true},
									                		                    formoptions:{label: "Valor ", rowpos:4 },
									                							searchoptions:{sopt:['eq','ne','bw','bn','ew','en','cn','nc']} 
						                            						 	}
					                            						 	 	,   
										                            				{
												    						        name:'tx_valor_complementario',
												    						        index:'tx_valor_complementario',
												    						      	//hidden:no
												    						        width:140, 
												    						        align:"left", 
												    						        sortable: true, 
												    						        editable:true,
												    						        //edittype:
												    						        //stype:no
												    						        search: true,
												    						      	editoptions:{readonly:false, maxlength:50},
										                            				editrules:{required:false},
										                		                    formoptions:{label: "Valor Adicional", rowpos:5 },
										                							searchoptions:{sopt:['eq','ne','bw','bn','ew','en','cn','nc']}                              
											                            			}
										                            				,   
											                            				{
													    						        name:'tx_observaciones',
													    						        index:'tx_observaciones',
													    						        //hidden:no
													    						        width:320, 
													    						        align:"left", 
													    						        sortable: true, 
													    						        editable:true,
													    						        //edittype:no
													    						        //stype:no
													    						        search: true,
													    						        editoptions:{size:255,maxlength: 100},
													    					            editrules:{required:true},
													    					            formoptions:{label: "Descripci&oacute;n", rowpos:6 },
													    								serchoptions:{sopt:['eq','ne','bw','bn','ew','en','cn','nc']} 
												                            			}
											                            				,   
																							{
																							name:'indicador',
											    											index:'a.tx_indicador',
											    											//hidden:no
											    											width:80, 
											    											align:"center",
											    											sortable: true,
											    											editable:true,
											    											edittype:"select",
											    											stype:"select",
											    											search: true,
											    											searchtype: "number" ,
											                                				editoptions:{value:"1:ACTIVO;0:INACTIVO"},
											                                				editrules:{required:true},
											            		                            formoptions:{label: "Indicador", rowpos:7 },
											            		                            searchoptions:{ 
											                            										value : "1:ACTIVO;0:INACTIVO"
																												,
											                        				  							sopt:['eq','ne']
																												}
											                            					}
										                            						,                                                                          
												                            					{
												                            					name:'fh_fecha_modifica',
												                                				index:'fh_fecha_modifica',
												                                				//hidden:no
												                                				width:110, 
												                                				align:"center",
												                                				sortable: true, 
												                                				editable:false,
												                                				//edittype:no ,
												    											//stype: no ,
												    											search: true,
												                                				editoptions:{readonly:true},
												                                				//editrules:no ,
												            		                            formoptions:{label: "Fecha de modificaci&oacute;n", rowpos:8 },
												                                				searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'yy/mm/dd'});}
												                                					 					,
										            		                                						sopt:['eq','ne','lt','le','gt','ge']
											            		                                					}
												                                					
												                            					}
											                            						,
													                            					{
													                            					name:'usuario_modifica',
													                                				index:' b.tx_nombre',
													                                				//hidden:no
													                                				width:150, 
													                                				align:"left",
													                                				sortable: true,  
													                                				editable:false,
													                                				//edittype:no ,
													    											//stype: no ,
													    											search: true,
													                                				editoptions:{readonly:true},
													                                				//editrules:no ,
													                                				formoptions:{label: "Usuario de modificaci&oacute;n", rowpos:9 },
													                                				searchoptions:{sopt:['eq','ne','cn','nc']}
													                            					}
													                            					,
														                            					{
														                            					name:'fh_fecha_alta',
														                                				index:'fh_fecha_alta',
														                                				//hidden:no
														                                				width:110, 
														                                				align:"center", 
														                                				sortable: true,  
														                                				editable:false,
														                                				//edittype:no ,
														    											//stype: no ,
														                                				editoptions:{readonly:true},
														                                				//editrules:no ,
														                                				formoptions:{label: "Fecha de alta ", rowpos:10 },
														                                				searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'yy/mm/dd'});} 
														                                									,
								            		                                										sopt:['eq','ne','lt','le','gt','ge']
																		                                					}
														                                					
															                                			}
														                                				,
															                                				{
															                                				name:'usuario_alta',
															                                				index:'c.tx_nombre ',
															                                				//hidden:no
															                                				width:150, 
															                                				align:"left", 
															                                				sortable: true, 
															                                				editable:false,
															                                				//edittype:no ,
															    											//stype: no ,
															                                				editoptions:{readonly:true},
															                                				//editrules:no ,
															                                				formoptions:{label: "Usuario de alta ", rowpos:11 },
															                                				searchoptions:{sopt:['eq','ne','cn','nc']}
															                            					}
														          ],
			                        					pager: '#pagerCat', // Nombre del paginador
                                       					altRows: true, // Activa la visualizacion de zebra en las filas
                                       					imgpath: "/css/ui-personal/images",
                                       					toolbar: [true,"bottom"], // Si cuenta con barra de herramienta y posicion de la misma
                                      					rowNum:100,  // numero de filas por pagina
                                       					rowList:[100,200,500],  // opciones de filas por pagina
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
	           					                        sortname: 'tx_clave', // primer columna de ordenacion
	           					                        sortorder: 'asc',       // tipo de ordenacion inicial
														



					                					                        loadError : function(xhr,st,err) {
					                					                                						jAlert(true,true,"Error cargando grid Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText,function(){	$('#dialogMain').dialog("close");	});
					                					                            							},    	 

					                					                        loadComplete: function()
					                					                        							{
            					                        														 maquillaGrid();
					                					                        							}
																											
	
															}
																);


						 $("#list1").navGrid('#pagerCat',                  
																		{add:false,edit:false,view:true,del:false,search:true, refresh:true}, //options
							                    						{}, // edit options
							                    						{}, // add options
							                    						{reloadAfterSubmit:false,jqModal:false, closeOnEscape:true}, // del options
							                    						{closeOnEscape:true,multipleSearch:true,closeAfterSearch:true}, // search options                    
																		{height:400,width:400,jqModal:false,closeOnEscape:true} // view options
							                						  );
						 $("#list1").navButtonAdd('#pagerCat',
					                        									{  id:"btnPin",
					                        										caption:"Mostrar",
					                        										title:"Mostrar/ocultar columnas",
					                        										buttonicon :'ui-icon-pin-s',
					                        										position:"first",
					                        										onClickButton:function()
					                        															{
					                            														if( editing==false)
					                                														{
					                                														jQuery("#list1").setColumns();
					                            															}
					                        															}
					                    										}
									                						  );

					
						//colocar border a botones de barra inferior
						$("#btnPin").addClass("border-button");     // Se agrega borde a los botones
	                    $("#search_list1").addClass("border-button");  //el prefijo search es estandar y se adjunta el nombre del id
						$("#view_list1").addClass("border-button");   //el prefijo view es estandar 
	                    $("#refresh_list1").addClass("border-button");

	                    $("#t_list1").addClass("ui-jqgrid-pager");  // Se agrega estilo para que puedan funcionar estilos por default del grid button
	                    $("#t_list1").height(22);                   // Se cambia alto toolbar

		                $("#t_list1").append(createButtons());      // Se agregan los botones al toolbar
		                
	                   	addButtonEvents();
	                    
						  edicion(false);
						}
				);

	


</script>        
 </div>
 </form>
<?php 
}
?>