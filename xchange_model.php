<?php

include("constants.php");

/**
The functions in this file assume that a connection to a database
with the tables in circle.sql on it. 
*/

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

Returns the return of the mail function.
*/
function rsvp($connection, $invitation_id, $email){
    // Get the confirmation code for the invitation id
    $confcode_query = mysqli_real_escape_string("SELECT confirmation_code, name FROM invited WHERE invitation_id = $invitation_id LIMIT 1;");
    $confcode_query_result = mysqli_query($connection, $confcode_query);
    $confcode_result = mysqli_fetch_assoc($confcode_query_result);
    $confcode = $confcode_result["confirmation_code"];
    $name = $confcode_result["name"];
    
    // FIXME UX Bug. Isn't it weird to get a message saying "Hi Chad Estioco!"?
    $mail_message = "Hi $name! You still need to confirm your RSVP. Please visit " . APP_URL . "/confirm.php?cc=$confcode . If you don't confirm, you will not be considered for the exchange gift. Kawawa ka naman.";
    // TODO Add headers for fun and fancy stuff!
    mail($email, "Your RSVP needs confirmation", wordwrap($mail_message, 70));
}

/**
Call after confirm().
*/
function mail_confirmation_ack($connection, $invitation_id){
    $email_query = mysqli_real_escape_string("SELECT email, name FROM invited WHERE invitation_id = $invitation_id LIMIT 1;");
    $email_query_result = mysqli_query($connection, $email_query);
    $email_result = mysqli_fetch_array($email_query_result);
    $email = $email_resut["email"];
    $name = $email_result["name"];

    $mail_message = "Hi $name! Thanks for confirming. You will now be part of the exchange gift. Hindi ka na kawawa.";
    mail($email, "You have confirmed your RSVP", wordwrap($mail_message, 70));
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
    $query_result = mysqli_query($connection, $confirmation_date_query);
    $result_details = mysqli_fetch_assoc($query_result);
    $date_str = $result_details["confirmation_deadline"];
    $actual_date = strtotime($date_str);

    if($actual_date < time()){
        // We can safely assume that the invitation id and confirmation code is
        // already in the DB iff it is a valid combination.
        $check_query = mysqli_real_escape_string("SELECT * FROM invited WHERE invitation_id = '$invitation_id' AND confirmation_code = '$confirmation_code' LIMIT 1;");
        return mysqli_query($connection, $check_query);
    } else{
        return false;
    }
}

/**
Assigns exchange-gift pairings using all guests who confirmed.

Returns an associative array of the following format:
  (1) The key is also an associative array with two keys:
      "invitation_id" and "email".
  (2) The value is an integer, containing an invitation_id.

The person corresponding to the invitation_id in the key WILL GIVE
to the person corresponding to the invitation_id in the value.
*/
function assign_pairings($connection){
    // A guest has confirmed iff the date_confirmed field is not null
    $confirmed_guests_query = mysqli_real_escape_string("SELECT invitation_id, email FROM invited WHERE date_confirmed IS NOT NULL;");
    $confirmed_guests_result = mysqli_query($connection, $confirmed_guests_query);
    $confirmed_guests = array();
    $gift_receivers = array();
    
    // Arrayify!
    while($guest = mysqli_fetch_assoc($confirmed_guests_result)){
        array_push($confirmed_guests, $guest);
        array_push($gift_receivers, $guest["invitation_id"]);
    }

    // Now shift the $gift_receivers array so that the first confirmed
    // guest gives to the second confirmed guest, etc., until the last
    // confirmed guest gives to the first confirmed guest.
    $first_guest = array_shift($gift_receivers);
    array_push($gift_receivers, $first_guest);

    // Now assign them to each other
    $giving_assignments = array();
    $limit = count($confirmed_guests);

    for($i = 0; $i < $limit; $i++){
        $giving_assignments[$confirmed_guests[$i]] = $gift_receivers[$i];
    }

    return $giving_assignments;
}

/**
Checks whether there is an event in the database.

(Note that we assume that the event_settings table assumes that there is
one and only one record in it---the current event. This function puts
priority to whatever record is fetched first.)

Returns an associative array containing the first row fetched from the
event_settings table.
*/
function check_set_event($connection){
    $event_check_query = mysqli_real_escape_string("SELECT * FROM event_settings LIMIT 1;");
    $event_check_result = mysqli_query($connection, $event_check_query);
    return mysqli_fetch_assoc($event_check_result);
}
?>
