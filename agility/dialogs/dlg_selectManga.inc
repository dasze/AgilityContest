<!-- FORMULARIO DE SELECCION DE RONDAS ABIERTAS-->
<div id="selmanga-window" class="easyui-window" style="position:relative,width:500px;height:250px;padding:20px 20px">
	<div id="selmanga-Layout" class="easyui-layout" data-options="fit:'true'">
		<form id="selmanga-Selection">
			<div id="selmanga-Content" data-options="region:'north',border:'true'">
        		<div class="fitem">
            		<label for="Prueba">Selecciona Prueba:</label>
            		<select id="selmanga-Prueba" name="Prueba" class="easyui-combogrid" style="width:200px"></select>
        		</div>        		
        		<div class="fitem">
            		<label for="Jornada">Selecciona Jornada:</label>
            		<select id="selmanga-Jornada" name="Jornada" class="easyui-combogrid" style="width:200px"></select>
        		</div>      		
        		<div class="fitem">
            		<label for="Ronda">Selecciona Ronda:</label>
            		<select id="selmanga-Ronda" name="Ronda" class="easyui-combogrid" style="width:200px"></select>
        		</div>
			</div> <!-- contenido -->
		</form>
		<div data-options="region:'center'">&nbsp;</div>
		<div id="selmanga-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="selmanga-okBtn" href="#" class="easyui-linkbutton" onclick="acceptSelectManga()">Aceptar</a>&nbsp;
    	    <a id="selmanga-cancelBtn" href="#" class="easyui-linkbutton" onclick="cancelSelectManga()">Cancelar</a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">
$('#selmanga-window').window({
	title: 'Selecciona la Prueba, Jornada y Ronda',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true
});

$('#selmanga-form').form();
$('#selmanga-layout').layout();

$('#selmanga-okBtn').linkbutton({ iconCls: 'icon-ok' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Trabajar con la ronda seleccionada</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#selmanga-cancelBtn').linkbutton({ iconCls: 'icon-cancel' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Cancelar selecci&oacute;n. Cerrar ventana</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#selmanga-Prueba').combogrid({
	panelWidth: 450,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	    {field:'ID',hidden:true},
		{field:'Nombre',title:'Nombre',width:50,align:'right'},
		{field:'Club',title:'Club',width:20,align:'right'},
		{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	onSelect:function(index,row) {
		workingData.prueba=row.ID;
		workingData.datosPrueba=row;
		var g = $('#selmanga-Jornada').combogrid('grid');
		g.datagrid('load',{ Prueba : workingData.datosPrueba.ID } );
	}
});

$('#selmanga-Jornada').combogrid({
	panelWidth: 400,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	columns: [[
	    { field:'ID',			hidden:true }, // ID de la jornada
	    { field:'Prueba',		hidden:true }, // ID de la prueba
	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
		{ field:'Nombre',		width:30, sortable:false,   align:'right', title: 'Nombre/Comentario' },
		{ field:'Fecha',		width:20, sortable:false,	align:'right', title: 'Fecha:' },
		{ field:'Hora',			width:15, sortable:false,	align:'right', title: 'Hora.' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate'; 
		param.Prueba=workingData.prueba; 
		return true; 
	},
	onSelect:function(index,row) {
		workingData.jornada=row.ID;
		workingData.datosJornada=row;
		var g = $('#selmanga-Ronda').combogrid('grid');
		g.datagrid('load');
	}
});

$('#selmanga-Ronda').combogrid({
	panelWidth: 200,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	columns: [[
	   	{ field:'Manga1',			hidden:true }, // ID de la manga1
		{ field:'Manga2',			hidden:true }, // ID de la manga2
		{ field:'Nombre1',			hidden:true }, // Tipo de la manga1
		{ field:'Nombre2',			hidden:true }, // ITipo de la manga2
		{ field:'Juez11',			hidden:true }, // jueces manga 1
		{ field:'Juez12',			hidden:true }, 
		{ field:'Juez21',			hidden:true }, // jueces manga 2
		{ field:'Juez22',			hidden:true }, 
		{ field:'Observaciones1',	hidden:true }, // Observaciones
		{ field:'Observaciones2',	hidden:true },
		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='rounds', 
		param.Prueba=workingData.prueba; 
		param.ID=workingData.jornada; 
		return true;
	},	
	onSelect:function(index,row) {
		workingData.datosRonda=row;
	}
});

function acceptSelectManga() {
	// si prueba invalida cancelamos operacion
	var p=$('#selmanga-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#selmanga-Jornada').combogrid('grid').datagrid('getSelected');
	var m=$('#selmanga-Ronda').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.alert("Error","Debe indicar una prueba y jornada v&aacute;lidas","error");
		return;
	}
	// clear selection to make sure next time gets empty
	$('#selmanga-Prueba').combogrid('setValue','');
	$('#selmanga-Jornada').combogrid('setValue','');
	$('#selmanga-Ronda').combogrid('setValue','');
	// store selected data into global structure
	workingData.prueba=p.ID;
	workingData.nombrePrueba=p.Nombre;
	workingData.jornada=j.ID;
	workingData.nombreJornada=j.Nombre;
	workingData.manga=m.Manga1;
	workingData.manga2=m.Manga2;
	$('#selmanga-window').window('close');	
}

function cancelSelectManga() {
	workingData.prueba=0;
	workingData.nombrePrueba="";
	workingData.jornada=0;
	workingData.nombreJornada="";
	workingData.manga=0;
	workingData.manga2=0;
	$('#selmanga-Prueba').combogrid('setValue','');
	$('#selmanga-Jornada').combogrid('setValue','');
	$('#selmanga-Ronda').combogrid('setValue','');
	$('#selmanga-window').window('close');
}
</script>