<?php
session_start();
include 'db_connect.php'; 
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $nid      = trim($_POST["nid"]);
    $phone    = trim($_POST["phone"]);
    $age      = trim($_POST["age"]);
    $gender   = isset($_POST["gender"]) ? $_POST["gender"] : "";

    //  VALIDATION 
    if (empty($name)) {
        $message = "Name is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid Email Address.";
    } elseif (strlen($password) < 5) {
        $message = "Password must contain at least 5 characters.";
    } elseif (strlen($nid) < 10) {
        $message = "NID must contain at least 10 characters.";
    } elseif (strlen($phone) < 11) {
        $message = "Phone must contain at least 11 characters.";
    } elseif (!is_numeric($age)) {
        $message = "Age must be a number.";
    } elseif (empty($gender)) {
        $message = "Gender is required.";
    } else {
        //  DUPLICATE EMAIL CHECK 
        $check = $conn->prepare("SELECT reg_id FROM registration WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already exists. Please login.";
        } else {
            //PASSWORD HASHING 
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // -INSERT INTO REGISTRATION TABLE 
            $stmt = $conn->prepare("INSERT INTO registration (name,email,password,nid,phone,age,gender) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssis", $name, $email, $hashedPassword, $nid, $phone, $age, $gender);

            if ($stmt->execute()) {
                $_SESSION['name']  = $name;
                $_SESSION['email'] = $email;
                $message = "Registration Completed Successfully!";
                $success = true;
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('87.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .register_box {
      width: 400px;
      background: #ffffff; 
      border-radius: 15px;
      padding: 2em;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.4);
    }
    .register_header { text-align: center; margin-bottom: 20px; }
    .register_header span { font-size: 28px; font-weight: bold; color: #093f5e; }
    .input_box { margin-bottom: 15px; }
    .input_box label { display:block; margin-bottom:5px; font-size:14px; color:#093f5e; }
    .input_box input { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; }
    .btn_box { text-align:center; }
    .btn-main {
      background: teal; color:#fff; border:none; border-radius:8px;
      padding:12px 20px; font-weight:600; cursor:pointer; transition:0.3s;
    }
    .btn-main:hover { background:#006666; }
    .success { text-align:center; margin-top:20px; font-size:16px; color:#093f5e; font-weight:bold; }
    .success a { color:teal; text-decoration:none; font-weight:bold; }
  </style>
</head>
<body>
  <div class="register_box">
    <div class="register_header">
      <span>Sign Up</span>
    </div>
    <form method="POST" action="">
      <div class="input_box"><label>Name</label><input type="text" name="name" required></div>
      <div class="input_box"><label>Email</label><input type="email" name="email" required></div>
      <div class="input_box"><label>Password</label><input type="password" name="password" required></div>
      <div class="input_box"><label>NID No</label><input type="text" name="nid" required></div>
      <div class="input_box"><label>Phone</label><input type="text" name="phone" required></div>
      <div class="input_box"><label>Age</label><input type="number" name="age" required></div>
      <div class="input_box">
        <label>Gender</label>
        <input type="radio" name="gender" value="Male"> Male
        <input type="radio" name="gender" value="Female"> Female
      </div>
      <div class="btn_box"><button type="submit" class="btn-main">Sign Up</button></div>
    </form>

    <?php if (!empty($message)) { ?>
      <div class="success">
        <p><?php echo $message; ?></p>
        <?php if ($success) { ?>
          <!-- ✅ Dashboard link works -->
          <p><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Go to Dashboard</a></p>
        <?php } else { ?>
          <p><a href="INDEX.PHP">Back to Home</a></p>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</body>
</html>
