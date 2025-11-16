const tbody = document.querySelector('#itemTable tbody');
const limit = 5;

// ==== Render Transaction ====
function displayTransaction(items) {
    tbody.innerHTML = '';
    if (items.length === 0) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 5;
        td.textContent = 'No transactions found';
        td.style.textAlign = 'center';
        tr.appendChild(td);
        tbody.appendChild(tr);
        return;
    }

    items.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.trans_id}</td>
            <td>${item.item_id}</td>
            <td>${item.trans_type}</td>
            <td>${item.trans_date}</td>
            <td>${item.created_by}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ==== Fetch Transaction ====
async function fetchTransaction() {
    try {
        const res = await fetch(`/inventory_manager/api/transaction/get_transaction.php?limit=${limit}`);
        const data = await res.json();
        if (data.success) {
            displayTransaction(data.item);
        } else {
            displayTransaction([]);
        }
    } catch (err) {
        console.error(err);
        alert("Error fetching transaction list");
    }
}

// ==== Fetch Dashboard Stats ====
async function fetchDashboardStats() {
    try {
        const res = await fetch('/inventory_manager/api/item/get_dashboard_stats.php');
        const data = await res.json();

        if (data.success) {
            document.getElementById('totalItems').textContent = data.data.total_items;
            document.getElementById('activeItems').textContent = data.data.active_items;
        } else {
            document.getElementById('totalItems').textContent = '0';
            document.getElementById('activeItems').textContent = '0';
        }
    } catch (err) {
        console.error('Error fetching stats:', err);
        document.getElementById('totalItems').textContent = '—';
        document.getElementById('activeItems').textContent = '—';
    }
}

// ==== Fetch Items by Status ====
async function fetchItemsByStatus() {
    try {
        const res = await fetch('/inventory_manager/api/item/get_items_by_status.php');
        const data = await res.json();

        const container = document.getElementById('itemsByStatus');
        container.innerHTML = '';

        if (data.success && data.data.length > 0) {
            data.data.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('detail-item');

                // تحديد لون البادج بناءً على الحالة
                let badgeClass = '';
                switch (item.status.toLowerCase()) {
                    case 'active':
                        badgeClass = 'badge-active';
                        break;
                    case 'standby':
                        badgeClass = 'badge-standby';
                        break;
                    case 'in maintenance':
                        badgeClass = 'badge-maintenance';
                        break;
                    case 'out of service':
                        badgeClass = 'badge-out-of-service';
                        break;
                    default:
                        badgeClass = 'badge-standby';
                }

                li.innerHTML = `
                    <span class="item-name">${item.status}</span>
                    <span class="item-badge ${badgeClass}">${item.total}</span>
                `;
                container.appendChild(li);
            });
        } else {
            container.innerHTML = '<li class="detail-item"><span class="item-name">No data found</span></li>';
        }
    } catch (err) {
        console.error('Error fetching items by status:', err);
    }
}

// ==== Fetch Items per Department ====
async function fetchItemsPerDepartment() {
    try {
        const res = await fetch('/inventory_manager/api/item/get_items_per_department.php');
        const data = await res.json();

        const container = document.getElementById('itemsPerDepartment');
        container.innerHTML = '';

        if (data.success && data.data.length > 0) {
            data.data.forEach(dep => {
                const li = document.createElement('li');
                li.classList.add('detail-item');

                li.innerHTML = `
                    <span class="item-name">${dep.department_name}</span>
                    <span class="item-value">${dep.total_items}</span>
                `;
                container.appendChild(li);
            });
        } else {
            container.innerHTML = '<li class="detail-item"><span class="item-name">No data found</span></li>';
        }
    } catch (err) {
        console.error('Error fetching items per department:', err);
    }
}

// ==== Initialize ====
window.addEventListener("DOMContentLoaded", () => {
    fetchTransaction();
    fetchDashboardStats();
    fetchItemsByStatus();
    fetchItemsPerDepartment();
});