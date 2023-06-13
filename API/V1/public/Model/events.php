<?php
    // Database conection string
    require "util/database.php";

    //get reservationdata by name
    function get_reservation_by_name($place_name) {
        //connect to databse
        global $database;

        //query function to list all data from database by placename
        $result = $database->query("SELECT * FROM events WHERE place_name = '$place_name';");

        /**
         * if result is false error
         * else if not ture error
         * else okey
         */
        if ($result == false) {
            error_function(500, "Error");
		} else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} else {
                error_function(404, "not Found");
            }
		} else {
            error_function(404, "not Found");
        }
    }

    //get reservariondata by id
    function get_reservation_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM events WHERE id = '$id';");

        if ($result == false) {
            error_function(500, "Error");
		} else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} else {
                error_function(404, "not Found");
            }
		} else {
            error_function(404, "not Found");
        }
    }

    //get reservations
    function get_all_reservations() {
        global $database;

        $result = $database->query("SELECT * FROM events;");

        if ($result == false) {
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                $result_array = array();
                while ($user = $result->fetch_assoc()) {
                    $result_array[] = $user;
                }
                return $result_array;
            } else {
                error_function(404, "not Found");
            }
        } else {
            error_function(404, "not Found");
        }
    }

    //create new reservation
    function create_reservation($from_date, $to_date, $place_name, $host, $description, $email) {
        global $database;
        
        // Check if place_name already exists
        $check_result = $database->prepare("SELECT COUNT(*) FROM `events` WHERE `place_name` = ? AND `to_date` > ?");
        $check_result->bind_param("ss", $place_name, $from_date);
        $check_result->execute();
        $check_result = $check_result->get_result()->fetch_row()[0];
        if ($check_result > 0) {
            // place_name already exists, return false
            error_function(400, "It's look like someone booked ( " . $place_name . " ) before you.");       
        };
        
        // Insert new reservation
        $result = $database->prepare("INSERT INTO `events` (`from_date`, `to_date`, `place_name`, `host`, `description`) VALUES (?, ?, ?, ?, ?)");
        $result->bind_param("sssss", $from_date, $to_date, $place_name, $host, $description);
        $result = $result->execute();
        
        if (!$result) {
            // handle error
            return false;
        };
                
        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

        // Generate unique ID for the event
        $uid = uniqid();

        // Generate .ics file contents
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . $uid . "
DTSTAMP:" . $from_date_utc . "
DTSTART:" . $from_date_utc . "
DTEND:" . $to_date_utc . "
SUMMARY:" . $place_name . "
END:VEVENT
END:VCALENDAR";

        // Generate reservation details for email body
        $reservation_details = "Reservation Details:\r\n\r\n";
        $reservation_details .= "Place Name: " . $place_name . "\r\n\n";
        $reservation_details .= "From Date: " . $from_date . "\r\n\n";
        $reservation_details .= "To Date: " . $to_date . "\r\n\n";

        // Generate email body with reservation details and .ics file attachment
        $boundary = md5(time());
        $headers = "From: morhaf.mouayad@gmail.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=" . $boundary . "\r\n\r\n";
        $body = "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $reservation_details . "\r\n\r\n";
        $body .= "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/calendar; charset=utf-8; method=REQUEST; name=reservation.ics\r\n";
        $body .= "Content-Disposition: attachment; filename=reservation.ics\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($ical)) . "\r\n\r\n";
        $body .= "--" . $boundary . "--";

        // Send email with reservation details and .ics file attachment
        $to = $email;
        $subject = "Your Reservation";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }

         // Send email with reservation details and .ics file attachment
         $to = "dominic.streit@ict.csbe.ch";
         $subject = "New Reservation";
         if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
             error_function(400, "Email sending failed, but the reservation was successfully created.");
         }

        return true;
    }
    
    //update reservation
    function update_reservation($id, $from_date, $to_date, $place_name, $host, $description, $email) {
        global $database;

        $result = $database->query("UPDATE `events` SET from_date = '$from_date', to_date = '$to_date', place_name = '$place_name', host = '$host', description = '$description' WHERE id = '$id';");

        if (!$result) {
            return false;
        }

        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

        // Generate unique ID for the event
        $uid = uniqid();

        // Generate .ics file contents
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . $uid . "
DTSTAMP:" . $from_date_utc . "
DTSTART:" . $from_date_utc . "
DTEND:" . $to_date_utc . "
SUMMARY:" . $place_name . "
END:VEVENT
END:VCALENDAR";

        // Generate reservation details for email body
        $reservation_details = "Reservation Details:\r\n\r\n";
        $reservation_details .= "Place Name: " . $place_name . "\r\n\n";
        $reservation_details .= "From Date: " . $from_date . "\r\n\n";
        $reservation_details .= "To Date: " . $to_date . "\r\n\n";

        // Generate email body with reservation details and .ics file attachment
        $boundary = md5(time());
        $headers = "From: morhaf.mouayad@gmail.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=" . $boundary . "\r\n\r\n";
        $body = "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $reservation_details . "\r\n\r\n";
        $body .= "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/calendar; charset=utf-8; method=REQUEST; name=reservation.ics\r\n";
        $body .= "Content-Disposition: attachment; filename=reservation.ics\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($ical)) . "\r\n\r\n";
        $body .= "--" . $boundary . "--";

        // Send email with reservation details and .ics file attachment
        $to = $email;
        $subject = "Reservation Update";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully updated.");
        }

        // Send email with reservation details and .ics file attachment
        $to = "dominic.streit@ict.csbe.ch";
        $subject =  "Reservation update";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }
        
        return true;
    }
    
    //deleting reservation by id
    function delete_reservation($id) {
        global $database;
    
        //query function to delete reservation by id
        $result = $database->query("DELETE FROM `events` WHERE id = '$id';");
            
        if (!$result) {
            return false;
        }
        else if ($database->affected_rows == 0) {
            return null;
        }
        else {
            return true;
        }
    }
    


?>