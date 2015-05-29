 <!-- 
dlg_jueces.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE JUECES -->
    <div id="jueces-dialog" style="width:450px;height:auto;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del juez</div>
        <form id="jueces-form" method="get" novalidate>
            <div class="fitem">
                <label for="jueces-Nombre">Nombre:</label>
                <input id="jueces-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:250px" />
                <input id="jueces-ID" name="ID" type="hidden" /> 
                <input id="jueces-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="jueces-Direccion1">Direccion 1:</label>
                <input id="jueces-Direccion1" class="easyui-validatebox" name="Direccion1" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="jueces-Direccion2">Direccion 2:</label>
                <input id="jueces-Direccion2" class="easyui-validatebox" name="Direccion2" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="jueces-Pais">Pa&iacute;s:</label>
                <select id="jueces-Pais" name="Pais" class="easyui-validatebox" style="width:200px"></select>
            </div>
            <div class="fitem">
                <label for="jueces-Telefono">Tel&eacute;fono:</label>
                <input id="jueces-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="jueces-Internacional">Juez internacional:</label>
                <input id="jueces-Internacional" name="Internacional" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
            <div class="fitem">
                <label for="jueces-Practicas">Juez en pr&aacute;cticas:</label>
                <input id="jueces-Practicas" name="Practicas" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
        	<div class="fitem">
        	    <label for="jueces-Federations">Federaciones:</label>
        	    RSCE <input id="jueces-RSCE" type="checkbox" value="1" name="RSCE"/>
        	    RFEC <input id="jueces-RFEC" type="checkbox" value="2" name="RFEC"/>
        	    UCA<input id="jueces-UCA" type="checkbox" value="4" name="UCA"/>
        	    <input id="jueces-Federations" name="Federations" type="hidden"/>
        	</div>
            <div class="fitem">
                <label for="jueces-Email">Correo electr&oacute;nico:</label>
                <input id="jueces-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="jueces-Observaciones">Observaciones:</label>
                <textarea id="jueces-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="jueces-dlg-buttons">
   	    <a id="jueces-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveJuez()">Guardar</a>
   	    <a id="jueces-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#jueces-dialog').dialog('close')">Cancelar</a>
   	</div>
   	
	<script type="text/javascript">
        // datos del formulario de nuevo/edit jueces

        // - declaracion del formulario
        $('#jueces-form').form({
            onLoadSuccess: function(data) {
                // take care on internacional & practice checkbox
                $('#jueces-Internacional').prop('checked',(data.Internacional == 1));
                $('#jueces-Practicas').prop('checked',(data.Practicas == 1));
            }
        });


        // despliegue del selector de paises
        $('#jueces-Pais').combogrid({
            panelWidth: 300,
            panelHeight: 200,
            idField: 'ID',
            textField: 'Country',
            url: '/agility/server/database/juezFunctions.php?Operation=countries',
            method: 'get',
            mode: 'remote',
            required: true,
            columns: [[
                {field:'ID',title:'ID',width:10,align:'right'},
                {field:'Country',title:'Pais',width:40,align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            selectOnNavigation: false
        });

        // - botones
		addTooltip($('#jueces-okBtn').linkbutton(),"Registrar los datos del juez y actualizar la BBDD");
		addTooltip($('#jueces-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
        // campos del formulario
        $('#jueces-dialog').dialog({
            closed: true,
            buttons: '#jueces-dlg-buttons',
            iconCls: 'icon-whistle'
        });
        $('#jueces-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#jueces-dialog').dialog('close');
        	}
        });
        $('#jueces-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#jueces-Email').validatebox({
            required: false,
            validType: 'email'
        });
        </script>