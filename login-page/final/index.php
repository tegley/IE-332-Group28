<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = $_POST['username'];
    $user_password = $_POST['password'];
    require 'check_login.php';

    //1st Case - Username is incorrect
    if ($result_array[1] == "Username is incorrect") {
        echo "<script>";
        echo "alert('Username is invalid');"; 
        echo "</script>";
    }

    //2nd Case - Password is incorrect
    if ($result_array[1]=="Password is incorrect") {
        $user_username_string = (string)$user_username;
        echo "<script>";
        echo "alert(`Hey $user_username_string - your password is incorrect :(`);";
        echo "</script>";
    }

    //3rd Case - Username & Password are correct
    if ($result_array[1]!=="Username is incorrect" || $result_array[1]!=="Password is incorrect") {
        $_SESSION['username'] = $user_username;
        $_SESSION['loggedin'] = true;
        $_SESSION['FullName'] = $result_array[1];

        //If the user is a supply chain manager, redirect them to the SCM home page
        if ($result_array[0]=="SupplyChainManager") {
            echo "<script>";
            echo "window.location.href = 'SCM_home_page.php';";
            echo "</script>";
        }

        //If the user is a senior manager, redirect them to the ERP home page
        if ($result_array[0]=="SeniorManager") {
            echo "<script>";
            echo "alert('Work in progress');";
            echo "</script>";
        }
    }
}
?>

<!DOCTYPE html>
<!-- Tells the browser that this is an HTML5 document -->
<html lang="en">
<head>
  <!-- Sets the character encoding so text displays correctly -->
  <meta charset="UTF-8">

  <!-- Makes the page adjust to phone/tablet screen sizes -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- The text that appears in the browser tab -->
  <title>Global Electronics LLC</title>

  <!-- 
    CSS Framework setup (Bootstrap)
    - General-purpose: Usable for many different aspects of the project
    - "Not classless”: Enables use of classnames
    - Uses CDN: No installation needed, stylesheet works instantly 
  -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet">

  <!-- CSS -->
  <style>
  /* Standardize formatting across website via Universal Project Formatting CSS file */
  @import url("standardized_project_formatting.css");
  /* Container for the login form */
  .login-box {
    margin: 40px auto; /* centers it horizontally using 'auto' */
    width: 280px;
    position: relative;      /* limits how wide it can go */
    }

    /* Styles for each label (Username / Password) */
  .login-box label {
    display: block;    /* forces each label to be on its own line */
    margin-top: 15px;
    font-size: 25px;  /* adds a bit of space above each label */
    }

  .btn-primary {
      position: relative;
      top: 5px
    }

    /* Section title for 'Global leaders in:' */
  .leaders {
      margin-top: 50px;
    }

    /* Flexbox layout for the 3 icons */
  .icons {
      display: flex;                /* places icons side-by-side */
      justify-content: center;      /* centers the group horizontally */
      gap: 30px;                    /* adds space between icons */
      margin-top: 15px;
    }
    
    /* Flexbox layout for the 5 photos */
  .containers {
      display: flex;                /* places icons side-by-side */
      justify-content: center;      /* centers the group horizontally */
      gap: 10px;                    /* adds space between icons */
      margin-top: 15px;
    }
	  /* Employee of the Month Banner */
    .employees-of-the-month {
      background-color: #b5e2fa; /* light blue bar at the bottom */
       width: 100%;
  	  box-sizing: border-box; /* include padding in the width */
      margin-top: 30px;          /* adds space above the banner */
      font-family: 'Brush Script MT', 'Brush Script Std', cursive; /* sets pretty font */
      font-size: 50px;
      text-align: center; 
    }
	/* Container for photos */
    .my-custom-container {
        margin-top: 30px;  /* Spaces from above */
        display: flex;                /* places icons side-by-side */
      	justify-content: center;      /* centers the group horizontally */
        align-items: center;          /* centers container contents */
      	gap: 10px;                    /* adds space between icons */
    }

	/* Box to display names (and center them) */
	.names { 
		display: flex; 
        flex-direction: column; 
        align-items: center; 
        font-family: "Times New Roman", Times, serif;
    }
  </style>
</head>

<body>  
<!-- Integrate universal company header -->
<h1>Global Electronics LLC</h1>

<!-- Login box area -->
  <form action = "index.php" onsubmit = "return ValidateLogin()" class="login-box" method = "post" name="LoginForm">
    <!-- Label and input for username -->
    <label>Username</label>
    <!-- 'form-control' is a Bootstrap class that makes input boxes look nice -->
    <input type="text" class="form-control" name="username">

    <!-- Label and input for password -->
    <label>Password</label>
    <input type="password" class="form-control" name="password">
    <!-- Add button to trigger login screen-->
    <button type="submit" on class="btn btn-primary">Login</button>
	</form>
    
  <!-- Section showing company focus areas -->
  <div class="leaders">
    <h4>Global leaders in:</h4>

    <!-- 3 icons side by side -->
    <div class="icons">
      <div>
        <!-- Emoji acts like a simple image -->
        <p>🏭<br>Manufacturing</p>
      </div>
      <div>
        <p>🚚<br>Distributing</p>
      </div>
      <div>
        <p>🏪<br>Retailing</p>
      </div>
    </div>
  </div>
  <div class="employees-of-the-month">
     Employees of the Month!
  </div>
	<!-- 5 Photo Display --!> 
  <div class="my-custom-container">
      <div>
        <!-- image displayed and the names below -->
        <p> <img src="Ekaterina Cox GM Photo.jpeg" width=150 height=200> </p> <div class="names"> Ekaterina Cox </div>
      </div>
      <div>
        <p><img src="Phoebe.jpg" width=150 height=200></p> <div class="names"> Phoebe Easterling </div>
      </div>
      <div>
        <p><img src="TimmysHeadshot.JPG" width=150 height=200></p> <div class="names"> Timothy Egley </div>
      </div>
      <div>
        <p><img src="IMG_5956.png" width=150 height=200></p> <div class="names"> Victor Hsu </div>
      </div>
      <div>
        <p><img src="unnamed.jpg" width=150 height=200></p> <div class="names"> Harrison Bavone </div>
      </div>
    </div>
</body>

<script> //Use JavaScript to validate the login
function ValidateLogin() {
    const username = document.LoginForm.username.value;
    const password = document.LoginForm.password.value;
    if (username == "" && password == "") {  //Don't send PHP requests without a username and password
        alert ("Please provide a username and password!");
        document.LoginForm.username.focus();
        return false;
    }
    if (username == "") {  //Don't send PHP requests without a username 
        alert ("Please provide a username!");
        document.LoginForm.username.focus();
        return false;
    }

    if (password == "") { //Don't send PHP requests without a password
        alert ("Please provide your password!");
        document.LoginForm.password.focus();
        return false;
    }

    if (username.length > 50 && password.length >255) { //both username and password limits
        alert ("Username can't be longer than 50 characters!\nPassword can't be longer than 255 characters!");
        document.LoginForm.username.focus();
        return false;
    }

    if (username.length > 50) { //Reasonable username limits
        alert ("Username can't be longer than 50 characters!");
        document.LoginForm.username.focus();
        return false;
    }

    if (password.length > 255) { //Reasonable password limits
        alert ("Password can't be longer than 255 characters!");
        document.LoginForm.password.focus();
        return false;
    }

    //Else - continue and run the PHP session script
    return true;
}
</script>
</html>
