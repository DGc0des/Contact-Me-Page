<?php
$name = $gender = $phone = $birthday = $email = $password = "";
$nameErr = $genderErr = $phoneErr = $birthdayErr = $emailErr = $passwordErr = "";
$success = $celebrate = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $success = true;

    $name = cleanInfo($_POST["name"]);
    if(empty($name)){
        $success = false;
        $nameErr = "Name is required!";
    }

    $gender = cleanInfo($_POST["gender"]);
    if(empty($gender)){
        $success = false;
        $genderErr = "Gender is required!";
    }

    $phone = cleanInfo($_POST["phone"]);
    if(empty($phone)){
        $success = false;
        $phoneErr = "Phone number is required!";
    }elseif(!preg_match("/^[0-9]{10}$/", $_POST["phone"])){
        $phoneErr = "Invalid phone number format!";
        $success = false;
    }

    $birthday = cleanInfo($_POST["birthday"]);
    if(empty($birthday)){
        $success = false;
        $birthdayErr = "Date of birth is required!";
    }else{
        $today = date("Y-m-d");
        $birthdateObj = new DateTime($birthday);
        $todayObj = new DateTime($today);
        $age = $birthdateObj->diff($todayObj);
        if($birthday>$today){
            $success = false;
            $birthdayErr = "Unable to create an account, you are even not born yet!";
        }elseif($age->y <13){
            $success = false;
            $birthdayErr = "Unable to create an account,you must be at least 13 years old"; 
        }else{
            $birthNotyear = date("m-d",strtotime($birthday));
            $todayNotYear = date("m-d",strtotime($today));
            if($birthNotyear == $todayNotYear){
                $celebrate = true;
            }
        }
    }

    $email = cleanInfo($_POST["email"]);
    if(empty($email)){
        $success = false;
        $emailErr = "Email is required!";
    }elseif(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
        $success = false;
        $emailErr = "Invalid email format!";
    }

    $password = $_POST["password"];
    if(empty($password)){
        $success = false;
        $passwordErr = "Password is required";
    }elseif(strlen($_POST["password"])< 8 ){
        $success = false;
        $passwordErr = "Password must be at least 8 characters!";  
    }elseif(!preg_match("/[A-Z]/",$_POST["password"])){
        $success = false;
        $passwordErr = "Password must contain at least one uppercase letter";
    }elseif(!preg_match("/[0-9]/",$_POST["password"])){
        $success = false;
        $passwordErr = "Password must contain at least one number";
    }elseif(!preg_match("/[\W_]/",$_POST["password"])){
        $success = false;
        $passwordErr = "Password must contain at least one special character";
    }else{
        $password = password_hash($password, PASSWORD_DEFAULT); // no need giati den to kanw store pouthena alla good practice.
    }

}


function cleanInfo($data){
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="style.css">  <!-- An yparxei provlhma syndeshs me to css allh href value me style.css?v=1.0 --> 
</head>
<body>
    <div class="container">
        <h2>Create Your Account</h2>
        <?php if($success):?>
            <div class="success page">
                <p>Thank you <strong><?php echo $name?></strong> for creating an account on our platform. </p>
                <p>You will receive more details at <strong><?php echo $email?></strong></p>
                <?php if($celebrate):?>
                    <p class="cel">PS, <strong>HAPPY BIRTHDAY:&#41;</strong></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateEmail()">
            
            <!-- NAME -->
            <div class="entireForm">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Makhs kotsampashs" value="<?php echo $name; ?>">
                <span class="error"><?php echo $nameErr; ?></span>
            </div>
            
            <!-- GENDER -->
            <div class="entireForm">
                <label for="gender">Gender:</label>
                <select name="gender">
                    <option value=""disabled selected>Select your Gender</option>
                    <option value="male" <?php if ($gender == "male") echo "selected"; ?>>Male</option>
                    <option value="female" <?php if ($gender == "female") echo "selected"; ?>>Female</option>
                    <option value="other" <?php if ($gender == "Other") echo "selected"; ?>>Other</option>
                    <option value="RatherNotSay" <?php if ($gender == "RatherNotSay") echo "Rather not say"; ?>>Rather not say</option>
                </select>
                <span class="error"><?php echo $genderErr; ?></span>
            </div>

            <!-- PHONE NUMBER -->
            <div class="entireForm">
                <label for="phone">Phone Number:</label>
                <input type="tel" name="phone" pattern="[0-9]*" inputmode="numeric" placeholder="69--------" value="<?php echo $phone; ?>"> <!-- inputmode kalytera tel alla sta requirements elege numeric -->
                <span class="error"><?php echo $phoneErr; ?></span>
            </div>

            <!-- BIRTHDAY -->
            <div class="entireForm">
                <label for="birthday">Date of Birth</label>
                <input type="date" name="birthday" id="birthday" placeholder="MM/DD/YYYY" value="<?php echo $birthday; ?>">
                <span class="error"><?php echo $birthdayErr; ?></span>  
            </div>

            <!-- EMAIL -->
            <div class="entireForm">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="fake24@gmail.com" value="<?php echo $email; ?>">
                <span class="error"><?php echo $emailErr; ?></span>
            </div>

            <!-- PASSWORD -->
            <div class="entireForm">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="*******" value="<?php echo $password; ?>">
                <span class="error"><?php echo $passwordErr?></span>
            </div>

            <!-- SUBMIT -->
            <div class="form-group">
                    <input type="submit" value="Create Account">
            </div>
        </form>
    <?php endif;?>
    </div>

    <script> 
  document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#birthday", {
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "F j, Y",
      allowInput: true,
    });
  });

  function validateEmail() {
    var email = document.getElementById("email").value;
    if (email.toLowerCase().endsWith(".con")) {
        var fix = confirm("It looks like you might have meant '.com'. Click ok to change it to .com. Click cancel to keep .con"); //epitrepw .con gia kapoio custom domain (?)
        if (fix) {
            document.getElementById("email").value = email.substring(0, email.length - 4) + ".com";
        }
    }
    return true; 
}

</script>


</body>
</html>