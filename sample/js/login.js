const uidInput = document.getElementById("uid");
const passwordInput = document.getElementById("password");
const uidError = document.getElementById("uid-error");
const passwordError = document.getElementById("password-error");

document.getElementById("userform").addEventListener("submit", async (e) => {
  e.preventDefault();

  // 🧹 تنظيف الأخطاء القديمة
  [uidError, passwordError].forEach(el => {
    el.textContent = "";
    el.classList.remove("show");
  });
  [uidInput, passwordInput].forEach(el => el.classList.remove("input-error"));

  const uid = uidInput.value.trim();
  const password = passwordInput.value.trim();
  let hasError = false;

  // ✅ Validation UID
  if (!uid) {
    uidError.textContent = "Please enter User ID";
    uidError.classList.add("show");
    uidInput.classList.add("input-error");
    hasError = true;
  } else if (uid.length <= 3) {
    uidError.textContent = "User ID must be greater than 3 characters";
    uidError.classList.add("show");
    uidInput.classList.add("input-error");
    hasError = true;
  } else if (!/^[a-zA-Z0-9]+$/.test(uid)) {
    uidError.textContent = "User ID can only contain letters and numbers";
    uidError.classList.add("show");
    uidInput.classList.add("input-error");
    hasError = true;
  }

  // ✅ Validation Password
  const passPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
  if (!password) {
    passwordError.textContent = "Please enter Password";
    passwordError.classList.add("show");
    passwordInput.classList.add("input-error");
    hasError = true;
  } else if (!passPattern.test(password)) {
    passwordError.textContent = "Password must be at least 8 characters, with upper/lowercase, number, and special char";
    passwordError.classList.add("show");
    passwordInput.classList.add("input-error");
    hasError = true;
  }

  if (hasError) return;

  // 🚀 إرسال للسيرفر
  try {
    let response = await fetch("../api/user/login_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
        credentials: 'same-origin', // <--- الأهم لو على نفس الدومين
      body: JSON.stringify({ uid, password })
    });

    let result = await response.json();

    if (result.success) {
      document.body.classList.add("fade-out");
      setTimeout(() => {
        if (result.user_type === "admin") {
          window.location.href = "../public/dashboard.php";
        } else {
          window.location.href = "../public/dashboard.php";
        }
      }, 600);
    } else {
      // ✨ رسائل خطأ السيرفر → نحطها فوق الباسورد
      passwordError.textContent = result.message;
      passwordError.classList.add("show");
      passwordInput.classList.add("input-error");
    }
  } catch (err) {
    console.error("Error:", err);
    passwordError.textContent = "Something went wrong, please try again.";
    passwordError.classList.add("show");
    passwordInput.classList.add("input-error");
  }
});
