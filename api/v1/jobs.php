<?php

	// Connect to database
	include("../../dbc.php");
	$db = new dbObj();
	$connection =  $db->getConnstring();

	$request_method = $_SERVER["REQUEST_METHOD"];

	switch($request_method) {
		case 'GET':
			// Get job
			if(!empty($_GET["id"])) {
				$id = intval($_GET["id"]);
				get_jobs($id);
			}
			else {
				get_jobs();
			}
			break;
		default:
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
			break;
	}

	function get_jobs($id = 0) {
		global $connection;
		$query = "SELECT * FROM itjobs";

		if($id != 0) {
			$query .= " WHERE id=".$id." LIMIT 1";
		}

		$response = array();
		$result = mysqli_query($connection, $query);

		while($row = mysqli_fetch_array($result)) {
			$response[] = $row;
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($response);
	}

?>