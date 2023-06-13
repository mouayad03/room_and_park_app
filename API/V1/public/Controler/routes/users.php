<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Users", function (Request $request, Response $response, $args) {
        $id = user_validation("A"); // Get user ID from user_validation function with argument "A"
        validate_token(); // Ensure that the user making the request is authorized
    
        $users = get_all_users(); // Retrieve a list of all users from the database
    
        if ($users) { // If the list of users is not empty
            echo json_encode($users); // Return the list of users in JSON format
        }
        else if (is_string($users)) { // If the list of users is empty but the function returned a string error message
            error($users, 500); // Return the error message and a 500 status code
        }
        else { // If the list of users is empty and the function did not return an error message
            error("The ID "  . $id . " was not found.", 404); // Return a "not found" error message and a 404 status code
        }
    
        return $response; // Return the response object
    });    

    $app->post("/User", function (Request $request, Response $response, $args) {
        $id = user_validation("A"); // Get user ID from user_validation function with argument "A"
        validate_token(); // Ensure that the user making the request is authorized
        validate_string($_string); // Validate a string
    
        $request_body_string = file_get_contents("php://input"); // Retrieve the request body
        $request_data = json_decode($request_body_string, true); // Decode the JSON request body into an associative array
        $name = trim($request_data["name"]); // Get the user's name from the request body and trim it
        $email = trim($request_data["email"]); // Get the user's email from the request body and trim it
        $password = trim($request_data["password"]); // Get the user's password from the request body and trim it
        $type = trim($request_data["type"]); // Get the user's type from the request body and trim it
        $add_date = trim($request_data["add_date"]); // Get the user's add_date from the request body and trim it
    
        // The name field cannot be empty and must not exceed 2048 characters
        if (empty($name)) {
            error_function(400, "The (name) field must not be empty.");
        } 
        elseif (strlen($name) > 255) {
            error_function(400, "The (name) field must be less than 2048 characters.");
        }
    
        // The email field cannot be empty and must not exceed 255 characters
        if (empty($email)) {
            error_function(400, "The (email) field must not be empty.");
        } 
        elseif (strlen($email) > 255) {
            error_function(400, "The (email) field must be less than 255 characters.");
        }
    
        // The password field cannot be empty
        if (empty($password)) {
            error_function(400, "Please provide the (password) field.");
        } 
    
        // The type field cannot be empty
        if (empty($type)) {
            error_function(400, "Please provide the (type) field.");
        } 
    
        // The add_date field cannot be empty
        if (empty($add_date)) {
            error_function(400, "Please provide the (add_date) field.");
        } 
    
        $password = hash("sha256", $password); // Hash the user's password using SHA-256
    
        // Check if the user was created successfully
        if (create_user($name, $email, $password, $type, $add_date) === true) {
            message_function(200, "The user was successfully created."); // Return a success message and a 200 status code
        } else {
            error_function(500, "An error occurred while saving the userdata."); // Return an error message and a 500 status code
        }
        return $response; // Return the response object
    });    

    // Handles PUT requests to update a user's data by ID
$app->put("/User/{id}", function (Request $request, Response $response, $args) {

	// Validate user credentials and token
	$id = user_validation("A");
    validate_token();
    validate_string($_string);
	
	// Get user ID from URL parameter
	$user_id = $args["id"];
	
	// Get user data by ID
	$user = get_user_by_id($id);
	
	// If user doesn't exist, return 404 error
	if (!$user) {
		error_function(404, "No user found for the id ( " . $user_id . " ).");
	}
	
	// Get request body data and decode JSON
	$request_body_string = file_get_contents("php://input");
	$request_data = json_decode($request_body_string, true);

	// Update user's name if specified in request data
	if (isset($request_data["name"])) {
		$name = strip_tags(addslashes($request_data["name"]));
	
		// If name is too long, return 400 error
		if (strlen($name) > 255) {
			error_function(400, "The name is too long. Please enter less than 255 letters.");
		}
	
		// Update user's name in data array
		$user["name"] = $name;
	}

    // Update user's email if specified in request data
    if (isset($request_data["email"])) {
		$email = strip_tags(addslashes($request_data["email"]));
	
		// If email is too long, return 400 error
		if (strlen($email) > 500) {
			error_function(400, "The email is too long. Please enter less than 500 letters.");
		}
	
		// Update user's email in data array
		$user["email"] = $email;
	}

    // Update user's password if specified in request data
    if (isset($request_data["password_hash"])) {
		$password = strip_tags(addslashes($request_data["password_hash"]));
	
		// If password is too long, return 400 error
		if (strlen($password) > 1000) {
			error_funciton(400, "The password is too long. Please enter less than 1000 letters.");
		}
	
		// Update user's password hash in data array
        $user["password_hash"] = $password;

        // Hash updated password
        $user["password_hash"] = hash("sha256", $password);
	}

    // Update user's type if specified in request data
    if (isset($request_data["type"])) {
		$type = strip_tags(addslashes($request_data["type"]));
	
		// If type is too long, return 400 error
		if (strlen($type) > 1000) {
			error_funciton(400, "The type is too long. Please enter less than 1000 letters.");
		}
	
		// Update user's type in data array
		$user["type"] = $type;
	}

    // Update user's add date if specified in request data
    if (isset($request_data["add_date"])) {
		$add_date = strip_tags(addslashes($request_data["add_date"]));
	
		// If add date is too long, return 400 error
		if (strlen($add_date) > 1000) {
			error_function(400, "The type is too long. Please enter less than 1000 letters.");
			}
		
			$user["add_date"] = $add_date;
		}
		
        //send all data
		if (update_user($user_id, $user["name"], $user["email"], $user["password_hash"], $user["type"], $user["add_date"])) {
			message_function(200, "The userdata were successfully updated");
		}
		else {
			error_function(500, "An error occurred while saving the user data.");
		}
		
		return $response;
	});
    
   // Endpoint to delete a user by name
    $app->delete("/User/{name}", function (Request $request, Response $response, $args) {
        
        // Validate user access level
        $id = user_validation("A");
        
        // Validate token and input string
        validate_token();
        validate_string($_string);
            
        // Get user name from URL parameter
        $name = $args["name"];
            
        // Delete user from database
        $result = delete_user($name);
            
        // Check if user was found and deleted
        if (!$result) {
            // Return error response if user was not found
            error_function(404, "No user found for the Name ( " . $name . " ).");
        }
        else {
            // Return success response if user was successfully deleted
            message_function(200, "The user was succsessfuly deleted.");
        }
            
        // Return response
        return $response;
    });

?>