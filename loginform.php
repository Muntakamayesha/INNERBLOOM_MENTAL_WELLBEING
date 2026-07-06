<?php
// BACKEND: LOGIN LOGIC 
session_start();
include 'db_connect.php';

$message = "";
$status = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email)) {
        $message = "Email is required.";
        $status = "error";
    } elseif (empty($password)) {
        $message = "Password is required.";
        $status = "error";
    } else {
        $stmt = $conn->prepare("SELECT name, password FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $email;
                $message = "Login Completed Successfully";
                $status = "success";
                
                             
                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Invalid Password.";
                $status = "error";
            }
        } else {
            $message = "Email not found.";
            $status = "error";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mental Wellness Hub - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
   
      background: #1a2a3a url('CD.JPG') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .login_box {
      width: 400px;
      background: rgba(0, 0, 0, 0.6); 
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 15px;
      padding: 3em 2em;
      color: white;
      box-shadow: 0px 8px 32px rgba(0,0,0,0.4);
    }
    .login_header { text-align: center; margin-bottom: 20px; }
    .login_header span { font-size: 28px; font-weight: bold; color: #fff; }
    .input_box { margin-bottom: 15px; }
    .input_box label { display:block; margin-bottom:5px; font-size:14px; color: #eee; }
    .input_box input { width:100%; padding:10px; border-radius:8px; border:none; box-sizing: border-box; color: #000; }
    .btn_box { text-align:center; margin-top: 20px; }
    .btn-main {
      width: 100%;
      background:#63C1BB; color:#fff; border:none; border-radius:8px;
      padding:12px 20px; font-weight:600; cursor:pointer; transition:0.3s;
    }
    .btn-main:hover { background:#3A9295; }
    .response_msg { text-align:center; margin-top:20px; font-size:16px; font-weight: bold; }
    .error_msg { color: #ff6b6b; }
    .success_msg { color: #AFD06E; }
  </style>
</head>
<body>

  <div class="login_box">
    <div class="login_header">
      <span><i class="fa-solid fa-right-to-bracket"></i> Login</span>
    </div>

    <!-- Keeping the action empty means
      the form will submit to the same file, so no 404 error will occur.-->
    <form action="" method="POST">
      <div class="input_box">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="input_box">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="btn_box">
        <button type="submit" class="btn-main">Login</button>
      </div>
    </form>

    <!-- Displaying the message -->
    <?php if (!empty($message)): ?>
        <div class="response_msg <?php echo ($status === 'success') ? 'success_msg' : 'error_msg'; ?>">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>
  </div>

</body>
</html>
