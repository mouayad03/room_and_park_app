<?php
    // Database conection string
    require "util/database.php";

// This function takes a place name as input and returns an array of information about the place from the database.
function get_room($place_name) {
    global $database; // Access the global database object.

    // Query the database for the place with the given name.
    $result = $database->query("SELECT * FROM places where name = '$place_name';");

    // If there was an error with the query, return a 500 error message.
    if ($result == false) {
        error_function(500, "Error");
	} 
    // If there were no errors with the query, process the results.
    else if ($result !== true) {
        // If there was at least one row of results, construct an array of all the results.
		if ($result->num_rows > 0) {
            $result_array = array();
			while ($user = $result->fetch_assoc()) {
                $result_array[] = $user;
            }
            return $result_array;
		} 
        // If there were no rows of results, return a 404 error message.
        else {
            error_function(404, "not Found");
        }
	} 
    // If the query was successful but returned no results, return a 404 error message.
    else {
        error_function(404, "not Found");
    }
}


/**
 * Creates a new place in the database.
 *
 * @param string $position The position of the place.
 * @param string $name The name of the place.
 * @param string $type The type of the place.
 *
 * @return bool Returns true if the place was successfully created, or false otherwise.
 */
function create_place($position, $name, $type) {
    global $database;

    // check if a place with the same name already exists
    $existing_place = $database->query("SELECT * FROM `places` WHERE `name` = '$name'")->fetch_assoc();
    if ($existing_place) {
        // handle error
        error_function(400, "A place with the name '$name' already exists.");
        return false;
    }

    // check if a place with the same position already exists
    $existing_place = $database->query("SELECT * FROM `places` WHERE `position` = '$position'")->fetch_assoc();
    if ($existing_place) {
        // handle error
        error_function(400, "A place with the position '$position' already exists.");
        return false;
    }

    // insert new place into the database
    $result = $database->query("INSERT INTO `places` (`position`,`name`, `type`) VALUES ('$position', '$name', '$type');");

    if (!$result) {
        // handle error
        error_function(400, "An error occurred while creating the place.");
        return false;
    }

    // return true if the place was successfully created
    return true;
}
    

/**
 * Retrieves all places from the database.
 *
 * @return array|bool Returns an array of all places on success, or false on failure.
 */
function get_all_places() {
    global $database;

    // Query the database for all places
    $result = $database->query("SELECT * FROM places;");

    if ($result == false) {
        // If the query failed, return an error message
        error_function(500, "Error");
    } else if ($result !== true) {
        if ($result->num_rows > 0) {
            // If there are places in the result set, create an array and return it
            $result_array = array();
            while ($places = $result->fetch_assoc()) {
                $result_array[] = $places;
            }
            return $result_array;
        } else {
            // If no places were found, return an error message
            error_function(404, "not Found");
        }
    } else {
        // If the result is not a valid mysqli_result object, return an error message
        error_function(404, "not Found");
    }
}


/**
 * Deletes a place with the specified name from the database.
 *
 * @param string $place_name The name of the place to be deleted.
 * @return bool|null Returns true if the place was successfully deleted, null if no rows were affected, and false if an error occurred.
 */
function delete_place($place_name) {
    global $database;

    // Delete the place with the specified name from the database
    $result = $database->query("DELETE FROM `places` WHERE name = '$place_name';");

    if (!$result) {
        // An error occurred
        return false;
    }
    else if ($database->affected_rows == 0) {
        // No rows were affected
        return null;
    }
    else {
        // The place was successfully deleted
        return true;
    }
}

?>
