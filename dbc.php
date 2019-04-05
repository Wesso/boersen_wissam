<?php

Class dbObj {
	
	var $servername = "localhost";
	var $username = "root";
	var $password = "";
	var $dbname = "wissam";
	var $connection;
	function getConnstring() {
		$con = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname) or die("Kan ikke forbindes til database: " . mysqli_connect_error());

		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Kan ikke forbindes til database: %s\n", mysqli_connect_error());
			exit();
		} else {
			$this->connection = $con;
		}
		return $this->connection;
	}
}

?>