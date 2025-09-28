<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="form-container">
    <h2>Signup</h2>

    <!-- Show session message if available -->
    <?php if(isset($_SESSION['message'])): ?>
      <p class="msg"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form action="signup-handler.php" method="POST">
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="number" name="age" placeholder="Age" required>
      <input type="text" name="phone" placeholder="Phone" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="cpassword" placeholder="Confirm Password" required>
      <button type="submit" name="signup">Signup</button>
    </form>
  </div>
  <script src="script.js"></script>
</body>
</html>
