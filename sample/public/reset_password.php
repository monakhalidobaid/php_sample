<!-- public/reset_password.php -->
<?php
$token = $_GET['token'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reset Password</title>
  <link rel="stylesheet" href="../assets/styles/login.css">
</head>
<body class="fade-in">
  <div class="login-box">
    <div class="site-title">Inventory Manager</div>
    <h2>Reset Password</h2>
    <p>Enter new password.</p>

    <form id="resetForm">
      <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">
      <div class="input-group">
        <div class="input-wrapper">
          <input type="password" id="new_password" placeholder="New password" required>
          <i class="fa-solid fa-lock"></i>
        </div>
        <span class="error-message" id="new-password-error"></span>
      </div>
      <div class="input-group">
        <div class="input-wrapper">
          <input type="password" id="confirm_password" placeholder="Confirm password" required>
          <i class="fa-solid fa-lock"></i>
        </div>
        <span class="error-message" id="confirm-password-error"></span>
      </div>
      <button type="submit" class="login-btn">Reset Password</button>
      <div id="result" style="margin-top:12px;font-size:14px;"></div>
    </form>
  </div>

<script>
document.getElementById('resetForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const token = document.getElementById('token').value;
  const pw = document.getElementById('new_password').value.trim();
  const pw2 = document.getElementById('confirm_password').value.trim();
  const resultEl = document.getElementById('result');
  resultEl.textContent = '';
  const passPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

  if (pw.length < 8) { resultEl.textContent = 'Password must be at least 8 characters.'; return; }
  if (!passPattern.test(pw)) { resultEl.textContent = 'Password must include upper/lowercase, number, and special char.'; return; }
  if (pw !== pw2) { resultEl.textContent = 'Passwords do not match.'; return; }

  try {
    const res = await fetch("../api/user/reset_password.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ token, password: pw })
    });
    const json = await res.json();
    resultEl.textContent = json.message || 'Done.';
    if (json.success) {
      // redirect to login after short delay
      setTimeout(() => window.location.href = 'login.php', 1500);
    }
  } catch (err) {
    resultEl.textContent = 'Something went wrong.';
  }
});
</script>
</body>
</html>
