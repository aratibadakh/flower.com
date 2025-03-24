<?php

@include 'config.php';

if(isset($_POST['submit'])){

   $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $name = mysqli_real_escape_string($conn, $filter_name);
   $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $email = mysqli_real_escape_string($conn, $filter_email);
   $filter_pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
   $filter_cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);

   $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die(mysqli_error($conn));

   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'User Already Exists!';
   } else {
      if(strlen($filter_pass) < 6){
         $message[] = 'Password must be at least 6 characters!';
      } elseif ($filter_pass !== $filter_cpass) {
         $message[] = 'Confirm Password does not match!';
      } else {
         $hashed_password = password_hash($filter_pass, PASSWORD_BCRYPT);
         $query = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')");
         
         if($query){
            header('Location: login.php');
            exit();
         } else {
            $message[] = 'Registration Failed. Try Again!';
         }
      }
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .error-message {
         color: red;
         font-size: 14px;
         margin-top: 5px;
         display: none;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.htmlspecialchars($msg).'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">
   <form action="" method="post" onsubmit="return validateForm()">
      <h3>Register Now</h3>
      <input type="text" name="name" class="box" placeholder="Enter Your Username" required>
      <input type="email" name="email" class="box" placeholder="Enter Your Email" required>

      <input type="password" name="pass" class="box" id="password" placeholder="Enter Your Password" required onkeyup="checkPassword()">
      <span id="password-error" class="error-message">Password must be at least 6 characters</span>

      <input type="password" name="cpass" class="box" id="cpassword" placeholder="Confirm Your Password" required onkeyup="checkConfirmPassword()">
      <span id="confirm-password-error" class="error-message">Passwords do not match</span>

      <input type="submit" class="btn" name="submit" value="Register Now">
      <p>Already have an account? <a href="login.php">Login Now</a></p>
   </form>
</section>

<script>
   function checkPassword() {
      let password = document.getElementById('password').value;
      let error = document.getElementById('password-error');

      if (password.length < 6) {
         error.style.display = "block";
      } else {
         error.style.display = "none";
      }
   }

   function checkConfirmPassword() {
      let password = document.getElementById('password').value;
      let cpassword = document.getElementById('cpassword').value;
      let error = document.getElementById('confirm-password-error');

      if (password !== cpassword) {
         error.style.display = "block";
      } else {
         error.style.display = "none";
      }
   }

   function validateForm() {
      let password = document.getElementById('password').value;
      let cpassword = document.getElementById('cpassword').value;
      if (password.length < 6) {
         alert("Password must be at least 6 characters!");
         return false;
      }
      if (password !== cpassword) {
         alert("Confirm Password does not match!");
         return false;
      }
      return true;
   }
</script>

</body>
</html>