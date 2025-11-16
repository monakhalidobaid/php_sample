const form = document.getElementById('deptrForm');
const result = document.getElementById('result');
const tbody = document.querySelector('#deptTable tbody');
let dept_name = document.getElementById("dept-name");
let errorMessage = null;

// الكود هذا يخلي الـ errorMessage يتحدد بس إذا الفورم موجود
if (dept_name) {
    errorMessage = dept_name.nextElementSibling;
}

//Departmen validation
function validateDepartmentName(name) {
    let value = name.trim(); //remove spaces
    //let englishOnly = /^[A-Za-z\s]+$/;

    if (value === "") {
        return "Please enter the department name";
    } else if (value.length < 2) {
        return "The department name must be at least two letters long.";
    }/* else if (!englishOnly.test(value)) { //Tests if the value is equal to the englishOnly and returns true.
        return "The department name must contain only English letters";
    }*/ 
    return ""; 
}

//display department
function displaydept(depts) {
  tbody.innerHTML = '';

  depts.forEach(dept => {
    let row = document.createElement('tr');

    let td1 = document.createElement('td');
    td1.textContent = dept.dept_id;

    let td2 = document.createElement('td');
    td2.textContent = dept.dept_name;

    let td3 = document.createElement('td');
    td3.textContent = dept.status;

    let td4 = document.createElement('td');

    if (window.userType === 'admin') {
      td4.innerHTML = `
        <div class="dropdown">
          <button class="dropdown-btn">⋮</button>
          <ul class="dropdown-menu">
            <li><a href="#" class="edit"><span class="material-symbols-outlined">edit</span>Edit</a></li>
            <li><a href="#" class="delete"><span class="material-symbols-outlined">delete</span>Delete</a></li>
          </ul>
        </div>
      `;
    } else {
      td4.textContent = ""; // مستخدم عادي: بدون خيارات
    }

    row.append(td1, td2, td3, td4);
    tbody.appendChild(row);
  });
}

// التحكم في فتح/إغلاق القائمة ⋮
document.addEventListener("click", function(e) {
  // لو ضغط على زر النقاط
  if (e.target.closest(".dropdown-btn")) {
    e.preventDefault();

    // قفل أي قائمة مفتوحة قبل فتح الجديدة
    document.querySelectorAll(".dropdown.open").forEach(menu => {
      menu.classList.remove("open");
    });

    // فتح القائمة الحالية
    const dropdown = e.target.closest(".dropdown");
    dropdown.classList.add("open");
  } else {
    // لو ضغط خارج أي قائمة → قفل الكل
    if (!e.target.closest(".dropdown")) {
      document.querySelectorAll(".dropdown.open").forEach(menu => {
        menu.classList.remove("open");
      });
    }
  }
});

// إغلاق القائمة بعد اختيار Edit أو Delete
document.addEventListener("click", function(e) {
  if (e.target.closest(".dropdown-menu a")) {
    const dropdown = e.target.closest(".dropdown");
    dropdown.classList.remove("open");
  }
});

//Add New Department
if(form){
form.onsubmit = async function(e) {
  e.preventDefault();

      let error = validateDepartmentName(dept_name.value);

if (error) {
    e.preventDefault();
    errorMessage.textContent = error;
    errorMessage.classList.add("show");       // تظهر الرسالة تدريجياً
    dept_name.classList.add("input-error");   // الحدود تتحول للأحمر تدريجياً
            return; // يمنع متابعة تنفيذ الكود

} else {
    errorMessage.textContent = "";
    errorMessage.classList.remove("show");    // تختفي تدريجياً
    dept_name.classList.remove("input-error");

}
  const deptNameValue = document.getElementById("dept-name").value.trim();

    const deptData = { deptNameValue }; 
    console.log(deptData)

  try {
    const response = await fetch('../api/department/insert.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(deptData)
    });
    

    const data = await response.json();

    if (data.success) {
            result.style.color = 'green';
            result.textContent = data.message;
            await loadDepartments();
            form.reset();
        } else {
            result.style.color = 'red';
            result.textContent = data.message;
        }

    }  catch(err) {
        result.style.color = 'red';
        result.textContent = 'Error connecting to server';
       console.error(err);
       
    }
}
};

// ==== Pagination ====
function renderPagination(total, page, limit, search) {
  const totalPages = Math.ceil(total / limit);
  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  pagination.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === page) btn.classList.add("active");

    btn.addEventListener("click", () => {
      loadDepartments(search, i, limit);
    });

    pagination.appendChild(btn);
  }
} 
//fetch department with paging
async function loadDepartments(search = '', page = 1, limit = 5) {
  try {
    const params = new URLSearchParams();
    if (search) params.append("q", search);
    params.append("page", page);
    params.append("limit", limit);

    const url = `../api/department/get.php?${params.toString()}`;
    const response = await fetch(url);
    const data = await response.json();

    if (data.success) {
      displaydept(data.departments);
      renderPagination(data.total, data.page, data.limit, search);
    }
  } catch (err) {
    console.error("Error fetching departments:", err);
  }
}

// search dynamic 
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-input");

  // البحث المباشر بمجرد الكتابة
  if (searchInput) {
    searchInput.addEventListener("input", debounce((e) => {
      const query = e.target.value.trim();
      loadDepartments(query, 1); // دايم يبدأ من الصفحة 1
    }, 300));
  }

  loadDepartments(); // initial load
});

// عناصر المودال
const editModal = document.getElementById("editModal");
const editForm = document.getElementById("editDeptForm");
const editName = document.getElementById("edit-dept-name");
const editStatus = document.getElementById("edit-dept-status");
let currentDeptId = null;
// عناصر الخطأ للمودال
const editErrorMessage = document.getElementById("edit-error-message");

// فتح المودال عند الضغط على Edit
document.addEventListener("click", function(e) {
  if (e.target.closest(".edit")) {
    e.preventDefault();

    let row = e.target.closest("tr");
    //currentDeptId = row.cells[0].textContent;
    currentDeptId = parseInt(row.cells[0].textContent.trim(), 10);
    let deptName = row.cells[1].textContent;
    let deptStatus = row.cells[2].textContent;


// تنظيف أي رسائل قديمة قبل فتح المودال
editErrorMessage.textContent = "";
editErrorMessage.classList.remove("show");
editName.classList.remove("input-error");
    editName.value = deptName;
    editStatus.value = deptStatus;

    editModal.style.display = "block";
            // إضافة class لتفعيل الانزلاق
    setTimeout(() => editModal.classList.add("show"), 10);

    // سكرول بسلاسة للمودال
    editModal.scrollIntoView({ behavior: "smooth", block: "start" });
  }
  

if (e.target.classList.contains("close-btn")) {
  editModal.style.display = "none";

  //  تنظيف الخطأ عند الإغلاق
  editErrorMessage.textContent = "";
  editErrorMessage.classList.remove("show");
  editName.classList.remove("input-error");
}

});

// إغلاق عند الضغط خارج المودال
window.addEventListener("click", function(e) {
  if (e.target === editModal) {
    editModal.style.display = "none";

    // تنظيف الخطأ لما يقفل المودال بالضغط بره
    editErrorMessage.textContent = "";
    editErrorMessage.classList.remove("show");
    editName.classList.remove("input-error");
  }
});

// تحديث بيانات القسم
editForm.onsubmit = async function(e) {
  e.preventDefault();

  let nameValue = editName.value.trim();

  //  فاليديشن قبل الإرسال
  if (nameValue === "") {
    editErrorMessage.textContent = "Please enter the department name";
    editErrorMessage.classList.add("show");
    editName.classList.add("input-error");
    return;
  } else if (nameValue.length < 2) {
    editErrorMessage.textContent = "The department name must be at least two letters long.";
    editErrorMessage.classList.add("show");
    editName.classList.add("input-error");
    return;
  } else {
    editErrorMessage.textContent = "";
    editErrorMessage.classList.remove("show");
    editName.classList.remove("input-error");
  }

  let updatedData = {
    id: currentDeptId,
    name: nameValue,
    status: editStatus.value
  };

  try {
    const response = await fetch("../api/department/update.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(updatedData)
    });

    const data = await response.json();

    if (data.success) {
  //  عرض رسالة نجاح
showToast(data.message, "success");

  await loadDepartments();
  editModal.style.display = "none";

  //  تنظيف رسالة الخطأ لو فيه قديم
  editErrorMessage.textContent = "";
  editErrorMessage.classList.remove("show");
  editName.classList.remove("input-error");

} else {
  //  لو الاسم مكرر أو أي خطأ من الـ PHP
  editErrorMessage.textContent = data.message;
  editErrorMessage.classList.add("show");
  editName.classList.add("input-error");
}
  } catch (err) {
    console.error("Error updating department:", err);
    editErrorMessage.textContent = "Error connecting to server";
    editErrorMessage.classList.add("show");
    editName.classList.add("input-error");
  }
};

// ====== حذف قسم ======
const deleteModal = document.getElementById("deleteModal");
const confirmDeleteBtn = document.getElementById("confirmDelete");
const cancelDeleteBtn = document.getElementById("cancelDelete");
let deptIdToDelete = null;

// فتح المودال عند الضغط على Delete
document.addEventListener("click", function(e) {
  if (e.target.closest(".delete")) {
    e.preventDefault();
    let row = e.target.closest("tr");
    deptIdToDelete = parseInt(row.cells[0].textContent.trim(), 10);
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
  deptIdToDelete = null;
});

// تنفيذ الحذف
confirmDeleteBtn.addEventListener("click", async () => {
  if (!deptIdToDelete) return;

  try {
    const response = await fetch("../api/department/delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: deptIdToDelete })
    });

    const data = await response.json();

    if (data.success) {
      showToast(data.message, "success");
      await loadDepartments();
    } else {
      showToast(data.message, "error");
    }
  } catch (err) {
    console.error("Error deleting department:", err);
    showToast("Error connecting to server", "error");
  }

  deleteModal.style.display = "none";
  deptIdToDelete = null;
});

// إغلاق عند الضغط خارج المودال
window.addEventListener("click", function(e) {
  if (e.target === deleteModal) {
    deleteModal.style.display = "none";
    deptIdToDelete = null;
  }
});
