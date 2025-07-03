<?php
function selectDB($table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "SELECT * FROM `".$table."`";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		while($row = $result->fetch_assoc() ){
			$array[] = $row;
		}
		if ( isset($array) AND is_array($array) ){
			return $array;
		}else{
			return 0;
		}
	}else{
		$error = array("msg"=>"select table error");
		return 0;
	}
}

function selectDBNew($table, $placeHolders, $where, $order){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT * FROM `{$table}`";
    if(!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if(!empty($order)) {
        $sql .= " ORDER BY {$order}";
    }
    if($stmt = $dbconnect->prepare($sql)) {
        $types = str_repeat('s', count($placeHolders));
        $stmt->bind_param($types, ...$placeHolders);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if(isset($array) && is_array($array)) {
            return $array;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function selectDataDB($select, $table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "SELECT {$select} FROM {$table}";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		while($row = $result->fetch_assoc() ){
			$array[] = $row;
		}
		if ( isset($array) AND is_array($array) ){
			return $array;
		}else{
			return 0;
		}
	}else{
		$error = array("msg"=>"select table error");
		return outputError($error);
	}
}

function selectDB2($select, $table, $where){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT {$select} FROM `{$table}`";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if (isset($array) && is_array($array)) {
            return $array;
        } else {
            return 0;
        }
    } else {
        $error = array("msg" => "select table error");
        return $error;
    }
}

function deleteDB($table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "DELETE FROM `".$table."`";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		return 1;
	}else{
		$error = array("msg"=>"delete table error");
		return outputError($error);
	}
}

function insertDB($table, $data){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $keys = array_keys($data);
    $sql = "INSERT INTO `{$table}`(";
    $placeholders = "";
    foreach ($keys as $key) {
        $sql .= "`{$key}`,";
        $placeholders .= "?,";
    }
    $sql = rtrim($sql, ",");
    $placeholders = rtrim($placeholders, ",");
    $sql .= ") VALUES ({$placeholders})";
    $stmt = $dbconnect->prepare($sql);
    $types = str_repeat('s', count($data));
    $stmt->bind_param($types, ...array_values($data));
    if($stmt->execute()){
        return 1;
    }else{
        return 0;
    }
}

function updateDB($table, $data, $where) {
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $keys = array_keys($data);
    $sql = "UPDATE `" . $table . "` SET ";
    $params = "";
    for ($i = 0; $i < sizeof($data); $i++) {
        $sql .= "`" . $keys[$i] . "` = ?";
        if (isset($keys[$i + 1])) {
            $sql .= ", ";
        }
        $params .= "s";
    }
    $sql .= " WHERE " . $where;
    $stmt = $dbconnect->prepare($sql); 
    $values = array_values($data);
    $stmt->bind_param($params, ...$values);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}
?>