<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE CLUBES -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="clubes-datagrid" class="easyui-datagrid" style="width:975px;height:550px;"></table>
    
	<!-- BARRA DE TAREAS DE LA TABLA DE CLUBES-->
    <div id="clubes-toolbar" style="padding:2px 2px 10px 2px;>
    	<span style="float:left;">
    		<a id="clubes-newBtn" href="#" class="easyui-linkbutton" onclick="newClub($('#clubes-search').val())">Nuevo Club</a>
    		<a id="clubes-editBtn" href="#" class="easyui-linkbutton" onclick="editClub()">Editar Club</a>
    		<a id="clubes-delBtn" href="#" class="easyui-linkbutton" onclick="deleteClub()">Borrar Club</a>
    		<input id="clubes-search" type="text" value="----- Buscar -----" class="search_textfield"/>
    	</span>
    	<span style="float:right;">
    		<a id="clubes-reloadBtn" href="#" class="easyui-linkbutton"">Actualizar</a>
    	</span>
    </div>   

    <?php include_once("dialogs/dlg_clubes.inc")?>
    <?php include_once("dialogs/dlg_guias.inc")?>
    <?php include_once("dialogs/dlg_chguias.inc")?>
    <?php include_once("dialogs/dlg_perros.inc")?>
    <?php include_once("dialogs/dlg_chperros.inc")?>

    <script type="text/javascript">

    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Base de Datos de Clubes</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de clubes
        // - tabla
        $('#clubes-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Clubes',
        	url: 'database/clubFunctions.php?Operation=select',
        	method: 'get',
            toolbar: '#clubes-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: detailview,
            columns: [[
                  	{ field:'ID',			hidden:true},
                   	{ field:'Nombre',		width:10, sortable:true,	title: 'Nombre:'},
            		{ field:'Direccion1',	width:15, sortable:true,	title: 'Direcci&oacute;n 1:' },
            		{ field:'Direccion2',	width:10, sortable:false,	title: 'Direcci&oacute;n 2' },
            		{ field:'Provincia',	width:7, sortable:false,   title: 'Provincia' },
            		{ field:'Contacto1',	width:10, sortable:false,   title: 'Contacto 1' },
            		{ field:'Contacto2',	width:5, sortable:true,    title: 'Contacto 2' },
            		{ field:'Contacto3',	width:5, sortable:true,    title: 'Contacto 3' },
            		{ field:'GPS',			width:7, sortable:true,    title: 'GPS' },
            		{ field:'Web',			width:5, sortable:true,    title: 'Direcci&oacute;n Web' },
            		{ field:'Email',		width:5, sortable:true,    title: 'Correo Electr&oacute;nico' },
            		{ field:'Facebook',		width:2, sortable:true,    title: 'Facebook' },
            		{ field:'Google',		width:2, sortable:true,    title: 'Google +' },
            		{ field:'Twitter',		width:2, sortable:true,    title: 'Twitter' },
            		// { field:'Logo',			width:2, sortable:true,    title: 'Logo club' },
            		//{ field:'Observaciones',width:2, sortable:true,    title: 'Observaciones' },
            		{ field:'Baja',			width:2, sortable:true,    title: 'Baja' }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editClub();
            },        
            // especificamos un formateador especial para desplegar la tabla de guias por club
            detailFormatter:function(index,club){
                return '<div style="padding:2px"><table class="easyui-datagrid"	id="guias-datagrid-' + replaceAll(' ','_',club.ID) + '"></table></div>';
            },
            onExpandRow: function(idx,club) { showGuiasByClub(idx,club); }
        }); // end of '#clubes-datagrid' declaration

        // activa teclas up/down para navegar por el panel
        $('#clubes-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
            function selectRow(t,up){
            	var count = t.datagrid('getRows').length;    // row count
            	var selected = t.datagrid('getSelected');
            	if (selected){
                	var index = t.datagrid('getRowIndex', selected);
                	index = index + (up ? -1 : 1);
                	if (index < 0) index = 0;
                	if (index >= count) index = count - 1;
                	t.datagrid('clearSelections');
                	t.datagrid('selectRow', index);
            	} else {
                	t.datagrid('selectRow', (up ? count-1 : 0));
            	}
        	}
			function selectPage(t,offset) {

            	var count = t.datagrid('getRows').length;    // row count
            	var selected = t.datagrid('getSelected');
            	if (selected){
                	var index = t.datagrid('getRowIndex', selected);
                	switch(offset) {
                	case 1: index+=10; break;
                	case -1: index-=10; break;
                	case 2: index=count -1; break;
                	case -2: index=0; break;
                	}
                	if (index<0) index=0;
                	if (index>=count) index=count-1;
                	t.datagrid('clearSelections');
                	t.datagrid('selectRow', index);
            	} else {
                	t.datagrid('selectRow', 0);
            	}
			}
			
        	var t = $('#clubes-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editClub(); return false;
            case 45:	/* Insert */ newClub($('#clubes-search').val()); return false;
            case 46:	/* Supr */	deleteClub(); return false;
            case 33:	/* Re Pag */ selectPage(t,-1); return false;
            case 34:	/* Av Pag */ selectPage(t,1); return false;
            case 35:	/* Fin */    selectPage(t,2); return false;
            case 36:	/* Inicio */ selectPage(t,-2); return false;
            case 9: 	/* Tab */
                // if (e.shiftkey) return false; // shift+Tab
                return false;
            case 16:	/* Shift */
            case 17:	/* Ctrl */
            case 18:	/* Alt */
            case 27:	/* Esc */
                return false;
            }
		}); 
		
        // - botones de la toolbar de la tabla
        $('#clubes-newBtn').linkbutton({iconCls:'icon-flag',plain:true}).tooltip({ // nuevo club        
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo club en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#clubes-editBtn').linkbutton({iconCls:'icon-edit',plain:true}).tooltip({ // editar club   
        	position: 'top',
        	content: '<span style="color:#000">Editar los datos del club seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    	});
        $('#clubes-delBtn').linkbutton({iconCls:'icon-trash',plain:true}).tooltip({ // borrar club
            position: 'top',
            content: '<span style="color:#000">Borrar el club seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#clubes-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // actualizar tabla
        $('#clubes-reloadBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Borrar casilla de busqueda y actualizar tabla</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
    	$('#clubes-reloadBtn').on("click", function () {
        	// clear selection and reload table
    		$('#clubes-search').val('----- Buscar -----');
            $('#clubes-datagrid').datagrid('load',{ where: '' });
    	});
        $("#clubes-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#clubes-datagrid').datagrid('load',{
                where: $('#clubes-search').val()
            });
        });
        $('#clubes-search').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        
    	
        function showGuiasByClub(index,club){
        	// - sub tabla de guias inscritos en un club
        	$('#guias-datagrid-'+replaceAll(' ','_',club.ID)).datagrid({
            	width: 900,
            	fit:false,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
                view: detailview,
        	    height: 'auto',
        		title: 'Gu&iacute;as inscritos en el club '+club.Nombre,
        	    loadMsg: 'Cargando lista de guias....',
        		url: 'database/guiaFunctions.php?Operation=getbyclub&Club='+club.ID,
        		method: 'get',
        		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        		toolbar: [{
            		id: 'guiasByClub-newBtn',
            		text: 'Asociar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-users',
        			handler: function(){assignGuiaToClub(club);}
        		},{
            		id: 'guiasByClub-editBtn',
            		text: 'Editar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-edit',
        			handler: function(){editGuiaFromClub(club);}
        		},{
            		id: 'guiasByClub-delBtn',
            		text: 'Des-asociar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-remove',
        			handler: function(){delGuiaFromClub(club);}
        		}],
        	    columns: [[
        	        { field:'ID',			hidden:true },	
        	    	{ field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
        	    	{ field:'Telefono',	width:15, sortable:true,	title: 'Tel&eacute;fono' },
        	    	{ field:'Email',		width:25, sortable:true,    title: 'Correo Electr&oacute;nico' },
        	    	{ field:'Observaciones',width:15,					title: 'Observaciones'}
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:function(idx,row) { 
            	    return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            	},
                onResize:function(){
                    $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                },
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editGuiaFromClub(club);
                },
                // especificamos un formateador especial para desplegar la tabla de perros por guia
                detailFormatter:function(index,guia){
                    return '<div style="padding:2px"><table class="easyui-datagrid" id="perros-datagrid-' + replaceAll(' ','_',guia.ID) + '"></table></div>';
                },
                
                onExpandRow: function(idx,guia) { showPerrosByGuiaByClub(idx,guia,club); },
                /* end of clubes-guias-dog subtable */
                onResize:function(){
                    $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of '#clubes-guias-datagrid' declaration
        	$('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
        	
            //** botones del subformulario guiasByClub
            $('#guiasByClub-newBtn').linkbutton().tooltip({ // anyadir entrada a lalista de guias del club
                position: 'top',
                content: '<span style="color:#000">Asociar/Crear nuevo guia en el club seleccionado</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });          
            $('#guiasByClub-editBtn').linkbutton().tooltip({ // eliminar entrada lista de guias del club
                position: 'top',
                content: '<span style="color:#000">Editar datos del gu&iacute;a seleccionado del club</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });      
            $('#guiasByClub-delBtn').linkbutton().tooltip({ // eliminar entrada lista de guias del club
                position: 'top',
                content: '<span style="color:#000">Desasociar al gu&iacute;a seleccionado del club</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });
            	
        } // end of "showGuiasByClub"
        
		// mostrar los perros asociados a un guia
        function showPerrosByGuiaByClub(index,guia,club){
            var parent='#guias-datagrid-'+replaceAll(' ','_',club.ID);
        	// - sub tabla de perros asignados a un guia
        	$('#perros-datagrid-'+replaceAll(' ','_',guia.ID)).datagrid({
            	fit:false,
            	width: 850,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
        	    height: 'auto',
        	    loadMsg: 'Loading list of dogs',
        		title: 'Perros registrados a nombre de '+guia.Nombre,
        		url: 'database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID },
        		method: 'get',
        		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        		// toolbar: '#perrosbyguia-toolbar', 
				toolbar:  [{
					id: 'perrosByGuia-newBtn',
					text: 'Asignar perro',
					plain: true,
					iconCls: 'icon-dog',
					handler: function(){assignPerroToGuia(guia);},
				},{
					id: 'perrosByGuia-editBtn',
					text: 'Editar datos',
					plain: true,
					iconCls: 'icon-edit',
					handler: function(){editPerroFromGuia(guia);}
				},{
					id: 'perrosByGuia-delBtn',
					text: 'Desasignar perro',
					plain: true,
					iconCls: 'icon-remove',
					handler: function(){delPerroFromGuia(guia);}
				}],
        	    columns: [[
            	    { field:'ID',		width:15, sortable:true,	title: 'ID'},
            		{ field:'Nombre',	width:30, sortable:true,	title: 'Nombre:' },
            		{ field:'Categoria',width:15, sortable:false,	title: 'Cat.' },
            		{ field:'Grado',	width:25, sortable:false,   title: 'Grado' },
            		{ field:'Raza',		width:25, sortable:false,   title: 'Raza' },
            		{ field:'LOE_RRC',	width:25, sortable:true,    title: 'LOE / RRC' },
            		{ field:'Licencia',	width:25, sortable:true,    title: 'Licencia' }
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:function(idx,row) { 
            	    return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            	},
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editPerroFromGuia(guia);
                },
                onResize:function(){
                    $(parent).datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $(parent).datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of perrosbyguia-datagrid-Nombre_del_Guia
        	$(parent).datagrid('fixDetailRowHeight',index);

            // botones de los sub-formularios
            $('#perrosByGuia-newBtn').linkbutton().tooltip({ // anyadir nuevo perro al guia
                position: 'top',
                content: '<span style="color:#000">Asignar un nuevo perro al guia</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });     
            $('#perrosByGuia-delBtn').linkbutton().tooltip({ // desasignar perro al guia
                position: 'top',
                content: '<span style="color:#000">Eliminar asignaci&oacute;n del perro al gu&iacute;a</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });        
            $('#perrosByGuia-editBtn').linkbutton().tooltip({ // editar datos del perro asignado al guia
                position: 'top',
                content: '<span style="color:#000">Editar los datos del perro asignado</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });
        } // end of showPerrosByGuia
	</script>

