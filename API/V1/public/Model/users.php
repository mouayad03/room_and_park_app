<?php
    // Database conection string
    require "util/database.php";
 
    //get userdata 
    function get_all_users() {
        //connect to database
        global $database;

        //query funciton to get name from database users
        $result = $database->query("SELECT name FROM users;");

        /**
         * if result is false error
         * else if not ture error
         * else okey
         */
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

    function change_player_data($data, $id) {
        global $database;

        $result = $database->query("UPDATE users SET player_data = '$data' WHERE users.id = $id;");

        if (!$result) {
            error_function(500, "Error");
        }
    }

    //get user mail by id
    function get_user_email($id) {
        global $database;

        $result = $database->query("SELECT email FROM users WHERE id = '$id';");

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

        $result = $result->fetch_assoc();

	    return $result;
    }

    //get userdata by mail
    function get_user_by_mail($mail) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE email = '$mail';");

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
    
    //get user type be id
    function get_user_type($id) {
        global $database;
    
        $result = $database->query("SELECT type FROM users WHERE id = '$id';");
    
        if ($result == false) {
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return $user['type'];
            } else {
                error_function(404, "not Found");
            }
        } else {
            error_function(404, "not Found");
        }
    }
      
    //get user data by name
    function get_user_by_username($name) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE name = '$name';");

        if ($result == false) {
            error_function(500, "Error");
		} 
        else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} 
		} 
        else {
            error_function(404, "not Found");
        }
    }

    //get userdata by id
    function get_user_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE id = '$id';");

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

        $result = $result->fetch_assoc();

	    echo json_decode($result);
    }

    //get user name and type by id
    function get_user_id($id) {
        global $database;

        $result = $database->query("SELECT name, type FROM users WHERE id = '$id';");

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

        $result = $result->fetch_assoc();

	    echo json_decode($result);
    }

    function get_skill_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM skills WHERE id = '$id';");

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

    //creating new user
    function create_user($name, $email, $password, $type, $add_date) {
        global $database;

        /**
         * Query function to check if there has a user with this name
         */
        $existing_place = $database->query("SELECT * FROM `users` WHERE `name` = '$name'")->fetch_assoc();
        if ($existing_place) {
            // handle error
            error_function(400, "A place with the name '$name' already exists.");
            return false;
        }

        /**
         * query function to create new user
         */
        $result = $database->query("INSERT INTO `users` (`name`,`email`, `password_hash`, `type`, `add_date`) VALUES ('$name', '$email', '$password', '$type', '$add_date');");

        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }

    //update user by id
    function update_user($user_id, $name, $email, $password, $type, $add_date) {
		global $database;

        //query function to update userdata
		$result = $database->query("UPDATE `users` SET name = '$name', email = '$email', password_hash = '$password', type = '$type', add_date = '$add_date' WHERE id = '$user_id';");

		if (!$result) {
			return false;
		}
		
		return true;
	}

    //delete user by username
    function delete_user($name) {
		global $database;
		
        //query function to delete userdata
		$result = $database->query("DELETE FROM `users` WHERE name = '$name';");
        
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
