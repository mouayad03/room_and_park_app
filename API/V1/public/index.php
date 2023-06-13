<?php

    //error handler function
    function customError($errno, $errstr) {
        echo " ";
    }
    
    //set error handler
    set_error_handler("customError");

    // this handel the request and response.
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;

    // This allows to Using Slim and build our application.
    use Slim\Factory\AppFactory;
    use ReallySimpletoken\Token;

    // all the libraries we need.
    require __DIR__ . "/../vendor/autoload.php";
    // self made functions
    require_once "Controler/validation.php";
    require "Model/users.php";
    require "Model/place.php";
    require "Model/events.php";
    require_once "Controler/error-and-info-messages.php";

    // all response data will be in the Json Fromat
    header("Content-Type: application/json");

    $app = AppFactory::create();

    $app->setBasePath("/API/V1");

    /**
     * This will work
     * @param args 
     * @param request_body 
     * @return response 
     */
    $app->post("/Login", function (Request $request, Response $response, $args) {

        // reads the requested JSON body
        $body_content = file_get_contents("php://input");
        $JSON_data = json_decode($body_content, true);

        // if JSON data doesn't have these then there is an error
        if (isset($JSON_data["username"]) && isset($JSON_data["password"])) {
        } else {
            error_function(400, "Empty request");
        }

        // Prepares the data to prevent bad data, SQL injection andCross site scripting
        $name = validate_string($JSON_data["username"]);
        $password = validate_string($JSON_data["password"]);

        if (!$password) {
            error_function(400, "password is invalid, must contain at least 5 characters");
        }
        if (!$name) {
            error_function(400, "username is invalid, must contain at least 5 characters");
        }

        //Password hash
        $password = hash("sha256", $password);

        $user = get_user_by_username($name);

        //if Password or Username are wrong then it should have an error else okey
        if ($user["password_hash"] !==  $password) {
            error_function(404, "Username or Password are not correct");
        }

        if ($user["name"] !==  $name) {
            error_function(404, "Username or Password are not correct");
        }

        $token = create_token($name, $password, $user["id"]);

        setcookie("token", $token, time() + 3600);

        message_function(200, "Successfully logged in");
        
        return $response;
    });

    //valifation for the users
    function user_validation($required_role = null) {
        $current_user_id = validate_token();
        $current_user_role = get_user_type($current_user_id);
        if ($required_role !== null && $current_user_role !== $required_role) {
            error_function(403, "Access Denied");
        }
        return $current_user_id;
    }
    
    //What can I do and who am i 
    $app->get("/WhoAmI", function (Request $request, Response $response, $args) {
        // unotherized pepole will get rejected
        $id = user_validation();
		$user = get_user_id($id);

		if ($user) {
	        echo json_encode($user);
		}
		else if (is_string($user)) {
			error($user, 500);
		}
		else {
			error("The ID "  . $id . " was not found.", 404);
		}

        return $response;
    });

    //old appointment should be deleted
    $app->get('/Time', function (Request $request, Response $response, $args) {
        // Connect to the database
        global $database;
    
        // Query the database for expired reservations
        $result = $database->query("SELECT id FROM `events` WHERE to_date < NOW();");
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
    
            // Delete the expired reservation
            $delete_result = $database->query("DELETE FROM `events` WHERE id = '$id';");
    
            if ($delete_result) {
                echo "Expired reservation with ID $id has been deleted. \n";
            } else {
                echo "Error deleting reservation. \n";
            }
        } else {
            echo "No expired reservations found. \n";
        }
    });
   
    //require all restAPI
    require "Controler/routes/users.php";
    require "Controler/routes/events.php";
    require "Controler/routes/place.php";

    $app->run();
?>
