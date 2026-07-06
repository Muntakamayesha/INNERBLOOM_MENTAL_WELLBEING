<?php
session_start();
include 'db_connect.php'; 

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $name       = trim($_POST["name"]);
    $email      = trim($_POST["email"]);
    $password   = trim($_POST["password"]);
    $nid        = trim($_POST["nid"]);
    $phone      = trim($_POST["phone"]);
    $age        = trim($_POST["age"]); 
    $gender     = isset($_POST["gender"]) ? $_POST["gender"] : "";
    $experience = trim($_POST["experience"]);
    $category   = isset($_POST["category"]) ? $_POST["category"] : "";
    $etin_id    = trim($_POST["etin_id"]);
    $degree     = isset($_POST["degree"]) ? $_POST["degree"] : ""; 

    //verify if all fields are filled
    if (empty($name) || empty($email) || empty($password) || empty($nid) || empty($phone) || empty($age) || empty($gender) || empty($category) || empty($degree)) {
        $message = "All required fields are mandatory.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid Email Address.";
    } elseif (strlen($password) < 5) {
        $message = "Password must contain at least 5 characters.";
    } elseif (strlen($nid) < 10 || strlen($nid) > 17) {
        $message = "NID must contain between 10 and 17 characters.";
    } elseif (!preg_match("/^[0-9]{11}$/", $phone)) {
        $message = "Phone number must be exactly 11 digits.";
    } elseif ($experience < 0 || $experience > 5) {
        $message = "Experience must be between 0 and 5 years as per database rules.";
    } else {
        // file upload 
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $photo_name       = time() . '_' . basename($_FILES['photo']['name']);
        $nid_file_name    = time() . '_' . basename($_FILES['nid_file']['name']);
        $internship_name  = time() . '_' . basename($_FILES['internship']['name']);
        $cv_name          = time() . '_' . basename($_FILES['cv']['name']);

        // Multiple Files
        $academic_files_json = "";
        if (!empty($_FILES['academic_files']['name'][0])) {
            $academic_paths = [];
            foreach ($_FILES['academic_files']['name'] as $key => $val) {
                $file_tmp = $_FILES['academic_files']['tmp_name'][$key];
                $file_name = time() . '_' . basename($val);
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $academic_paths[] = $upload_dir . $file_name;
                }
            }
            $academic_files_json = json_encode($academic_paths);
        }

       
        if (
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name) &&
            move_uploaded_file($_FILES['nid_file']['tmp_name'], $upload_dir . $nid_file_name) &&
            move_uploaded_file($_FILES['internship']['tmp_name'], $upload_dir . $internship_name) &&
            move_uploaded_file($_FILES['cv']['tmp_name'], $upload_dir . $cv_name)
        ) {
            //  password hashing for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            //  (Reference Error Fix) 
            $final_internship_path = $upload_dir . $internship_name;
            $final_photo_path      = $upload_dir . $photo_name;
            $final_cv_path         = $upload_dir . $cv_name;

            //database insertion
            $stmt = $conn->prepare("INSERT INTO doctors (name, email, password, nid, phone, experience, degrees, category, internship_certificate, etin_id, photo, academic_files, cv) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param(
                "sssssisssssss", 
                $name, 
                $email, 
                $hashedPassword, 
                $nid, 
                $phone, 
                $experience, 
                $degree, 
                $category, 
                $final_internship_path, 
                $etin_id, 
                $final_photo_path, 
                $academic_files_json, 
                $final_cv_path
            );

            if ($stmt->execute()) {
                $_SESSION['doctor_name'] = $name;
                $_SESSION['doctor_email'] = $email;
                $success = true;
                $message = "Registration successfully completed!";
            } else {
                $message = "Database Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Failed to upload required documents.";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Enrollment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body{
            background: #f5f7fa;
        }
        .container{
            display: flex;
            min-height: 100vh;
        }
        /* LEFT SIDE */
        .left{
            width: 60%;
            background: #fff;
            padding: 40px;
            overflow-y: auto;
        }
        .logo-section{
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-section h1{
            color: #2563eb;
            font-size: 40px;
        }
        .logo-section p{
            color: #666;
        }
        .progress{
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px 0;
        }
        .circle{
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #cbd5e1;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }
        .active{
            background: #2563eb;
        }
        .line{
            width: 120px;
            height: 2px;
            background: #d1d5db;
        }
        .section-title{
            color: #16a34a;
            margin: 25px 0 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .form-grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group{
            display: flex;
            flex-direction: column;
        }
        label{
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }
        /* Select and Input Global Styling (Error 7 Fixed) */
        input, select{
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus{
            border-color: #2563eb;
        }
        input[type="file"] {
            padding: 8px;
            background: #f8fafc;
        }
        .radio-group{
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            padding-top: 10px;
        }
        .radio-group label{
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            width: auto;
            cursor: pointer;
        }
        .btn_box {
            margin-top: 30px;
        }
        .submit-btn{
            padding: 14px;
            width: 220px;
            border: none;
            border-radius: 8px;
            background: #16a34a;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover{
            background: #15803d;
        }
        /* Message Box Styling (Error 14 Fixed) */
        .message-box{
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }
        .message-box.error-msg {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .message-box.success-msg {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .message-box a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: bold;
        }
        /* RIGHT SIDE */
        .right{
            width: 40%;
            background: url('765.jpg') center center / cover no-repeat;
            position: relative;
        }
        .overlay{
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            color: white;
        }
        .overlay h2{
            margin-bottom: 15px;
        }
        .overlay p{
            color: #ddd;
            margin-bottom: 20px;
        }
        .features{
            display: flex;
            justify-content: space-between;
        }
        .features div{
            flex: 1;
        }
        /* Responsive */
        @media(max-width: 900px){
            .container{
                flex-direction: column;
            }
            .left, .right{
                width: 100%;
            }
            .right{
                height: 400px;
            }
            .form-grid{
                grid-template-columns: 1fr;
            }
            .line{
                width: 60px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left">
        <div class="logo-section">
            <h1>🩺 Doctor Enrollment</h1>
            <p>Join our platform and start helping lives</p>
        </div>
        <div class="progress">
            <div class="circle active">1</div>
            <div class="line"></div>
            <div class="circle">2</div>
            <div class="line"></div>
            <div class="circle">3</div>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message-box <?php echo $success ? 'success-msg' : 'error-msg'; ?>">
                <p><?php echo $message; ?></p>
                <?php if ($success) { ?>
                    <p style="margin-top: 10px;"><a href="doctordashboard.php">Go to Dashboard <i class="fa-solid fa-arrow-right"></i></a></p>
                <?php } else { ?>
                    <p style="margin-top: 10px;"><a href="INDEX.PHP"><i class="fa-solid fa-house"></i> Back to Home</a></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="POST" action="doctorsenrollment.php" enctype="multipart/form-data">

            <h2 class="section-title">Basic Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="e.g. 017XXXXXXXX" pattern="[0-9]{11}" maxlength="11" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" min="22" max="75" placeholder="Enter your age" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" minlength="5" placeholder="Minimum 5 characters" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="Male" required> Male</label>
                        <label><input type="radio" name="gender" value="Female" required> Female</label>
                        <label><input type="radio" name="gender" value="Other" required> Other</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>NID Number</label>
                    <input type="text" name="nid" minlength="10" maxlength="17" placeholder="10 to 17 digits" required>
                </div>
            </div>

            <h2 class="section-title">Professional Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Experience (Years)</label>
                    <input type="number" name="experience" min="0" max="5" placeholder="0 to 5 years" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <div class="radio-group">
                        <label><input type="radio" name="category" value="Psychologist" required> Psychologist</label>
                        <label><input type="radio" name="category" value="Psychiatrist" required> Psychiatrist</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>ETIN ID</label>
                    <input type="text" name="etin_id" placeholder="Optional E-TIN">
                </div>
                <div class="form-group">
                    <label>Professional Photo</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label>Completed Degree</label>
                    <select name="degree" required>
                        <option value="">-- Select Degree --</option>
                        <option value="PhD in Psychology">PhD in Psychology</option>
                        <option value="PsyD">PsyD</option>
                        <option value="MPhil Clinical Psychology">MPhil Clinical Psychology</option>
                        <option value="MSc/MA Clinical Psychology">MSc/MA Clinical Psychology</option>
                        <option value="BSc/BA Psychology">BSc/BA Psychology</option>
                        <option value="DM Psychiatry">DM Psychiatry</option>
                        <option value="MD Psychiatry">MD Psychiatry</option>
                        <option value="FCPS Psychiatry">FCPS Psychiatry</option>
                        <option value="DNB Psychiatry">DNB Psychiatry</option>
                        <option value="MBBS">MBBS</option>
                    </select>
                </div>
            </div>

            <h2 class="section-title">Identity & Documents</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>NID Copy File</label>
                    <input type="file" name="nid_file" accept=".pdf, image/*" required>
                </div>
                <div class="form-group">
                    <label>Academic Files (Multiple)</label>
                    <input type="file" name="academic_files[]" accept=".pdf, image/*" multiple>
                </div>
                <div class="form-group">
                    <label>Internship Certificate</label>
                    <input type="file" name="internship" accept=".pdf, image/*" required>
                </div>
                <div class="form-group">
                    <label>CV / Resume</label>
                    <input type="file" name="cv" accept=".pdf" required>
                </div>
            </div>

            <div class="btn_box">
                <button type="submit" class="submit-btn">Sign Up & Enroll</button>
            </div>
        </form>
    </div>

    <div class="right">
        <div class="overlay">
            <h2>Your Journey to Making a Difference Begins Here</h2>
            <p>Join our network of dedicated healthcare professionals and make a positive impact on mental health care.</p>
            <div class="features">
                <div>
                    <span>🔒</span>
                    <h4>Secure</h4>
                    <small>Your data is safe</small>
                </div>
                <div>
                    <span>👨‍⚕️</span>
                    <h4>Network</h4>
                    <small>Professional community</small>
                </div>
                <div>
                    <span>❤️</span>
                    <h4>Quality Care</h4>
                    <small>Better patient support</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($message)) { ?>
    <div class="message-box <?php echo $success ? 'success-msg' : 'error-msg'; ?>">
        <p class="msg-text"><?php echo $message; ?></p>
        
        <div class="action-buttons">
            <?php if ($success) { ?>
                <a href="doctordashboard.php" class="btn-redirect btn-dashboard">
                    <i class="fa-solid fa-gauge"></i> Go to Dashboard
                </a>
            <?php } else { ?>
                <a href="index.php" class="btn-redirect btn-home">
                    <i class="fa-solid fa-house"></i> Go to Home
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>
</body>
</html>