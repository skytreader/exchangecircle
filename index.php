<?php
	// ALWAYS COMMIT 
	include("constants.php");
	$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

	if(mysqli_connect_errno()){
		// TODO Better error handling.
		echo "Connection failed.";
		exit();
	}
?>
<html>
	<head>
		<title>Exchange Circle</title>
	</head>
	<body>
		<!-- Check if there is an event configured in the database and adapt display accordingly. -->

	</body>
</html>
