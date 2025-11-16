const form = document.getElementById('userForm');
const tbody = document.querySelector('#userTable tbody');
const searchInput = document.getElementById('searchInput');
const result = document.getElementById('result');

const user_name = document.getElementById("username");
const uid = document.getElementById("uid");
const password = document.getElementById("password");
const usertype = document.getElementById("usertype");
const email = document.getElementById("email");
const phone = document.getElementById("phone");

let currentPage = 1;
let limit = 5;

// ==== Pagination ====
function renderPagination(total, page, limit) {
  const totalPages = Math.ceil(total / limit);
  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  pagination.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === page) btn.classList.add("active");

    btn.addEventListener("click", () => {
      currentPage = i;
      fetchUsers();
    });

    pagination.appendChild(btn);
  }
}
// ==== Validation helpers ====
function showFieldError(field, msg) {
    const span = field.parentElement.querySelector('.error-message');
    if (span) { span.textContent = msg; span.classList.add('show'); }
    field.classList.add('input-error');
}
function clearErrors() {
    document.querySelectorAll("#userForm .error-message").forEach(span => {
        span.textContent = ""; span.classList.remove("show");
    });
    document.querySelectorAll("#userForm input, #userForm select").forEach(input => {
        input.classList.remove("input-error");
    });
}
function validateUserForm() {
    let isValid = true;
    clearErrors();
    if (!user_name.value.trim()) { showFieldError(user_name,"Username is required"); isValid=false; }
    const uidVal = uid.value.trim();
    if(uidVal.length<4 || !/[A-Za-z]/.test(uidVal) || !/\d/.test(uidVal)){ showFieldError(uid,"UID must be at least 4 chars and contain english letters and numbers"); isValid=false; }
    const passVal = password.value;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.\-_\+#^])[A-Za-z\d@$!%*?&.\-_\+#^]{8,}$/;
    if(!passRegex.test(passVal)){ showFieldError(password,"Password must be ≥8 chars and include upper, lower, number and special char"); isValid=false; }
    if(usertype.value===""){ showFieldError(usertype,"Please select user type"); isValid=false; }
    const emailVal = email.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(emailVal)){ showFieldError(email,"Invalid email format"); isValid=false; }
    const phoneVal = phone.value.trim();
    const phoneRegex = /^[0-9]{8,15}$/;
    if(!phoneRegex.test(phoneVal)){ showFieldError(phone,"Invalid phone number (8–15 digits)"); isValid=false; }
    return isValid;
}

// ==== Render Users ====
function displayUsers(users) {
    tbody.innerHTML = '';

    users.forEach(user => {
        const tr = document.createElement('tr');

        const tdId = document.createElement('td'); tdId.textContent = user.id;
        const tdName = document.createElement('td'); tdName.textContent = user.user_name;
        const tdUID = document.createElement('td'); tdUID.textContent = user.uid;
        const tdType = document.createElement('td'); tdType.textContent = user.user_type;
        const tdEmail = document.createElement('td'); tdEmail.textContent = user.email;
        const tdPhone = document.createElement('td'); tdPhone.textContent = user.phone;

        const tdActions = document.createElement('td');
        if (window.userType === 'admin') {
            const dropdown = document.createElement('div');
            dropdown.classList.add('dropdown');
            const btn = document.createElement('button');
            btn.classList.add('dropdown-btn'); btn.textContent = '⋮';
            const menu = document.createElement('ul');
            menu.classList.add('dropdown-menu');

            const liEdit = document.createElement('li');
            const editLink = document.createElement('a');
            editLink.href = '#'; editLink.classList.add('edit'); editLink.innerHTML = '<span class="material-symbols-outlined">edit</span>Edit';
            liEdit.appendChild(editLink);

            const liDelete = document.createElement('li');
            const delLink = document.createElement('a');
            delLink.href = '#'; delLink.classList.add('delete'); delLink.innerHTML = '<span class="material-symbols-outlined">delete</span>Delete';
            liDelete.appendChild(delLink);

            menu.append(liEdit, liDelete);
            dropdown.append(btn, menu);
            tdActions.appendChild(dropdown);
        }

        tr.append(tdId, tdName, tdUID, tdType, tdEmail, tdPhone, tdActions);
        tbody.appendChild(tr);
    });
}

// ==== Fetch Users ====
async function fetchUsers() {
    const q = searchInput.value.trim();
    try {
        const res = await fetch(`../api/user/get.php?q=${encodeURIComponent(q)}&page=${currentPage}&limit=${limit}`);
        const data = await res.json();
        if (data.success) {
            displayUsers(data.users);
            renderPagination(data.total, data.page, data.limit);
        }
    } catch (err) {
        console.error(err);
        showToast("Error fetching users", "error");
    }
}

// ==== Search ====
searchInput.addEventListener('input', debounce(()=>{ currentPage=1; fetchUsers(); }, 300));

// ==== Form submit ====
if (form) {
    form.addEventListener('submit', async e=>{
        e.preventDefault();
        if(!validateUserForm()) return;

        const payload = {
            username: user_name.value.trim(),
            uid: uid.value.trim(),
            password: password.value,
            usertype: usertype.value,
            email: email.value.trim(),
            phone: phone.value.trim()
        };

        try {
            const res = await fetch('../api/user/insert.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify(payload)
            });
            const data = await res.json();
            if(data.success){
                showToast(data.message,'success');
                form.reset();
                fetchUsers();
            } else showToast(data.message,'error');
        } catch(err){
            console.error(err);
            showToast("Server error",'error');
        }
    });
}

// ==== Dropdown actions ====
document.addEventListener('click', e=>{
    if(e.target.closest('.dropdown-btn')){
        e.preventDefault();
        document.querySelectorAll('.dropdown.open').forEach(d=>d.classList.remove('open'));
        e.target.closest('.dropdown').classList.add('open');
    } else if (!e.target.closest('.dropdown')){
        document.querySelectorAll('.dropdown.open').forEach(d=>d.classList.remove('open'));
    }

    if(e.target.closest('.dropdown-menu a')) {
        e.target.closest('.dropdown').classList.remove('open');
    }
});

// ==== Initial load ====
document.addEventListener('DOMContentLoaded', ()=>{ fetchUsers(); });

// ==== edit model error msg ====
function showEditFieldError(field, msg) {
  const span = field.parentElement.querySelector('.error-message');
  if (span) {
    span.textContent = msg;
    span.classList.add("show");
  }
  field.classList.add("input-error");
}

function clearEditErrors() {
  editForm.querySelectorAll('.error-message').forEach(span => {
    span.textContent = "";
    span.classList.remove("show");
  });
  editForm.querySelectorAll('input, select').forEach(input => {
    input.classList.remove("input-error");
  });
}

// ========= ✅ كود المودال يبدأ من هنا =========
const editModal = document.getElementById("editModal");
const editForm = document.getElementById("editUserForm");
const editUsername = document.getElementById("editUsername");
const editUid = document.getElementById("editUid");
const editUserType = document.getElementById("editUserType");
const editEmail = document.getElementById("editEmail");
const editPhone = document.getElementById("editPhone");
let currentUsertId = null;

const editErrorMessage = document.getElementById("edit-error-message");

// فتح المودال عند الضغط على Edit
document.addEventListener("click", function(e) {
  if (e.target.closest(".edit")) {
    e.preventDefault();

    let row = e.target.closest("tr");
    //currentDeptId = row.cells[0].textContent;
    currentUsertId = parseInt(row.cells[0].textContent.trim(), 10);
    let USERNAME = row.cells[1].textContent;
    let UID = row.cells[2].textContent;
    let USERTYPE = row.cells[3].textContent;
    let EMAIL = row.cells[4].textContent;
    let PHONE = row.cells[5].textContent;

    editUsername.value = USERNAME;
    editUid.value = UID;
    editUserType.value = USERTYPE;
    editEmail.value = EMAIL;
    editPhone.value = PHONE;


    editModal.style.display = "block";
    setTimeout(() => editModal.classList.add("show"), 10);

    // سكرول بسلاسة للمودال
    editModal.scrollIntoView({ behavior: "smooth", block: "start" });
  }

if (e.target.classList.contains("close-btn")) {
  editModal.style.display = "none";

  // ✅ تنظيف الخطأ عند الإغلاق
    clearEditErrors()

}
});

// إغلاق عند الضغط خارج المودال
window.addEventListener("click", function(e) {
  if (e.target === editModal) {
    editModal.style.display = "none";

    // ✅ تنظيف الخطأ لما يقفل المودال بالضغط بره
    clearEditErrors()

  }
});

// تحديث بيانات القسم
editForm.onsubmit = async function(e) {
  e.preventDefault();
  clearEditErrors();

  let usernameValue = editUsername.value.trim();
  let uidValue = editUid.value.trim();
  let usertypeValue = editUserType.value;
  let emailValue = editEmail.value.trim();
  let phoneValue = editPhone.value.trim();

  // فاليديشن قبل الإرسال
if (usernameValue === "") {
  showEditFieldError(editUsername, "Username is required");
  return;
}

if (uidValue.length < 4 || !/[A-Za-z]/.test(uidValue) || !/\d/.test(uidValue)) {
  showEditFieldError(editUid, "UID must be at least 4 chars and contain letters and numbers");
  return;
}

if (usertypeValue === "") {
  showEditFieldError(editUserType, "Please select user type");
  return;
}

if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
  showEditFieldError(editEmail, "Invalid email format");
  return;
}

if (!/^[0-9]{8,15}$/.test(phoneValue)) {
  showEditFieldError(editPhone, "Invalid phone number (8–15 digits)");
  return;
}
  let updatedData = {
    id: currentUsertId,
    name: usernameValue,
    uid:uidValue ,
    usertype: usertypeValue,
    email:emailValue,
    phone:phoneValue
  };

 try {
  const response = await fetch("../api/user/update.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(updatedData)
  });

  const data = await response.json();

if (data.success) {
    showToast(data.message, "success");
    await fetchUsers();
    editModal.style.display = "none";
    clearEditErrors();
} else {
    // لو السيرفر رجع خطأ UID أو Email موجود
    if (data.message.toLowerCase().includes("uid")) {
        showEditFieldError(editUid, data.message);
    } else if (data.message.toLowerCase().includes("email")) {
        showEditFieldError(editEmail, data.message);
    } else {
        // أي خطأ عام نعرضه تحت اسم المستخدم
        showToast(data.message, "error");
    }
}

} catch (err) {
  console.error("Error updating user:", err);
  showEditFieldError(editUsername, "Error connecting to server");
}

};

// ====== حذف يوزر ======
const deleteModal = document.getElementById("deleteModal");
const confirmDeleteBtn = document.getElementById("confirmDelete");
const cancelDeleteBtn = document.getElementById("cancelDelete");
let userIdToDelete = null;

// فتح المودال عند الضغط على Delete
document.addEventListener("click", function(e) {
  if (e.target.closest(".delete")) {
    e.preventDefault();
    let row = e.target.closest("tr");
    userIdToDelete = parseInt(row.cells[0].textContent.trim(), 10);
    deleteModal.style.display = "block";
        // إضافة class لتفعيل الانزلاق
    // إضافة class لتفعيل الانزلاق
    setTimeout(() => deleteModal.classList.add("show"), 10);

    // سكرول بسلاسة للمودال
    deleteModal.scrollIntoView({ behavior: "smooth", block: "start" });  }
});

// إغلاق عند الضغط على Cancel
cancelDeleteBtn.addEventListener("click", () => {
  deleteModal.style.display = "none";
  userIdToDelete = null;
});

// تنفيذ الحذف
confirmDeleteBtn.addEventListener("click", async () => {
  if (!userIdToDelete) return;

  try {
    const response = await fetch("../api/user/delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: userIdToDelete })
    });

    const data = await response.json();

    if (data.success) {
      showToast(data.message, "success");
      await fetchUsers();
    } else {
      showToast(data.message, "error");
    }
  } catch (err) {
    console.error("Error deleting department:", err);
    showToast("Error connecting to server", "error");
  }

  deleteModal.style.display = "none";
  userIdToDelete = null;
});

// إغلاق عند الضغط خارج المودال
window.addEventListener("click", function(e) {
  if (e.target === deleteModal) {
    deleteModal.style.display = "none";
    userIdToDelete = null;
  }
});
