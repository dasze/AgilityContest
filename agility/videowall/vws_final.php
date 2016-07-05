<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::combinada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vws_final.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!--
Generic event handler template for simplified final rounds
Just holds a panel container ( to be filled depending on individual/team rounds ) and javascript code to handle
events
-->
<div id="vws-window">
    <h1><?php _e("Waiting for connection with event server");?>...</h1>
    <!-- to be filled depending on team/individual round -->
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronometro"><span id="vws_StartStopFlag" style="display:none">Start</span></div>

<script type="text/javascript">

    // create a Chronometer instance
    $('#cronometro').Chrono( {
        seconds_sel: '#vwls_timestamp', // on team contest, should be redirected to entries 0..3
        auto: false,
        interval: 50,
        showMode: 2,
        onUpdate: function(elapsed,running,paused) {
            var time=parseFloat(elapsed/1000.0);
            $('#vwls_Tiempo').html(toFixedT(time,(running)?1:ac_config.numdecs));
            if (!running && !paused) return true; // do not update penalization on stop
            vwcf_evalPenalizacion();
            return true;
        },
        onBeforePause:function() { $('#vwls_Tiempo').addClass('blink'); return true; },
        onBeforeResume: function() { $('#vwls_Tiempo').removeClass('blink'); return true; },
        onBeforeReset: function() { $('#vwls_Tiempo').removeClass('blink'); return true; }
    });


    $('#vws-window').window({
        fit:true,
        noheader:true,
        border:false,
        closable:false,
        collapsible:false,
        collapsed:false,
        resizable:true,
        onOpen: function() {
            startEventMgr();
        }
    });

    var eventHandler= {
        'null': null,// null event: no action taken
        'init': function (event, time) { // connection is made.
            vw_updateWorkingData(event,function(e,d){
                vws_updateHeaderAndFooter(e,d); // fix header. Do not add round as no data yet
                vws_setFinalIndividualOrTeamView(d); // fix individual or team view for final results
            });
        },
        'open': function (event, time) { // operator select tanda
            vw_updateWorkingData(event,function(e,d){
                vws_updateHeaderAndFooter(e,d); // fix header. ( add round information )
                vws_updateLlamada(e,d); // load call to ring data and existing results
            });
        },
        'close': function (event, time) { // no more dogs in tanda
            vw_updateWorkingData(event,function(e,d){
                vws_updateLlamada({'Dog':-1},d); // seek at end of list
            });
        },
        'datos': function (event, time) {      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
            vwls_updateData(event);
            vwcf_evalPenalizacion();
        },
        'llamada': function (event, time) {    // llamada a pista
            var crm=$('#cronometro');
            myCounter.stop();
            crm.Chrono('stop',time);
            crm.Chrono('reset',time);
            vw_updateWorkingData(event,function(e,d){
                vwcf_updateLlamada(e,d);
            });
        },
        'salida': function (event, time) {     // orden de salida
            myCounter.start();
            vwcf_displayPuesto(false,0);
        },
        'start': function (event, time) {      // start crono manual
            // si crono automatico, ignora
            var ssf = $('#vwls_StartStopFlag');
            if (ssf.text() === "Auto") return;
            ssf.text("Stop");
            myCounter.stop(); // stop 15 seconds countdown if needed
            var crm = $('#cronometro');
            crm.Chrono('stop', time);
            crm.Chrono('reset');
            crm.Chrono('start', time);
            vwcf_displayPuesto(false,0);
        },
        'stop': function (event, time) {      // stop crono manual
            var crm= $('#cronometro');
            $('#vwls_StartStopFlag').text("Start");
            myCounter.stop();
            crm.Chrono('stop', time);
            vwcf_displayPuesto(true,crm.Chrono('getValue')/1000);
        },
        // nada que hacer aqui: el crono automatico se procesa en el tablet
        'crono_start': function (event, time) { // arranque crono automatico
            vwcf_displayPuesto(false,0);
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text('Auto');
            // si esta parado, arranca en modo automatico
            if (!crm.Chrono('started')) {
                crm.Chrono('stop', time);
                crm.Chrono('reset');
                crm.Chrono('start', time);
                vwcf_evalPenalizacion();
                return
            }
            if (ac_config.crono_resync === "0") {
                crm.Chrono('reset'); // si no resync, resetea el crono y vuelve a contar
                crm.Chrono('start', time);
            } // else wait for chrono restart event
        },
        'crono_restart': function (event, time) {	// paso de tiempo manual a automatico
            $('#cronometro').Chrono('resync', event['stop'], event['start']);
        },
        'crono_int': function (event, time) {	// tiempo intermedio crono electronico
            var crm = $('#cronometro');
            if (!crm.Chrono('started')) return;	// si crono no esta activo, ignorar
            crm.Chrono('pause', time);
            setTimeout(function () {
                crm.Chrono('resume');
            }, 5000);
        },
        'crono_stop': function (event, time) {	// parada crono electronico
            var crm= $('#cronometro');
            $('#vwls_StartStopFlag').text("Start");
            crm.Chrono('stop', time);
            vwcf_displayPuesto(true,crm.Chrono('getValue')/1000);
        },
        'crono_reset': function (event, time) {	// puesta a cero del crono electronico
            var crm = $('#cronometro');
            myCounter.stop();
            $('#vwls_StartStopFlag').text("Start");
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
            vwcf_displayPuesto(false,0);
        },
        'crono_dat': function(event,time) {      // actualizar datos -1:decrease 0:ignore 1:increase
            vwls_updateChronoData(event);
            vwcf_evalPenalizacion();
        },
        'crono_error': null, // fallo en los sensores de paso
        'aceptar': function (event,time) { // operador pulsa aceptar
            myCounter.stop();
            $('#cronometro').Chrono('stop', time);  // nos aseguramos de que los cronos esten parados
            vw_updateWorkingData(event,function(e,d){
                /* vw_updateFinales(e,d); */ // required to be done at
            });
        },
        'cancelar': function (event,time) {  // operador pulsa cancelar
            var crm = $('#cronometro');
            myCounter.stop();
            crm.Chrono('stop', time);
            crm.Chrono('reset', time);
        },
        'camera':	null, // change video source
        'reconfig':	function(event,time) { loadConfiguration(); }, // reload configuration from server
        'info': null // click on user defined tandas
    };
</script>