<?php 
require_once(__DIR__."/../server/auth/Config.php");
$config =new Config()
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="application-name" content="Agility Contest" />
<meta name="copyright" content="© 2013-2015 Juan Antonio Martinez" />
<meta name="author" lang="en" content="Juan Antonio Martinez" />
<meta name="description"
        content="A web client-server (xampp) app to organize, register and show results for FCI Dog Agility Contests" />
<meta name="distribution" 
	content="This program is free software; you can redistribute it and/or modify it under the terms of the 
		GNU General Public License as published by the Free Software Foundation; either version 2 of the License, 
		or (at your option) any later version." />
<!-- try to disable zoom in tablet on double click -->
<meta content='width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=no;' name='viewport' />
<title>Agility Contest</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/tablet.css" />
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/tablet/tablet.js" type="text/javascript" charset="utf-8" > </script>
<script type="text/javascript" charset="utf-8">
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR, settings) {
		if (!workingData.sessionKey) return;
		jqXHR.setRequestHeader('X-AC-SessionKey',workingData.sessionKey);
	    return true;
	  }
	});
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @paramm {integer} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
	var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";" } else { return res+c2+";" }
}
</script>
<style>
body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }
</style>
</head>

<body style="margin:0;padding:0" onload="initialize();">

<div id="tablet_contenido" style="width:100%;height:100%;margin:0;padding:0"></div>

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="seltablet-dialog" class="easyui-dialog" style="position:relative,width:600px;height:250px;padding:20px 20px">
	<form id="seltablet-form">
    	<div class="fitem">
       		<label for="Sesion">Sesi&oacute;n:</label>
       		<select id="seltablet-Sesion" name="Sesion" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="Prueba">Selecciona Prueba:</label>
       		<select id="seltablet-Prueba" name="Prueba" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="Jornada">Selecciona Jornada:</label>
       		<select id="seltablet-Jornada" name="Jornada" style="width:200px"></select>
    	</div>
	</form>
</div> <!-- Window -->

<div id="seltablet-Buttons" style="text-align:right">
   	<a id="seltablet-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls:'icon-ok'" onclick="tablet_acceptSelectJornada()">Aceptar</a>
</div>	<!-- botones -->

<script type="text/javascript">
$('#seltablet-dialog').dialog({
	title: 'Selecciona la Prueba y Jornada a desplegar',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: true,
	modal: true,
	buttons: '#seltablet-Buttons' 
});

$('#seltablet-form').form();

$('#seltablet-Prueba').combogrid({
	panelWidth: 400,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	   	    {field:'ID',hidden:true},
			{field:'Nombre',title:'Nombre',width:50,align:'right'},
			{field:'Club',hidden:true},
			{field:'NombreClub',title:'Club',width:20,align:'right'},
			{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	onChange:function(value){
		workingData.prueba=Number(value);
		var g = $('#seltablet-Jornada').combogrid('grid');
		g.datagrid('load',{Prueba:value});
	}
});

$('#seltablet-Sesion').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/sessionFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	rownumber: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,
	columns: [[
	   	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
			{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: 'Nombre' },
			{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: 'Observaciones' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate'
		return true;
	}
});

$('#seltablet-Jornada').combogrid({
	panelWidth: 550,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,
	columns: [[
	    { field:'ID',			hidden:true }, // ID de la jornada
	    { field:'Prueba',		hidden:true }, // ID de la prueba
	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
		{ field:'Nombre',		width:30, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
		{ field:'Fecha',		hidden:true},
		{ field:'Hora',			hidden:true},
		{ field:'Grado1',		width:8, sortable:false,	align:'center', title: 'G-I    ' },
		{ field:'Grado2',		width:8, sortable:false,	align:'center', title: 'G-II   ' },
		{ field:'Grado3',		width:8, sortable:false,	align:'center', title: 'G-III  ' },
		{ field:'Open',		    width:8, sortable:false,	align:'center', title: 'Open   ' },
		{ field:'Equipos3',		width:8, sortable:false,	align:'center', title: 'Eq.3x4 ' },
		{ field:'Equipos4',		width:8, sortable:false,	align:'center', title: 'Eq.Conj' },
		{ field:'PreAgility',	width:8, sortable:false,	align:'center', title: 'Pre. 1 ' },
		{ field:'PreAgility2',	width:8, sortable:false,	align:'center', title: 'Pre. 2 ' },
		{ field:'KO',			width:8, sortable:false,	align:'center', title: 'K.O.   ' },
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' },
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate', 
		param.Prueba=workingData.prueba;
		param.AllowClosed=0;
		return true;
	}
});

function tablet_acceptSelectJornada() {
	// si prueba invalida cancelamos operacion
	var s=$('#seltablet-Sesion').combogrid('grid').datagrid('getSelected');
	var p=$('#seltablet-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#seltablet-Jornada').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.alert("Error","Debe<br />- Indicar la sesión para los videomarcadores<br />- Seleccionar prueba/jornada para manejo de datos","error");
		return;
	}

	// update database session info with provided operator data
	updateSessionInfo(s.ID,{Nombre: s.Nombre,Prueba:p.ID, Jornada:j.ID});
	// los demas valores se actualizan en la linea anterior
	workingData.nombreSesion=s.Nombre;
	workingData.nombrePrueba=p.Nombre;
	workingData.nombreJornada=j.Nombre;
	// notify session open() to event manager
	tablet_putEvent(
		'init',
		{ 'Session': s.ID, 'Source': 'tablet_'+s.ID, 'Prueba': p.ID, 'Jornada': j.ID, 'Manga':	0, 'Tanda':	0, 'Perro':	0 }
	);

	var page="/agility/tablet/tablet_competicion.php";
	if (workingData.datosJornada.Equipos3==1) {
		page="/agility/tablet/tablet_competicion_eq3.php";
	}
	if (workingData.datosJornada.Equipos4==1) {
		page="/agility/tablet/tablet_competicion_eq4.php";
	}
	if (workingData.datosJornada.Open==1) {
		page="/agility/tablet/tablet_competicion_open.php";
	}
	if (workingData.datosJornada.KO==1) {
		page="/agility/tablet/tablet_competicion_ko.php";
	}
	$('#seltablet-dialog').dialog('close');
	$('#tablet_contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#tablet_contenido').load('/agility/frm_notavailable.php');
			}
		);
		
}
</script>
</body>
</html> 