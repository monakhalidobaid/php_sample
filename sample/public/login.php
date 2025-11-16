<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Manager - Login</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- CSS File -->
  <link rel="stylesheet" href="../assets/styles/login.css">
</head>
<body>
  <div class="login-box">
    <div class="site-title">Inventory Manager</div>
    <h2>Welcome Back</h2>
    <p>Sign in to manage your inventory.</p>
    <form id ="userform">
    <div class="input-group">
      <div class="input-wrapper">
        <input type="text" placeholder="User ID" id="uid" name="uid" required>
        <i class="fa-solid fa-user"></i>
      </div>
      <span class="error-message" id="uid-error"></span>
    </div>

    <div class="input-group">
      <div class="input-wrapper">
        <input type="password" placeholder="Password" id="password" name="password" required>
        <i class="fa-solid fa-lock"></i>
      </div>
      <span class="error-message" id="password-error"></span>
    </div>
      <div class="forgot">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>
      <button type="submit" class="login-btn" id ="login-btn" >Log In</button>
    </form>
  </div>

<script src="../js/login.js"></script>
</body>
</html>
