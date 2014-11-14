<?php
/*
ordenTandasFunctions.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/** mandatory requires for database and logging */
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/classes/DBConnection.php");
require_once(__DIR__."/classes/OrdenTandas.php");

$file="ordenTandasFunctions";

try {
	$result=null;
	$ot=new OrdenTandas($file);
	// retrieve variables
	$operation=http_request("Operation","s",null);
	if ($operation===null) 
		throw new Exception("Call to ordenTandasFunctions without 'Operation' requested");
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	$td = http_request("Tanda","i",0);
	// los siguiente campos se usan para drag and drop
	$f = http_request("From","i",0);
	$t = http_request("To","i",0);
	$w = http_request("Where","i",0); // 0:up 1:down
	$a = http_request("Pendientes","i",0); // 0: listado completo; else retorna hasta "n" perros pendientes
	if ( ($p<=0) || ($j<=0) ) 
		throw new Exception("Call to ordenTandasFunctions with Invalid Prueba:$p or Jornada:$j ID");
	switch ($operation) {
		case "getTandas":$result = $ot->getTandas($p,$j); break;
		case "getData":	$result = $ot->getData($p,$j,$a,$td); break;
		case "getDataByTanda":	$result = $ot->getDataByTanda($p,$j,$td); break;
		case "dnd":	$result = $ot->dragAndDrop($p,$j,$f,$t,$w); break;
	}
	// result may contain null (error),  "" success, or (any) data
	if ($result===null) throw new Exception($ot->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>