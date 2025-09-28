<?php
session_start();
require 'connection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info
$email = $_SESSION['email'];
$stmt = $con->prepare("SELECT fullname, email, phone, profile_image FROM devdata WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If no profile image, set default
$profileImage = !empty($user['profile_image']) ? $user['profile_image'] : "assets/maindp.jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DEV-IT Community</title>
<link rel="stylesheet" href="home.css">
<script defer src="home.js"></script>
</head>
<body>

<!-- Navbar -->
<header>
    <nav class="navbar">
        <div class="logo">DEV-IT</div>
        <ul class="nav-links">
            <li><a href="#" class="active">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Events</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <!-- Profile Circle in Navbar -->
<div class="profile-circle" id="profileCircle">
  <img src="https://wallpapercave.com/wp/wp13129658.jpg" alt="Profile" class="nav-profile-img">
</div>

<!-- Profile Card (hidden by default) -->
<div class="profile-card" id="profileCard">
  <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" class="profile-img">

  <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
  
  <div class="profile-body">
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
  </div>

  <!-- Upload new profile image -->
  <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_image" accept="image/*" required>
    <button type="submit" class="upload-btn">Change Image</button>
  </form>

  <a href="logout.php" class="logout-btn">Logout</a>
</div>


<!-- Toggle Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const profileCircle = document.getElementById("profileCircle");
  const profileCard = document.getElementById("profileCard");

  profileCircle.addEventListener("click", () => {
    profileCard.classList.toggle("active");
  });
});
</script>

    </nav>
</header>

<!-- Profile Side Card -->
<div class="profile-card" id="profileCard">
    <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-img" id="profilePreview">
        <input type="file" name="profile_image" accept="image/*" onchange="previewImage(event)" required>
        <button type="submit" class="upload-btn">Change Image</button>
    </form>

    <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
    <div class="profile-body">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>


<!-- Hero Section -->
<section class="hero">
    <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
    <p>Explore the latest events and members in DEV-IT Community</p>
</section>

<!-- Events Section -->
<section class="card-section">
    <h2>Our Events</h2>
    <div class="card-container">
        <div class="card">
            <img src="https://wallpapercave.com/wp/wp8926179.png" alt="Event 1">
            <h3>Garba Night</h3>
            <p>Join us for a colorful Garba celebration!</p>
        </div>
        <div class="card">
            <img src="https://wallpapercave.com/wp/wp9984352.jpg" alt="Event 2">
            <h3>Hackathon</h3>
            <p>Collaborate and innovate in our 24-hour hackathon.</p>
        </div>
        <div class="card">
            <img src="https://wallpapercave.com/fuwp-510/uwp4703588.png" alt="Event 3">
            <h3>Workshop</h3>
            <p>Learn new skills from industry experts.</p>
        </div>
    </div>
</section>
<section class="team-section">
  <h2>Meet Our Team</h2>

  <!-- Management Team -->
  <div class="team-group">
    <h3>Management Team</h3>
    <div class="team-container">
      <div class="team-card">
        <img src="https://wallpapercave.com/wp/wp15499976.jpg" alt="Dinesh Pawar">
        <h4>John Doe</h4>
        <p>President</p>
      </div>
      <div class="team-card">
        <img src="https://wallpapercave.com/wp/wp15500009.jpg" alt="Pratik Pawar">
        <h4>Pratik Pawar</h4>
        <p>Vice President</p>
      </div>
    </div>
  </div>

  <!-- Development Team -->
  <div class="team-group">
    <h3>Development Team</h3>
    <div class="team-container">
      <div class="team-card">
        <img src="https://wallpapercave.com/wp/wp13129658.jpg" alt="Pratiksha Pawar">
        <h4>Pratiksha Pawar</h4>
        <p>Secretary</p>
      </div>
      <div class="team-card">
        <img src="https://wallpapercave.com/wp/wp15499981.jpg" alt="Pranav Pawar">
        <h4>Pranav Pawar</h4>
        <p>Frontend Developer</p>
      </div>
    </div>
  </div>

<script defer src="home.js"></script>
</body>
</html>
