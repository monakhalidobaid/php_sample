<?php
include_once '../config/secure_page.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Manager</title>
<link rel="stylesheet" href="../assets/styles/pagination.css">    
<link rel="stylesheet" href="../assets/styles/department.css">    
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<link rel="stylesheet" href="../assets/styles/main.css">

</head>
<body class="department-page">
    <script>
  window.userType = '<?php echo $_SESSION['user_type'] ?? ''; ?>';
</script>
    <header class="header">
            <h1 class="header-title">Department</h1>
            <a href="../api/user/logout.php" class="logout-btn">Logout</a>
    </header>

    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">analytics</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li> 
            <?php if ($_SESSION['user_type'] === 'admin'): ?>
                 <li class="menu-item">
                    <a href="user.php" class="nav-link">
                    <span class="material-symbols-outlined">person_apron</span> 
                    <span class="nav-label">Users</span>
                    </a>
                </li>                 
            <?php endif; ?>

                <li class="menu-item">
                    <a href="department.php" class="nav-link">
                    <span class="material-symbols-outlined">corporate_fare</span> 
                    <span class="nav-label">Departments</span>
                    </a>
                </li>                 

            <?php if ($_SESSION['user_type'] === 'admin'): ?>                
                <li class="menu-item">
                    <a href="employees.php" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">badge</span>
                    <span class="nav-label">Employees</span>
                    </a>
                </li> 
            <?php endif; ?>
                                <li class="menu-item">

                    <a href="categories.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">category</span>
                        <span class="nav-label">Categories</span>
                    </a>
                </li>  
                
                <li class="menu-item">
                    <a href="item.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">list_alt</span>
                        <span class="nav-label">Items</span>
                    </a>
                </li> 

            <?php if ($_SESSION['user_type'] === 'admin'): ?>                
                <li class="menu-item">
                    <a href="add_item.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">list_alt_add</span>
                        <span class="nav-label">Add Item</span>
                    </a>
                </li> 
            <?php endif; ?>

            <?php if ($_SESSION['user_type'] === 'admin'): ?>   
                <li class="menu-item">
                    <a href="transaction.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">flowsheet</span>
                        <span class="nav-label">Transaction</span>
                    </a>
                </li> 
                            <?php endif; ?>


                <li class="menu-item">
                    <a href="report.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">content_paste</span>
                        <span class="nav-label">Reports</span>
                    </a>
                </li> 

            </ul> 
        </nav>        
    </aside>
    <main class="main-content">
            <?php if ($_SESSION['user_type'] === 'admin'): ?>

        <section class="add-department-section">
            <form id="deptrForm" method="post">
            <div class="add-department-form">
                <h3 class="form-header">Add new Department</h3>
                <hr style="border: 1px solid rgb(241, 234, 234);">
                <div class="form-group">
                    <label for="dept-name" style="font-weight: 500;">Department Name</label>
                    <br>
                    <input id="dept-name" name="dept-name" type="text" placeholder="Enter department name" required>
                    <span class="error-message"></span> 
                </div>
                <div class="form-buttons">
                    <input type="submit"  class="btn-add" value="Add">
                    <input  type = "reset" class="btn-cancel" value="Reset">
                    
                </div>
                            </div>
            </form>
                <p id="result"></p>

        </section>
            <?php endif; ?>

            <section class="departments-list-section">
            <div class="section-header">
                <h3 class="section-title">Departments</h3>
                <div class="search-container">
                <input type="search" placeholder="Search" class="search-input">
                </div>
            </div>

            <table class="transactions-table"  id="deptTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                     <!-- البيانات ستضاف هنا -->
                </tbody>
            </table>
            <div id="pagination" class="pagination">

            </div>
            </section>
    </main>

<!-- Edit Department Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h3 class="form-header">Edit Department</h3>
    <hr style="border: 1px solid rgb(241, 234, 234);">

    <form id="editDeptForm">
      <div class="form-group">
        <label for="edit-dept-name">Department Name</label>
        <input type="text" id="edit-dept-name" name="edit-dept-name" required>
          <span class="error-message" id="edit-error-message"></span>

      </div>

      <div class="form-group">
        <label for="edit-dept-status">Department Status</label>
        <select id="edit-dept-status" name="edit-dept-status">
          <option value="active">Active</option>
          <option value="disabled">Disabled</option>
        </select>
      </div>

      <div class="form-buttons">
        <button type="submit" class="btn-add">Edit</button>
      </div>
    </form>
  </div>
</div>

<!-- رسالة نجاح عملية التحديث -->
<div id="toast" class="toast"></div>


<!-- Delete Department Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3 class="form-header">⚠️ Delete Department</h3>
    <hr style="border: 1px solid rgb(241, 234, 234);">
    <p>Are you sure you want to delete this department? 
       <br>This action is irreversible and the data cannot be recovered.</p>
    <div class="form-buttons">
      <button id="confirmDelete" class="btn-add">Delete</button>
      <button id="cancelDelete" class="btn-cancel">Cancel</button>
    </div>
  </div>
</div>
    <script>
  window.addEventListener("DOMContentLoaded", () => {
    document.body.classList.add("fade-in");
  });
</script>



<script type="module" src="../js/fetch.js"></script>
<script src="../js/helper.js"></script>


</body>
</html>