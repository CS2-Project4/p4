<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: index.php");
  exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "សូមបញ្ចូលឈ្មោះអ្នកប្រើប្រាស់។";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "សូមបញ្ចូលពាក្យសម្ងាត់របស់អ្នក។";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "ពាក្យសម្ងាត់ដែលអ្នកបានបញ្ចូលមិនត្រឹមត្រូវ។";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "រកមិនឃើញគណនីជាមួយឈ្មោះអ្នកប្រើប្រាស់នេះទេ។";
                }
            } else{
                echo "អូ៎! មានអ្វីមួយខុសប្រក្រតី។ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>

<div class="wrapper fadeInDown">
  <div id="formContent">
    <!-- Tabs Titles -->

    <!-- Icon -->
    <div class="fadeIn first">

      <h1>ចូលប្រើកម្មវិធី</h1>
    </div>

    <!-- Login Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <input type="text" id="username" class="fadeIn second" name="username" value="<?php echo $username; ?>" placeholder="ឈ្មោះ">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <input type="password" id="password" class="fadeIn third" name="password" value="<?php echo $username; ?>" placeholder="ពាក្យសំងាត់">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="wrap">
            <button class="button">ចូលប្រើ</button>
        </div>
    </form>

    <!-- Remind Passowrd -->
    <div id="formFooter">
        <p>អ្នកមិនទាន់មានគណនីមែនទេ? <a class="underlineHover" href="register.php">ចុះឈ្មោះឥឡូវនេះ</a></p>
    </div>
    

  </div>
</div>
</body>
</html>
