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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/styles/main.css">
    <link rel="stylesheet" href="../assets/styles/user.css">

</head>
<body class="department-page">
        <script>
            window.userType = '<?php echo $_SESSION['user_type'] ?? ''; ?>';
        </script>
    <header class="header">
            <h1 class="header-title">Users</h1>
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
<section class="add-user-section">
    <form id="userForm" method="post">
        <div class="add-user-form">
            <h3 class="form-header">Add New User</h3>
            <hr style="border: 1px solid rgb(241, 234, 234);">

            <!-- Username -->
            <div class="form-group">
                <label for="username" style="font-weight: 500;">Username</label>
                <input id="username" name="username" type="text" placeholder="Enter Username" required>
                <span class="error-message"></span>
            </div>

            <!-- UID -->
            <div class="form-group">
                <label for="uid" style="font-weight: 500;">UID</label>
                <input id="uid" name="uid" type="text" placeholder="Enter User Id" required>
                <span class="error-message"></span>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" style="font-weight: 500;">Password</label>
                <input id="password" name="password" type="password" placeholder="Enter User Password" required>
                <span class="error-message"></span>
            </div>

            <!-- User Type -->
            <div class="form-group">
                <label for="usertype" style="font-weight: 500;">User Type</label>
                <select id="usertype" name="usertype" required>
                    <option value="">-- Select User Type --</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <span class="error-message"></span>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" style="font-weight: 500;">Email</label>
                <input id="email" name="email" type="email" placeholder="Enter User Email" required>
                <span class="error-message"></span>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label for="phone" style="font-weight: 500;">Phone Number</label>
                <input id="phone" name="phone" type="tel" placeholder="Enter Phone Number" required>
                <span class="error-message"></span>
            </div>

            <!-- Buttons -->
            <div class="form-buttons">
                <input type="submit" class="btn-add" value="Add">
                <input type="reset" class="btn-cancel" value="Reset">
            </div>
        </div>
    </form>
    <p id="result"></p>
</section>
            <section class="departments-list-section">
            <div class="section-header">
                <h3 class="section-title">Users</h3>
                <div class="search-container">
                <input type="search" placeholder="Search" class="search-input" id="searchInput">
                </div>
            </div>

            <table class="transactions-table"  id="userTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>UID</th>
                    <th>User Type</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                     <!-- البيانات ستضاف هنا -->
                </tbody>
            </table>
                <div id="pagination"></div>

            </section>
    </main>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h3 class="form-header">Edit User</h3>
    <hr style="border: 1px solid rgb(241, 234, 234);">

    <form id="editUserForm">
      <div class="form-group">
        <label for="editUsername">Username</label>
        <input type="text" id="editUsername" name="editUsername" required>
        <span class="error-message"></span>
      </div>

    <div class="form-group">
        <label for="editUid">UID</label>
        <input type="text" id="editUid" name="editUid" required>
        <span class="error-message"></span>
      </div>

      <div class="form-group">
        <label for="editUserType">User Type</label>
        <select id="editUserType" name="editUserType">
            <option value="">-- Select User Type --</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
      </div>

      <div class="form-group">
        <label for="editEmail">Email</label>
        <input type="text" id="editEmail" name="editEmail" required>
        <span class="error-message"></span>
      </div>

      
      <div class="form-group">
        <label for="editPhone">Phone</label>
        <input type="text" id="editPhone" name="editPhone" required>
        <span class="error-message"></span>
      </div>

      <div class="form-buttons">
        <button type="submit" class="btn-add">Edit</button>
      </div>
    </form>
  </div>
</div>


<!--  رسالة نجاح عملية التحديث او الحذف-->
<div id="toast" class="toast"></div>


<!-- Delete User Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3 class="form-header">⚠️ Delete User</h3>
    <hr style="border: 1px solid rgb(241, 234, 234);">
    <p>Are you sure you want to delete this user? 
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
<script type="module" src="../js/user.js"></script>
<script src="../js/helper.js"></script>

</body>
</html>