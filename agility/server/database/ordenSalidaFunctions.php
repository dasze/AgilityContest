<?php
/*
ordenSalidaFunctions.php

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
require_once(__DIR__."/classes/OrdenSalida.php");

$file="ordenSalidaFunctions";

try {
	$result=null;
	$os=new OrdenSalida($file);
	// retrieve variables
	$operation=http_request("Operation","s",null);
	if ($operation===null) 
		throw new Exception("Call to ordenSalidaFunctions without 'Operation' requested");
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	$m = http_request("Manga","i",0);
	$d = http_request("Perro","i",0);
	// los siguiente campos se usan para drag and drop
	$f = http_request("From","i",0);
	$t = http_request("To","i",0);
	$w = http_request("Where","i",0);
	if (($p<=0) || ($j<=0) || ($m<=0)) 
		throw new Exception("Call to ordenSalidaFunctions with Invalid Prueba:$p Jornada:$j or manga:$m ID");
	switch ($operation) {
		case "random":	$result = $os->random($j,$m); break;
		case "reverse":	$result = $os->reverse($j,$m); break;
		case "getData":	$result = $os->getData($p,$j,$m); break;
		case "dnd":	$result = $os->dragAndDrop($j,$m,$f,$t,$w); break;
	}
	// result may contain null (error),  "" success, or (any) data
	if ($result===null) throw new Exception($os->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>