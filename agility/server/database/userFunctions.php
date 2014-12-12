<?php

/*
userFunctions.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Usuarios.php");

try {
	$result=null;
	$users= new Usuarios("userFunctions");
	$am= new AuthManager("userFunctions");
	$operation=http_request("Operation","s",null);
	$id=http_request("ID","i",0);
	if ($operation===null) throw new Exception("Call to userFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $am->access(PERMS_ADMIN); $result=$users->insert(); break;
		case "update": $am->access(PERMS_ADMIN); $result=$users->update($id); break;
		case "delete": $am->access(PERMS_ADMIN); $result=$users->delete($id); break;
		case "passwd": $am->access(PERMS_ADMIN); $result=$users->setPassword($id); break;
		case "selectbyid": $result=$users->selectByID($id); break;
		case "select": $result=$users->select(); break; // list with order, index, count and where
		case "enumerate": $result=$users->enumerate(); break; // list with where
		default: throw new Exception("userFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null) 
		throw new Exception($users->errormsg);
	if ($result==="")
		echo json_encode(array('success'=>true,'insert_id'=>$users->conn->insert_id,'affected_rows'=>$users->conn->affected_rows)); 
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>