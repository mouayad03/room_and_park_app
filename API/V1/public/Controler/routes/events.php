<?php
    // this handel the request and response.
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Reservations", function (Request $request, Response $response, $args) {
        // Only authorized users can access this endpoint
        validate_token();

        // Retrieve all reservations
        $reservations = get_all_reservations();

        // If reservations are found, return them as JSON
        if ($reservations) {
            echo json_encode($reservations);
        }
        // If there was an error retrieving the reservations, return a 500 error with the error message
        else if (is_string($reservations)) {
            error_function(500, $reservations);
        }
        // If there was an error with the request, return a 400 error
        else {
            error(400, "Error");
        }

        return $response;
    });
    
    $app->get("/Reservation/{id}", function (Request $request, Response $response, $args) {
        // This route is accessible by everyone
        validate_token(); // Check if the request has a valid token, unauthorized people will get rejected
        
        $id = intval($args["id"]); // Get the reservation ID from the URL parameter and convert it to an integer
    
        $reservation = get_reservation_by_id($id); // Retrieve the reservation with the specified ID from the database
    
        if ($reservation) { // If the reservation exists, encode it to JSON format and send it in the response body
            echo json_encode($reservation);
        }
        else if (is_string($reservation)) { // If there was an error while retrieving the reservation, send an error message with a 500 status code
            error($reservation, 500);
        }
        else { // If the reservation doesn't exist, send an error message with a 404 status code
            error("The reservation with ID "  . $id . " was not found.", 404);
        }
    
        return $response; // Return the response object
    });
    
    $app->post("/Reservation", function (Request $request, Response $response, $args) {
        //everyone
        validate_token(); //unauthorized people will get rejected
        validate_string($_string); //validate string function call
    
        $id = user_validation(); //user_validation function call to get user ID
    
        $email = get_user_email($id); //get email address of the user with given ID
        $email = implode(':', $email); //implode email address array into string with ":" separator
    
        //get request data from input stream
        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);
    
        //sanitize input fields
        $from_date = trim($request_data["from_date"]);
        $to_date = trim($request_data["to_date"]);
        $place_name = trim($request_data["place_name"]);
        $host = trim($request_data["host"]);
        $description = trim($request_data["description"]);
    
        //The fields cannot be empty and must not exceed 2048 characters
        if (empty($to_date)) {
            error_function(400, "The (to date) field must not be empty.");
        } elseif (strlen($to_date) > 2048) {
            error_function(400, "The (date_time) field must be less than 2048 characters.");
        }
    
        //set default values for optional fields if they are not provided
        $place_name = "NULL";
        if (isset($request_data["place_name"])) {
            $place_name = $request_data["place_name"];
        }
    
        $host = "NULL";
        if (isset($request_data["host"])) {
            $host = $request_data["host"];
        }
    
        $description = "NULL";
        if (isset($request_data["description"])) {
            $description = "'" . $request_data["description"] . "'";
        }
    
        if (strlen($description) > 2048) {
            error_function(400, "The (host) field must be less than 255 characters.");
        }
    
        //create reservation using the input data and user email
        if (create_reservation($from_date, $to_date, $place_name, $host, $description, $email) === true) {
            message_function(200, "The reservation was successfully created.");
        } else {
            error_function(500, "An error occurred while saving the reservation.");
        }
        return $response;
    });    

    // This endpoint updates an existing reservation by its id using PUT request method
    $app->put("/Reservation/{id}", function (Request $request, Response $response, $args) {

		// Validate user authentication level 
        $id = user_validation("A");

        // Validate user token and string input
        validate_token();
        validate_string($_string);

        // Get user email by id
        $email = get_user_email($id);

        // Concatenate emails if there are multiple emails
        $email = implode(':', $email);

        // Get reservation id from URL parameters
        $id = $args["id"];

        // Get reservation by id
        $reservation = get_reservation_by_id($id);

        // If reservation doesn't exist, return 404 error response
        if (!$reservation) {
            error_function(404, "No reservation found for the id ( " . $id . " ).");
        }

        // Get request body as string
        $request_body_string = file_get_contents("php://input");

        // Decode request body JSON into an associative array
        $request_data = json_decode($request_body_string, true);

        // Update reservation's from_date if it exists in request body
        if (isset($request_data["from_date"])) {
            $from_date = strip_tags(addslashes($request_data["from_date"]));

            // If from_date length is greater than 255 characters, return 400 error response
            if (strlen($from_date) > 255) {
                error_function(400, "The from_date is too long. Please enter less than 255 letters.");
            }

            $reservation["from_date"] = $from_date;
        }

        // Update reservation's to_date if it exists in request body
        if (isset($request_data["to_date"])) {
            $to_date = strip_tags(addslashes($request_data["to_date"]));

            // If to_date length is greater than 500 characters, return 400 error response
            if (strlen($to_date) > 500) {
                error_function(400, "The to_date is too long. Please enter less than 500 letters.");
            }

            $reservation["to_date"] = $to_date;
        }

        // Update reservation's place_name if it exists in request body
        if (isset($request_data["place_name"])) {
            $place_name = strip_tags(addslashes($request_data["place_name"]));

            // If place_name length is greater than 1000 characters, return 400 error response
            if (strlen($place_name) > 1000) {
                error_funciton(400, "The place_name is too long. Please enter less than 1000 letters.");
            }

            $reservation["place_name"] = $place_name;
        }

        // Update reservation's host if it exists in request body
        if (isset($request_data["host"])) {
            $host = strip_tags(addslashes($request_data["host"]));

            // If host length is greater than 1000 characters, return 400 error response
            if (strlen($host) > 1000) {
                error_funciton(400, "The host is too long. Please enter less than 1000 letters.");
            }

            $reservation["host"] = $host;
        }

        // Update reservation's description if it exists in request body
        if (isset($request_data["description"])) {
            $description = strip_tags(addslashes($request_data["description"]));
		
			if (strlen($description) > 1000) {
				error_function(400, "The description is too long. Please enter less than 1000 letters.");
			}
		
			$reservation["description"] = $description;
		}
		
        //send data
		if (update_reservation($id, $reservation["from_date"], $reservation["to_date"], $reservation["place_name"], $reservation["host"], $reservation["description"], $email)) {
			message_function(200, "The reservation data were successfully updated");
		}
		else {
			error_function(500, "An error occurred while saving the reservation data.");
		}
		
		return $response;
	});

    $app->delete("/Reservation/{id}", function (Request $request, Response $response, $args) {
        // Validate token for everyone
        validate_token();
        validate_string($_string);
        
        // Get the ID of the reservation to delete from the URL parameter
        $id = $args["id"];
        
        // Call the delete_reservation function to delete the reservation with the given ID
        $result = delete_reservation($id);
        
        // Check if the reservation was found and deleted
        if (!$result) {
            // If not found, return a 404 error with a message
            error_function(404, "No reservation found for the id " . $id . ".");
        }
        else {
            // If found and deleted, return a 200 success message
            message_function(200, "The reservation was successfully deleted.");
        }
        
        return $response;
    });
?>
