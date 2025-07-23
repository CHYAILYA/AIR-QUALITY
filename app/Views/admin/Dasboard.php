<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --background: #f9fafb;
        }

        body {
            background: linear-gradient(135deg, #f6f7ff 0%, #e9ecff 100%);
            transition: margin-left 0.3s;
            font-family: 'Arial', sans-serif;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
            background: transparent;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            color: #1f2937;
            padding: 20px;
            position: fixed;
            height: 100%;
            transition: 0.3s;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li a {
            color: #4b5563;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
            margin-bottom: 5px;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 30px;
            background: transparent;
            transition: margin-left 0.3s;
        }

        .main-content.shifted {
            margin-left: 0;
            width: 100%;
        }

        /* Button to Toggle Sidebar */
        .toggle-sidebar-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            z-index: 9999;
            font-size: 20px;
            cursor: pointer;
        }
        
        /* Styles for responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar.hidden {
                transform: translateX(-100%);
            }
        }

        /* Modern Cards */
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Adjustments for smaller screens */
        @media (max-width: 576px) {
            .stat-card {
                margin-bottom: 20px;
            }

            .main-content {
                padding: 15px;
            }

            .toggle-sidebar-btn {
                top: 15px;
                left: 15px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-fw fa-tachometer-alt"></i> Admin Panel</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="<?= site_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <button class="toggle-sidebar-btn" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Top Nav -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Dashboard Overview</h2>
                <div class="d-flex gap-3">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search...">
                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Admin
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-primary text-white p-4">
                        <h5>Total Users</h5>
                        <h2>1,234</h2>
                        <small><i class="fas fa-arrow-up"></i> 12% from last month</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-success text-white p-4">
                        <h5>Revenue</h5>
                        <h2>$45,678</h2>
                        <small><i class="fas fa-arrow-up"></i> 8.5% from last month</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-warning text-dark p-4">
                        <h5>Pending Orders</h5>
                        <h2>56</h2>
                        <small><i class="fas fa-arrow-down"></i> 3% from last month</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card bg-info text-white p-4">
                        <h5>Support Tickets</h5>
                        <h2>15</h2>
                        <small><i class="fas fa-arrow-up"></i> 2 new today</small>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="chart-container">
                <canvas id="mainChart"></canvas>
            </div>

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" id="ordersTable">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample Data -->
                                    <tr>
                                        <td>#1234</td>
                                        <td>John Doe</td>
                                        <td>$120</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Add more rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group" id="notifications-list">
                                <!-- Notifications will be populated here via Ajax -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle Sidebar
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('shifted');
        });
        
        // Fetch notifications using Ajax
        fetch('<?= site_url('admin/getNotifications') ?>')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notifications-list');
                notificationsList.innerHTML = '';

                if (data.length === 0) {
                    notificationsList.innerHTML = '<p>No notifications available.</p>';
                    return;
                }

                data.forEach(notification => {
                    const notificationItem = document.createElement('a');
                    notificationItem.classList.add('list-group-item', 'list-group-item-action');
                    notificationItem.href = '#';

                    const respon = notification.respon;
                    const sentText = respon.sent_text;
                    const parsedText = parseSentText(sentText);

                    const timestamp = new Date(notification.tgl);
                    const formattedDate = timestamp.toLocaleString('en-US', {
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        hour12: true
                    });

                    notificationItem.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>Status:</strong> ${parsedText.status}<br>
                                <strong>Message:</strong> ${parsedText.message}<br>
                                <strong>Sent Text:</strong> ${parsedText.sentText}
                            </div>
                            <div><small>${formattedDate}</small></div>
                        </div>
                    `;
                    notificationsList.appendChild(notificationItem);
                });
            })
            .catch(error => console.error('Error fetching notifications:', error));

        // Helper function to parse the 'sent_text' and make it readable
        function parseSentText(sentText) {
            const regex = /Label: (.*?)\nStatus: (.*?)\nValue: (.*?)\nTimestamp \(WIB\): (.*?)/;
            const match = sentText.match(regex);

            if (match) {
                return {
                    label: match[1],
                    status: match[2],
                    value: match[3],
                    timestampWIB: match[4],
                    sentText: `Label: ${match[1]}, Status: ${match[2]}, Value: ${match[3]}, Timestamp (WIB): ${match[4]}`
                };
            } else {
                return {
                    status: 'Unknown',
                    message: 'No message found',
                    sentText: sentText
                };
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Initialize Chart
        const ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return '$' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Initialize DataTables
        $(document).ready(function() {
            $('#ordersTable').DataTable();
        });
    </script>
</body>
</html>
