<?php
class user_forms {
    public function sign_up_form($ObjGlob) {
        ?>
        <div class="row align-items-md-stretch">
            <div class="col-md-9">
                <div class="h-100 p-5 text-bg-dark rounded-3">
                    <h2>Sign Up</h2>
                    <?php
                    print $ObjGlob->getMsg('msg');
                    $err = $ObjGlob->getMsg('errors');
                    ?>
                    <form action="<?php print basename($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Fullname:</label>
                            <input type="text" name="fullname" class="form-control form-control-lg" maxlength="50" id="fullname" placeholder="Enter your name" <?php print (isset($_SESSION["fullname"])) ? 'value="'.$_SESSION["fullname"].'"'  : ''; unset($_SESSION["fullname"]); ?> >
                            <?php print (isset($err['nameLetters_err'])) ? "<span class='invalid'>" . $err['nameLetters_err'] . "</span>" : '' ; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email_address" class="form-label">Email Address:</label>
                            <input type="email" name="email_address" class="form-control form-control-lg" maxlength="50" id="email_address" placeholder="Enter your email address" <?php print (isset($_SESSION["email_address"])) ? 'value="'.$_SESSION["email_address"].'"'  : ''; unset($_SESSION["email_address"]); ?> >
                            <?php print (isset($err['email_format_err'])) ? "<span class='invalid'>" . $err['email_format_err'] . "</span>" : '' ; ?>
                            <?php print (isset($err['mailExists_err'])) ? "<span class='invalid'>" . $err['mailExists_err'] . "</span>" : '' ; ?>
                            <?php print (isset($err['mailDomain_err'])) ? "<span class='invalid'>" . $err['mailDomain_err'] . "</span>" : '' ; ?>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control form-control-lg" maxlength="50" id="username" placeholder="Enter your username" <?php print (isset($_SESSION["username"])) ? 'value="'.$_SESSION["username"].'"'  : ''; unset($_SESSION["username"]); ?> >
                            <?php print (isset($err['usernameExists_err'])) ? "<span class='invalid'>" . $err['usernameExists_err'] . "</span>" : '' ; ?>
                            <?php print (isset($err['usernameLetters_err'])) ? "<span class='invalid'>" . $err['usernameLetters_err'] . "</span>" : '' ; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control form-control-lg" maxlength="50" id="password" placeholder="Enter your password">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender:</label>
                            <select name="genderId" class="form-control form-control-lg" id="gender">
                                <option value="0">Select Gender</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role:</label>
                            <select name="roleId" class="form-control form-control-lg" id="role">
                                <option value="0">Select Role</option>
                                <option value="1">User</option>
                                <option value="2">Admin</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <button type="submit" name="signup" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}

// Processing the form submission
if (isset($_POST['signup'])) {
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $email_address = isset($_POST['email_address']) ? $_POST['email_address'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $genderId = isset($_POST['genderId']) ? $_POST['genderId'] : 0; // Default to 0 if not set
    $roleId = isset($_POST['roleId']) ? $_POST['roleId'] : 0; // Default to 0 if not set

    // Check if the password field is not empty
    if (!empty($password)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create an array with the data to update
        $data = [
            'fullname' => $fullname,
            'email' => $email_address,
            'username' => $username,
            'password' => $hashed_password, // Use the hashed password
            'genderId' => $genderId, // Include genderId
            'roleId' => $roleId // Include roleId
        ];

        // Define the WHERE condition to target the right user (replace $user_id with the correct user ID variable)
        $where = ['userId' => $user_id]; // Ensure $user_id is defined in your context

        // Initialize the database connection
        $db = new dbConnection('MySQLi', 'localhost', 3306, 'root', '', 'bootstrap');

        // Call the update method
        if ($db->update('users', $data, $where)) {
            echo "Password and user data updated successfully!";
        } else {
            echo "Error updating user data: " . $db->connection->error; // Debugging error output
        }
    } else {
        echo "Password field is empty.";
    }
}
?>
