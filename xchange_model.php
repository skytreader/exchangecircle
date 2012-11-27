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
Adds a participant with the given name to the database.

Returns true if participant was successfuly added, false otherwise.

TODO Handle 1/(26^255) case when an already-inserted confirmation code is generated
again.
*/
function add_participant($connection, $name){
	$confirmation_code = generate_confirmation_code();
	$insert_invited_query = mysqli_real_escape_string("INSERT INTO invited (name, confirmation_code) VALUES ($name, $confirmation_code)");
	return mysqli_query($connection, $insert_invited_query);
}

/**
Called when a guest confirms attendance to the event.

Send an email to the guest with a confirmation code. Only when
the guest confirms through this is he considered "attending".
*/
function rsvp($connection, $invitation_id, $email){
	// TODO implement!
}

/**
Called when a guest confirms via email. Guests should finish confirmation
before the set confirmation deadline (in event_settings table).

Returns the result set for the record if the given confirmation code matches
the invitation id. False, otherwise.

TODO Must send another email to the guest that rsvp is confirmed.
*/
function confirm($connection, $invitation_id, $confirmation_code){
	// Get the confirmation deadline and check server date if it is not yet over.
	$confirmation_date_query = mysqli_real_escape_string("SELECT confirmation_deadline FROM event_settings LIMIT 1;");
	$query_result = mysqli_query($confirmation_date_query);

	// We can safely assume that the invitation id and confirmation code is
	// already in the DB iff it is a valid combination.
	$check_query = mysqli_real_escape_string("SELECT * FROM invited WHERE invitation_id = '$invitation_id' AND confirmation_code = '$confirmation_code' LIMIT 1;");
	return mysqli_query($connection, $check_query);
}

/**

*/

?>
