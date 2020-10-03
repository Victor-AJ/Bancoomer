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

?>

<script type="text/javascript">

$("#divEspacios").html("");	
// Variables locales para el grid
var valLastSel;		  //indica el ultimo reg seleccionado
//Funciones genericas
		
</script>
    
    
<div class="ui-widget-header align-center">BIT&Aacute;CORA</div>

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
														caption: "<table border=0 cellspacing=0 cellpadding=0> <tr><td valign='middle'><img src='images/bitacora.gif'> </td><td valign='middle'>&nbsp; BIT&Aacute;CORA DE EVENTOS</td></tr></table>" ,  
														mtype: "GET" ,  
														url:'process_bitacora.php?dispatch=load',
														datatype: "json",
														colNames:[  'Id','Usuario', 'Nombre' , 'Tipo Evento','Fecha Evento',  'M&oacute;dulo' , 'Secci&oacute;n' , 'Programa' , 'IP remota' , 'IP FW', 'IP Cliente',  'Tabla CSI' ,'Valores', 'Clave CSI'  ],
														colModel:[   
																	{
																	name:'id_bitacora',
																	index:'id_bitacora',
																	hidden: false,
																	width:30, 
																	align:"center", 
																	sortable: true,
																	editable:false,
																    //edittype: 
																    //stype:no
																	search: true,
																	editoptions:{readonly:true,size:10},
																	//editrules:no
																	formoptions:{label: "Id", rowpos:1 } , 
																	searchoptions:{sopt:['eq','ne','lt','le','gt','ge'] }  
																	}
																	,
																		{
																		name:'tx_usuario',
																		index:'tx_usuario',
																		//hidden:no
																		width:70, 
																		align:"left", 
																		sortable: true, 
																	    editable: false,
																	    //edittype:
																	    //stype:no
																	    search: true,
																	  	//editoptions:{readonly:false, maxlength:50},
																	    formoptions:{label: "Clave usuario", rowpos:2 },
																		searchoptions:{  sopt:['eq','ne','in','ni','cn','nc']}                              
																		}
																		,   
																			{
																	        name:'tx_nombre',
																	        index:'tx_nombre',
																	        //hidden:no
																	        width:200, 
																	        align:"left", 
																	        sortable: true, 
																	        editable: false,
																	        //edittype:no
																	        //stype:no
																	        search: true,
																	        formoptions:{label: "Nombre usuario", rowpos:3 },
																			serchoptions:{sopt:['eq','ne','in','ni','cn','nc']} 
																			}
																			,  

		
								                   						
																        	{
										    						        name:'tx_tipo_operacion',
										    						        index:'tx_tipo_operacion',
										    						        //hidden:no
										    						        width:80, 
										    						        align:"left", 
										    						        sortable: true,
										    						        editable:false,
										    						        //edittype:
										    						        //stype:no
										    						        search: true,
										    						        editoptions:{readonly:false, maxlength:30},
										    				                formoptions:{label: "Acci&oacute;n", rowpos:4 },
										    								searchoptions:{sopt:['eq','ne','in','ni','cn','nc']}
									                            			}
								                            				,   
									                            				{
											    						        name:'fh_evento',
											    						        index:'fh_evento',
											    						        //hidden:no
											    						        width:120, 
											    						        align:"left",
											    						        sortable: true, 
											    						        editable:false,
											    						        //edittype:
											    						        //stype:no
											    						        search: true,
											    						        editoptions:{readonly:false, maxlength:30},
									                		                    formoptions:{label: "Fecha Evento ", rowpos:5 },
																				searchoptions:{dataInit:function(el){$(el).datepicker({dateFormat:'yy/mm/dd'});}
												                                					 					,
										            		                                						sopt:['eq','ne','lt','le','gt','ge']
											            		                               }
																													

						                            						 	}
					                            						 	 	,   
										                            				 
																							{
																							name:'tx_modulo',
											    											index:'tx_modulo',
											    											//hidden:no
											    											width:150, 
											    											align:"left",
											    											sortable: true,
											    											search: true,
											            		                            formoptions:{label: "Modulo", rowpos:6 },
																							serchoptions:{sopt:['eq','ne','in','ni','cn','nc']} 
											                            					}
										                            						,      


																							{
																							name:'tx_name',
											    											index:'tx_name',
											    											//hidden:no
											    											width:150, 
											    											align:"left",
											    											sortable: true,
											    											search: true,
											            		                            formoptions:{label: "Seccion", rowpos:7 },
																							serchoptions:{sopt:['eq','ne','in','ni','cn','nc']} 
											                            					}
										                            						,

										                            						                                                                    
												                            					{
												                            					name:'B.tx_programa',
												                                				index:'B.tx_programa',
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
												            		                            formoptions:{label: "Programa", rowpos:8 },
												                                				serchoptions:{sopt:['eq','ne','in','ni','cn','nc']}
																								}
											                            						,
													                            					{
													                            					name:'tx_remote_ip',
													                                				index:'tx_remote_ip',
													                                				//hidden:no
													                                				width:50, 
													                                				align:"left",
													                                				sortable: true,  
													                                				editable:false,
													                                				//edittype:no ,
													    											//stype: no ,
													    											search: true,
													                                				editoptions:{readonly:true},
													                                				//editrules:no ,
													                                				formoptions:{label: "IP Remota: ", rowpos:9 },
													                                				searchoptions:{sopt:['eq','ne','cn','nc']}
													                            					}
													                            					,
														                            					{
														                            					name:'tx_forward_ip',
														                                				index:'tx_forward_ip',
														                                				//hidden:no
														                                				width:50, 
														                                				align:"center", 
														                                				sortable: true,  
														                                				editable:false,
														                                				//edittype:no ,
														    											//stype: no ,
														                                				editoptions:{readonly:true},
														                                				//editrules:no ,
														                                				formoptions:{label: "IP Forrdward ", rowpos:10 },
														                                				searchoptions:{sopt:['eq','ne','cn','nc']}
														                                					
															                                			}
														                                				,
															                                				{
															                                				name:'tx_client_ip',
															                                				index:'tx_client_ip',
															                                				//hidden:no
															                                				width:50, 
															                                				align:"left", 
															                                				sortable: true, 
															                                				editable:false,
															                                				//edittype:no ,
															    											//stype: no ,
															                                				editoptions:{readonly:true},
															                                				//editrules:no ,
															                                				formoptions:{label: "IP Cliente", rowpos:11 },
															                                				searchoptions:{sopt:['eq','ne','cn','nc']}
															                            					}
																											
																											,
																												{
																												name:'tx_tabla',
																												index:'tx_tabla',
																												//hidden:no
																												width:100, 
																												align:"left", 
																												sortable: true, 
																												editable:false,
																												//edittype:no ,
																												//stype: no ,
																												editoptions:{readonly:true},
																												//editrules:no ,
																												formoptions:{label: "Tabla CSI", rowpos:12 },
																												searchoptions:{sopt:['eq','ne','in','ni','cn','nc']}
																												}
																												,
																													
																													
																														{
																														name:'tx_valores',
																														index:'tx_valores',
																														//hidden:no
																														width:500, 
																														align:"left", 
																														sortable: true, 
																														editable:true,
																														//edittype:no ,
																														//stype: no ,
																														editoptions:{readonly:false},
																														//editrules:no ,
																														formoptions:{label: "Valores", rowpos:13 },
																														searchoptions:{sopt:['cn','nc']}
																														}
																												,
																												{
																													name:'id_key',
																													index:'id_key',
																													//hidden:no
																													width:100, 
																													align:"right", 
																													sortable: true, 
																													editable:false,
																													//edittype:no ,
																													//stype: no ,
																													editoptions:{readonly:true},
																													//editrules:no ,
																													formoptions:{label: "Clave CSI", rowpos:14 },
																													searchoptions:{sopt:['eq','ne','cn','nc']}
																													}
																												
																												
														          ],
			                        					pager: '#pagerCat', // Nombre del paginador
                                       					altRows: true, // Activa la visualizacion de zebra en las filas
                                       					imgpath: "/css/ui-personal/images",
                                       					toolbar: [true,"bottom"], // Si cuenta con barra de herramienta y posicion de la misma
                                      					rowNum:200,  // numero de filas por pagina
                                       					rowList:[200,400,800],  // opciones de filas por pagina
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
	           					                        sortname: 'fh_evento', // primer columna de ordenacion
	           					                        sortorder: 'desc',       // tipo de ordenacion inicial
														
	           					                     	footerrow : true,     
	           											userDataOnFooter : true,              
	           				                        	onSelectRow: function(id)
	           				                        							{
	           				                            							
	           				                        							},


					                					                        loadError : function(xhr,st,err) {
					                					                                						jAlert(true,true,"Error cargando grid Type: "+st+"; Response: "+ xhr.status + " "+xhr.statusText,function(){	$('#dialogMain').dialog("close");	});
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

		               // $("#t_list1").append(createButtons());      // Se agregan los botones al toolbar
		                
						}
				);

	


</script>        
 </div>
 </form>
<?php 
}
?>