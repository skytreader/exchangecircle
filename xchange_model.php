<?php

/**
The functions in this file assume that a connection to a database
with the tables in circle.sql on it. 
*/

define(CONFIRMATION_CODE_LENGTH, 255);

function generate_confirmation_code(){
	return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, CONFIRMATION_CODE_LENGTH);
}

/**
Adds a participant with the given name to the database. The participant
is added in two tables: invited and mail_confirmations

Returns true if participant was successfuly added, false otherwise.
*/
function add_participant($name){
	$insert_query = mysqli_real_escape_string("INSERT INTO invited (name) VALUES ($name)");
	return mysqli_query($insert_query);
}

?>
