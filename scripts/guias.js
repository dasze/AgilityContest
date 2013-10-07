/**
 * Abre el formulario para anyadir perros a un guia
 *@param index: indice que ocupa el guia en la entrada principal
 *@param guia: nombre del guia
 */
function addPerroToGuia(guia) {
	$('#perros-dialog').dialog('open').dialog('setTitle','Crear un nuevo perro y asignarlo a '+guia.Nombre);
	$('#perros-form').form('clear'); // erase form
	$('#perros-Guia').combogrid({ 'value': guia.Nombre} ); 
	$('#perros-Guia').combogrid({ 'readonly': true }); // mark guia as read-only
	$('#perros-Operation').val('insert');
}

/**
* Abre el formulario para anyadir perros a un guia
*@param index: indice que ocupa el guia en la entrada principal
*@param guia: nombre del guia
*/
function editPerroFromGuia(guia) {
	// try to locate which dog has been selected 
    var row = $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('getSelected');
    if (!row) return; // no way to know which dog is selected
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
    $('#perros-form').form('load',row);
	$('#perros-Guia').combogrid({ 'value': guia.Nombre} ); 
	$('#perros-Guia').combogrid({ 'readonly': true }); // mark guia as read-only
    $('#perros-Operation').val('update');
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 */
function delPerroFromGuia(guia) {
    var row = $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{Operation:'orphan',Dorsal:row.Dorsal},function(result){
                if (result.success){
                    $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('reload');    // reload the guia data
                } else {
                    $.messager.show({    // show error message
                        title: 'Error',
                        msg: result.errorMsg
                    });
                }
            },'json');
        }
    });
}

/**
 * Recalcula el formulario anyadiendo parametros de busqueda
 */
function doSearchGuia() {
	// reload data adding search criteria
    $('#guias-datagrid').datagrid('load',{
        where: $('#guias-search').val()
    });
}

/**
 * Open "Guia dialog"
 */
function newGuia(){
	$('#guias-dialog').dialog('open').dialog('setTitle','Nuevo g&uiacute;a');
	$('#guias-form').form('clear');
	$('#guias-Operation').val('insert');
}

/**
 * Open "Edit guia" dialog
 */
function editGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
    $('#guias-form').form('load',row);
    // take care on int-to-bool translation for checkboxes
    $('#guias-Baja').prop('checked',(row.Baja==1)?true:false);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#guias-Viejo').val( $('#guias-Nombre').val());
	$('#guias-Operation').val('update');
}

/**
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
	// take care on bool-to-int translation from checkboxes to database
    $('#guias-Baja').val( $('#guias-Baja').is(':checked')?'1':'0');
    // do normal submit
    $('#guias-form').form('submit',{
        url: 'database/guiaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(result){
            var result = eval('('+result+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#guias-dialog').dialog('close');        // close the dialog
                $('#guias-datagrid').datagrid('reload');    // reload the guia data
            }
        }
    });
}

/**
 * Delete guia data
 */
function destroyGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm','Borrar datos del guia. ¿Seguro?',function(r){
        if (r){
            $.get('database/guiaFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
                if (result.success){
                    $('#guias-datagrid').datagrid('reload');    // reload the guia data
                } else {
                    $.messager.show({    // show error message
                        title: 'Error',
                        msg: result.errorMsg
                    });
                }
            },'json');
        }
    });
}