<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InnerBloom - Mental Wellness Hub</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body { margin:0; font-family:Arial,sans-serif; }
    .btn-main { background:#63C1BB; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; margin:5px; }
    .btn-main:hover { background:#3A9295; }
    .popup { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:1000; }
    .popup h3 { margin-top:0; }
    .overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; }
    .howworks { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; width:400px; }
    .howworks ul { padding-left:20px; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <img src="logo.png" alt="Logo" class="small-logo">
    <a href="subscription.php"><i class="fa-solid fa-credit-card"></i> Subscription</a>
    <a href="registerform.php"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
    <a href="doctorsenrollment.php"><i class="fa-solid fa-user-doctor"></i> Doctor Enrollment</a>
    <a href="about.php"><i class="fa-solid fa-circle-info"></i> About Us</a>
    <a href="chatbot.py"><i class="fa-solid fa-robot"></i> AI Assistant</a>
    <a href="questionnaire.php"><i class="fa-solid fa-circle-question"></i> Questionnaire</a>
    <a href="bookappointment.php"><i class="fa-solid fa-calendar-check"></i> Book Appointment</a>
  </aside>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-left">
      <h2>Your <span class="highlight">wellbeing,</span><br>
          our unwavering<br>
          <span class="highlight">mission</span><br><br><br>
      </h2>
      <ul>
        <li><i class="fa-solid fa-circle-check"></i><b> Verified Mental Health Experts</b></li>
        <li><i class="fa-solid fa-lock"></i><b> Private & Secure Consultations</b></li>
        <li><i class="fa-solid fa-circle-check"></i><b> 24/7 Support & Wellbeing Tools</b></li>
      </ul>
      <div class="btn-group">
        <button class="btn-main" id="getStartedBtn">Get Started</button>
        <button class="btn-main" id="howWorksBtn">How It Works</button>
      </div>
    </div>

    <div class="hero-center">
      <img src="14.jpg" alt="Hero Illustration" class="floating">
    </div>

    <div class="hero-right">
      <div class="action-card"><i class="fa-solid fa-user-plus"></i><b> Sign Up </b><span>Create your account</span></div>
      <div class="action-card"><i class="fa-solid fa-calendar-check"></i><b> Book a Session </b><span>Schedule with a doctor</span></div>
      <div class="action-card"><i class="fa-solid fa-robot"></i><b> AI Assistant </b><span>Instant mental support</span></div>
      <div class="action-card"><i class="fa-solid fa-circle-question"></i><b> Get Info </b><span>Help & Support</span></div>
    </div>
  </section>
<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background:rgba(0,0,0,0.5); z-index:999;"></div>

<!-- How It Works Popup -->
<div class="howworks" id="howWorksPopup" style="display:none; position:fixed; top:50%; left:50%; 
     transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; z-index:1000;">
    <h3>How It Works</h3>
    <ul>
      <li>Complete Sign Up / Login</li>
      <li>Profile Setup</li>
      <li>Personalized Recommendations</li>
      <li>Booking a Session</li>
      <li>Tracking & Progress</li>
    </ul>
    <button class="btn-main" onclick="closeHowWorks()">Close</button>
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); z-index:999;"></div>

<!-- Get Started Popup -->
<div id="getStartedPopup" style="display:none; position:fixed; top:50%; left:50%;
     transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; z-index:1000;">
  <h3>Are you a new user or old user?</h3>
  <button class="btn-main" onclick="openRegister()">New User</button>
  <button class="btn-main" onclick="openLogin()">Old User</button>
  <button class="btn-main" onclick="closePopup()">Close</button>
</div>

<!-- How It Works Popup -->
<div id="howWorksPopup" style="display:none; position:fixed; top:50%; left:50%;
     transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; z-index:1000;">
  <h3>How It Works</h3>
  <ul>
    <li>Complete Sign Up / Login</li>
    <li>Profile Setup</li>
    <li>Personalized Recommendations</li>
    <li>Booking a Session</li>
    <li>Tracking & Progress</li>
  </ul>
  <button class="btn-main" onclick="closeHowWorks()">Close</button>
</div>

<script>
  const getStartedBtn   = document.getElementById('getStartedBtn');
  const howWorksBtn     = document.getElementById('howWorksBtn');
  const overlay         = document.getElementById('overlay');
  const getStartedPopup = document.getElementById('getStartedPopup');
  const howWorksPopup   = document.getElementById('howWorksPopup');

  //  Open Get Started popup
  if(getStartedBtn){
    getStartedBtn.addEventListener('click', () => {
      overlay.style.display = 'block';
      getStartedPopup.style.display = 'block';
    });
  }

  //  Open How Works popup
  if(howWorksBtn){
    howWorksBtn.addEventListener('click', () => {
      overlay.style.display = 'block';
      howWorksPopup.style.display = 'block';
    });
  }

  //  Close Get Started popup
  function closePopup() {
    overlay.style.display = 'none';
    getStartedPopup.style.display = 'none';
  }

  //  Close How Works popup
  function closeHowWorks() {
    overlay.style.display = 'none';
    howWorksPopup.style.display = 'none';
  }

  // Redirect functions
  function openLogin() {
    window.location.href = "loginform.php"; // Old user → login form
  }

  function openRegister() {
    window.location.href = "registerform.php"; // New user → register form
  }

  //  Overlay click closes everything
  overlay.addEventListener('click', () => {
    overlay.style.display = 'none';
    getStartedPopup.style.display = 'none';
    howWorksPopup.style.display = 'none';
  });
</script>

 
</body>
<footer class="footer">
  <div class="footer-bottom">
    <p>&copy; 2026 InnerBloom. All rights reserved.</p>
  </div>
</footer>
</html>
