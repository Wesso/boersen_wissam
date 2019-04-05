<?php

Class serverObj {
	
	var $servername = "localhost";
	var $username = "root";
	var $password = "";
	var $connection;
	function getServerConnstring() {
		$con = mysqli_connect($this->servername, $this->username, $this->password) or die("Kan ikke forbindes til serveren: " . mysqli_connect_error());

		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Kan ikke forbindes til serveren: %s\n", mysqli_connect_error());
			exit();
		} else {
			$this->connection = $con;
		}
		return $this->connection;
	}
}

$serv = new serverObj();
$conn =  $serv->getServerConnstring();

// Create a database
$sql = "CREATE DATABASE IF NOT EXISTS `wissam` DEFAULT CHARACTER SET utf8 COLLATE utf8_danish_ci";
if (!mysqli_query($conn, $sql)) {
    echo "Kan ikke oprette database: " . mysqli_error($conn);
}

mysqli_close($conn);
?>