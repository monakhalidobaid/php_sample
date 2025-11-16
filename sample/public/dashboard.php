<?php

include_once '../config/secure_page.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Manager</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
    <link rel="stylesheet" href="../assets/styles/main.css">
    
</head>
<body class="dashboard-page">
    <header class="header">
        <h1 class="header-title">Dashboard</h1>
        <a href="../api/user/logout.php" class="logout-btn">Logout</a>
    </header>

    <aside class="sidebar">
        <!-- نفس الـ sidebar الحالي - ما تغير شي -->
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
        <!-- البطاقات الإحصائية الجديدة -->
        <section class="stats-cards-grid">
            <!-- Total Items Card -->
            <article class="stat-card blue">
                <div class="card-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <div class="card-info">
                    <div class="card-title">Total Items</div>
                    <div class="card-value" id="totalItems">0</div>
                </div>
            </article>

            <!-- Active Items Card -->
            <article class="stat-card green">
                <div class="card-icon">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div class="card-info">
                    <div class="card-title">Active Items</div>
                    <div class="card-value" id="activeItems">0</div>
                </div>
            </article>
        </section>

        <!-- البطاقات التفصيلية -->
        <section class="detailed-cards-section">
            <!-- Items by Status -->
            <article class="detail-card">
                <div class="detail-header">
                    <div class="detail-icon">
                        <span class="material-symbols-outlined">analytics</span>
                    </div>
                    <h3 class="detail-title">Items by Status</h3>
                </div>
                <ul class="detail-list" id="itemsByStatus">
                    <li class="detail-item">
                        <span class="item-name">Loading...</span>
                    </li>
                </ul>
            </article>

            <!-- Items per Department -->
            <article class="detail-card">
                <div class="detail-header">
                    <div class="detail-icon green-gradient">
                        <span class="material-symbols-outlined">pie_chart</span>
                    </div>
                    <h3 class="detail-title">Items per Department</h3>
                </div>
                <ul class="detail-list" id="itemsPerDepartment">
                    <li class="detail-item">
                        <span class="item-name">Loading...</span>
                    </li>
                </ul>
            </article>
        </section>

        <!-- Recent Transactions - نفس التصميم القديم -->
        <section class="recent-transactions">
            <h3 class="section-title">Recent Transaction</h3>
            <table class="transactions-table" border="1" id="itemTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item ID</th>
                        <th>Transaction Type</th>
                        <th>Transaction Date</th>
                        <th>Created by</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- البيانات ستضاف هنا -->
                </tbody>
            </table>
        </section>
    </main>

    <script>
        window.addEventListener("DOMContentLoaded", () => {
            document.body.classList.add("fade-in");
        });
    </script>
    <script src="../js/dashboard.js"></script>
    <script src="../js/helper.js"></script>
</body>
</html>
