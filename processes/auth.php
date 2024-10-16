<?php
class auth {
    public function signup($conn, $ObjGlob) {
        if (isset($_POST["signup"])) {
            $errors = array();

            // Capture input values
            $fullname = $_SESSION["fullname"] = $conn->escape_values(ucwords(strtolower($_POST["fullname"])));
            $email_address = $_SESSION["email_address"] = $conn->escape_values(strtolower($_POST["email_address"]));
            $username = $_SESSION["username"] = $conn->escape_values(strtolower($_POST["username"]));
            $password = $conn->escape_values($_POST["password"]); // Capture password
            $genderId = $conn->escape_values($_POST["genderId"]); // Capture genderId
            $roleId = $conn->escape_values($_POST["roleId"]); // Capture roleId

            // Validation logic
            if (ctype_alpha(str_replace(" ", "", $fullname)) === FALSE) {
                $errors['nameLetters_err'] = "Invalid name format: Full name must contain letters and spaces only.";
            }
            if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                $errors['email_format_err'] = 'Wrong email format.';
            }

            // Verify email domain
            $conf['valid_domains'] = ["strathmore.edu", "gmail.com", "yahoo.com", "mada.co.ke", "outlook.com"];
            $arr_email_address = explode("@", $email_address);
            $spot_dom = end($arr_email_address);
            if (!in_array($spot_dom, $conf['valid_domains'])) {
                $errors['mailDomain_err'] = "Invalid email address domain. Use only: " . implode(", ", $conf['valid_domains']);
            }

            // Verify if email or username already exists
            if ($conn->count_results(sprintf("SELECT email FROM users WHERE email = '%s' LIMIT 1", $email_address)) > 0) {
                $errors['mailExists_err'] = "Email Already Exists.";
            }
            if ($conn->count_results(sprintf("SELECT username FROM users WHERE username = '%s' LIMIT 1", $username)) > 0) {
                $errors['usernameExists_err'] = "Username Already Exists.";
            }

            // Verify username contains letters only
            if (!ctype_alnum($username)) {
                $errors['usernameLetters_err'] = "Invalid username format. Username must contain letters and numbers only.";
            }

            if (!count($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

                // Prepare data for insertion
                $cols = ['fullname', 'email', 'username', 'password', 'genderId', 'roleId'];
                $vals = [$fullname, $email_address, $username, $hashed_password, $genderId, $roleId];
                $data = array_combine($cols, $vals);
                $insert = $conn->insert('users', $data);

                if ($insert === TRUE) {
                    header('Location: signup.php');
                    unset($_SESSION["fullname"], $_SESSION["email_address"], $_SESSION["username"]);
                    exit();
                } else {
                    die($insert); // Handle insertion error
                }
            } else {
                $ObjGlob->setMsg('msg', 'Error(s)', 'invalid');
                $ObjGlob->setMsg('errors', $errors, 'invalid');
            }
        }
    }
}
?>
