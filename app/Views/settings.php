<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Quality Monitoring System</title>
    
    <!-- Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* NAVBAR STYLES (VERSI ASLI) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: #1e293b;
            font-weight: 700;
            line-height: 1.2;
        }

        .side-nav {
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link.active {
            color: #6366f1;
        }

        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 1.25rem;
            padding: 0.75rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem;
            color: #64748b;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-item .material-icons {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .nav-text {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .nav-item.active {
            color: #6366f1;
        }

        @media (max-width: 768px) {
            .side-nav {
                display: none;
            }
            .mobile-nav {
                display: block;
            }
            body {
                padding-bottom: 6rem;
            }
        }

        /* CONTENT STYLES */
        .content-section {
            display: none;
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
        }

        .content-section.active {
            display: block;
        }

        /* DASHBOARD STYLES */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* SETTINGS STYLES */
        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            gap: 2rem;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* ... (Tambahkan style lainnya sesuai kebutuhan) ... */
    </style>
</head>
<body>
    <!-- Mobile Navbar -->
    <div class="mobile-nav">
        <div class="nav-items">
            <a href="#dashboard" class="nav-item">
                <span class="material-icons">dashboard</span>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="#analytics" class="nav-item">
                <span class="material-icons">trending_up</span>
                <span class="nav-text">Analytics</span>
            </a>
            <a href="#alerts" class="nav-item">
                <span class="material-icons">notifications</span>
                <span class="nav-text">Alerts</span>
            </a>
            <a href="#settings" class="nav-item active">
                <span class="material-icons">settings</span>
                <span class="nav-text">Settings</span>
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Desktop Navbar -->
        <div class="header">
            <div class="header-left">
                <h1 class="title">Air Quality Monitoring</h1>
            </div>
            <nav class="side-nav">
                <a href="#dashboard" class="nav-link">
                    <span class="material-icons">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="#analytics" class="nav-link">
                    <span class="material-icons">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="#alerts" class="nav-link">
                    <span class="material-icons">notifications</span>
                    <span>Alerts</span>
                </a>
                <a href="#settings" class="nav-link active">
                    <span class="material-icons">settings</span>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboard" class="content-section active">
            <!-- Konten dashboard -->
            <div class="stats-grid">
                <!-- Stat cards -->
            </div>
        </div>

        <!-- Settings Content -->
        <div id="settings" class="content-section">
            <div class="settings-container">
                <!-- Konten settings -->
            </div>
        </div>
    </div>

    <script>
    // Navigation Controller
    function switchContent(target) {
        $('.content-section').removeClass('active');
        $(target).addClass('active');
        $('.nav-link, .nav-item').removeClass('active');
        $(`[href="${target}"]`).addClass('active');
        
        if(target === '#dashboard') {
            initCharts();
            startMonitoring();
        }
    }

    // Chart Initialization
    function initCharts() {
        Highcharts.chart('chart-container', {
            // Konfigurasi chart
        });
    }

    // Data Monitoring
    let monitoringInterval;
    function startMonitoring() {
        monitoringInterval = setInterval(fetchData, 5000);
    }

    async function fetchData() {
        try {
            const response = await fetch('/api/data');
            const data = await response.json();
            updateDashboard(data);
        } catch (error) {
            handleError(error);
        }
    }

    // Settings Functions
    function initSettings() {
        $('.toggle-input').on('change', function() {
            $(this).closest('.form-group').toggleClass('active', this.checked);
        });

        $('.btn-primary').on('click', function(e) {
            e.preventDefault();
            saveSettings();
        });
    }

    async function saveSettings() {
        const formData = {
            // Ambil data dari form
        };
        
        try {
            const response = await fetch('/api/settings', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
            alert('Settings saved successfully!');
        } catch (error) {
            alert('Error saving settings!');
        }
    }

    // Initialization
    $(document).ready(function() {
        initCharts();
        initSettings();
        startMonitoring();
        
        $('.nav-link, .nav-item').on('click', function(e) {
            e.preventDefault();
            switchContent($(this).attr('href'));
        });
    });
    </script>
</body>
</html>