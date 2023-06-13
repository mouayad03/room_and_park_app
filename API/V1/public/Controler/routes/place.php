<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

// Define a route to get information about a specific place
$app->get("/Place/{place_name}", function (Request $request, Response $response, $args) {

	// Call a function to validate the user's token before proceeding
	validate_token(); // Unauthorized people will get rejected

	// Get the name of the place from the URL parameters
	$place_name = $args["place_name"];

	// Call a function to retrieve information about the place from a database
	$place = get_room($place_name);

	// If the place was found, return its information as JSON
	if ($place) {
		echo json_encode($place);
	}
	// If there was an error retrieving the place from the database, return a server error
	else if (is_int($place)) {
		error($place, 500);
	}
	// If the place wasn't found, return a 404 error
	else {
		error("The ID "  . $place_name . " was not found.", 404);
	}

	// Return the response object to the client
	return $response;
});


// Define a route to get information about all places
$app->get("/Places", function (Request $request, Response $response, $args) {

	// Call a function to validate the user's token before proceeding
	validate_token(); // Unauthorized people will get rejected

	// Call a function to retrieve information about all places from a database
	$places = get_all_places();

	// If places were found, return their information as JSON
	if ($places) {
		echo json_encode($places);
	}
	// If there was an error retrieving the places from the database, return a server error
	else if (is_string($places)) {
		error($places, 500);
	}
	// If there are no places in the database, return a 400 error
	else {
		error_function(400, "There is no place");
	}

	// Return the response object to the client
	return $response;
});



// This is a POST endpoint that creates a new place.
$app->post("/Place", function (Request $request, Response $response, $args) {

    // Validate that the user has the correct permissions.
    $id = user_validation("A");

    // Validate that the user has a valid token.
    validate_token();

    // Validate that the provided string is safe to use.
    validate_string($_string);

    // Retrieve the request body and decode it from JSON to an array.
    $request_body_string = file_get_contents("php://input");
    $request_data = json_decode($request_body_string, true);

    // Get the values of the required fields from the request body.
    $position = trim($request_data["position"]);
    $name = trim($request_data["name"]);
    $type = trim($request_data["type"]);

    // Check that the position field is not empty and is within the allowed character limit.
    if (empty($position)) {
        error_function(400, "The (position) field must not be empty.");
    } elseif (strlen($position) > 2048) {
        error_function(400, "The (position) field must be less than 2048 characters.");
    }

    // Check that the name field is not empty and is within the allowed character limit.
    if (empty($name)) {
        error_function(400, "The (name) field must not be empty.");
    } elseif (strlen($name) > 255) {
        error_function(400, "The (name) field must be less than 255 characters.");
    }

    // Check that the type field is a single uppercase alphabetic character and is either 'R' or 'P'.
    if (empty($type)) {
        error_function(400, "Please provide the (type) field.");
    } elseif (!ctype_alpha($type)) {
        error_function(400, "The (type) field must contain only alphabetic characters.");
    } elseif (!ctype_upper($type)) {
        error_function(400, "The (type) field must be an uppercase alphabetic character.");
    } elseif ($type !== 'R' && $type !== 'P') {
        error_function(400, "The (type) field must be either 'R' or 'P'.");
    }

    // Create a new place using the provided values.
    if (create_place($position, $name, $type) === true) {
        message_function(200, "The Place was successfully created.");
    } else {
        error_function(500, "An error occurred while saving the place.");
    }

    // Return the response.
    return $response;
});


// This is a DELETE endpoint that deletes a place based on its name.
$app->delete("/Place/{place_name}", function (Request $request, Response $response, $args) {

	// Validate that the user has the correct permissions.
	$id = user_validation("A");
	
	// Validate that the user has a valid token.
    validate_token();
	
	// Validate that the provided string is safe to use.
    validate_string($_string);
	
	// Get the name of the place to delete from the request arguments.
	$place_name = $args["place_name"];
	
	// Attempt to delete the place from the database.
	$result = delete_place($place_name);
	
	// If the deletion was unsuccessful, return a 404 error.
	if (!$result) {
		error_function(404, "No place found for the Name " . $place_name . ".");
	}
	// If the deletion was successful, return a 200 message.
	else {
		message_function(200, "The place was succsessfuly deleted.");
	}
	
	// Return the response.
	return $response;
});

?>
