<!-- public/forgot_password.php -->
<?php /* صفحة عرض */ ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Forgot Password - Inventory Manager</title>
  <link rel="stylesheet" href="../assets/styles/login.css">
</head>
<body class="fade-in">
  <div class="login-box">
    <div class="site-title">Inventory Manager</div>
    <h2>Forgot Password</h2>
    <p>Enter your email to receive a reset link.</p>

    <form id="forgotForm">
      <div class="input-group">
        <div class="input-wrapper">
          <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <span class="error-message" id="email-error"></span>
      </div>
      <button type="submit" class="login-btn">Send Reset Link</button>
      <div id="result" style="margin-top:12px;font-size:14px;"></div>
    </form>
  </div>

<script>
document.getElementById('forgotForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  const resultEl = document.getElementById('result');
  const submitBtn = e.target.querySelector('button[type="submit"]');
  
  submitBtn.disabled = true;
  submitBtn.textContent = 'Sending...';
  resultEl.textContent = '';

  try {
    const res = await fetch("../api/user/request_password_reset.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email })
    });
    const json = await res.json();
    
    if (json.success === false) {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Reset Link';
      resultEl.style.color = '#f44336';
      resultEl.textContent = '✗ ' + json.message;
    } else {
      resultEl.style.color = '#4CAF50';
      resultEl.textContent = '✓ Reset link sent! Check your email. Redirecting to login...';
      
      // ✅ استخدم replace بدلاً من href
      setTimeout(() => {
        window.location.replace('login.php');
      }, 3000);
    }
    
  } catch (err) {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Send Reset Link';
    resultEl.style.color = '#f44336';
    resultEl.textContent = '✗ Something went wrong. Try again later.';
  }
});
</script>
</body>
</html>
