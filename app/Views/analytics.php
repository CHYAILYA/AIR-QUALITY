<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <title>Air Quality Dashboard</title>
    
    <!-- Required libraries -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --glass: rgba(255, 255, 255, 0.9);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            min-height: 100vh;
            padding: 1rem;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-left {
            display: block;
        }

        .title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: var(--text-primary);
            font-weight: 700;
            line-height: 1.2;
            margin-right: 2rem;
        }

        .side-nav {
            display: none;
            gap: 1.5rem;
            position: absolute;
            right: 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link.active {
            color: var(--primary);
        }

        .nav-link:hover {
            color: var(--primary);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin: 1rem 0.5rem;
        }

        @media screen and (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                padding: 0 0.5rem;
            }

            .stat-card {
                min-height: 50px; /* Reduced from 180px */
                padding: 0.75rem; /* Reduced padding */
            }

            .stat-value {
                font-size: 1.75rem; /* Slightly smaller font */
                margin: 0.25rem 0; /* Reduced margin */
            }

            .ispu-category {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .fuzzy-details {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media screen and (max-width: 480px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .stat-card {
                min-height: 50px; /* Even smaller height */
                padding: 0.5rem; Smaller padding
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }

        .stat-card {
            background: var(--glass);
            padding: 1.25rem;
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 200px;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .stat-icon {
            font-size: 1.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
            margin: 0.5rem 0;
        }

        .ispu-category {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .fuzzy-details {
            margin-top: auto;
            font-size: 0.9rem;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.5);
            padding: 0.75rem;
            border-radius: 0.5rem;
            word-break: break-word;
        }

        /* Media Queries */
        @media screen and (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media screen and (max-width: 768px) {
            .stats-grid {
                gap: 1rem;
                margin: 0.75rem 0.25rem;
            }

            .stat-card {
                padding: 1rem;
                min-height: 180px;
            }

            .stat-value {
                font-size: 2rem;
            }
        }

        @media screen and (max-width: 480px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .stat-card {
                min-height: 160px;
            }

            .fuzzy-details {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
        }

        @media (hover: none) {
            .stat-card:hover,
            .status-item:hover {
                transform: none;
            }
        }

        /* Main Content Grid */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem; /* Add margin top */
        }

        /* Chart Container */
        .chart-container {
            background: var(--glass);
            padding: 1.5rem;
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            min-height: 400px;
        }

        .chart-container canvas {
            width: 100% !important;
            height: auto !important;
        }

        /* Status List */
        .status-list {
            background: var(--glass);
            padding: 1.5rem;
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 2rem; /* Add margin bottom */
        }

        .status-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.6);
            transition: all 0.2s ease;
        }

        .status-item:not(:last-child) {
            margin-bottom: 1rem; /* Add more space between status items */
        }

        .status-item:hover {
            transform: translateX(5px);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 1rem;
            position: relative;
        }

        .online {
            background: var(--success);
            animation: pulse 1.5s infinite;
        }

        .warning {
            background: var(--warning);
        }

        .critical {
            background: var(--danger);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* Mobile Navbar */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 400px;
            background: var(--glass);
            backdrop-filter: blur(12px);
            padding: 0.75rem;
            border-radius: 1.25rem;
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
            color: var(--text-secondary);
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

        .nav-item.active .material-icons,
        .nav-item.active .nav-text {
            color: var(--primary);
        }

        @media screen and (min-width: 769px) {
            .side-nav {
                display: flex;
            }
            
            .mobile-nav {
                display: none;
            }
        }

        @media screen and (max-width: 768px) {
            .side-nav {
                display: none;
            }
            .mobile-nav {
                display: block;
            }
            .data-table {
                font-size: 0.9rem;
            }
            .chart-container {
                min-height: 300px;
            }
            body {
                padding: 0.5rem;
                padding-bottom: 5rem;
            }

            .header {
                margin-bottom: 1rem;
                padding: 0.75rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                padding: 0 0.5rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .grid-container {
                margin-top: 0.75rem;
                gap: 1rem;
            }

            .status-list {
                margin-bottom: 1.5rem; /* Slightly smaller margin on mobile */
            }
        }

        @media screen and (max-width: 480px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .status-item {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            .chart-container {
                padding: 1rem;
                min-height: 300px;
            }

            /* .fuzzy-details {
                grid-template-columns: 1fr;
                text-align: left;
            } */
            .fuzzy-details {
    display: flex;
    gap: 0.3rem;
    margin-top: 0.3rem;
    height: 24px; /* Fixed height */
    overflow: hidden;
}

.fuzzy-details > div {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 0.3rem;
    padding: 0.1rem 0.3rem;
    font-size: 0.7rem;
    white-space: nowrap;
    display: flex;
    align-items: center;
    line-height: 1;
    min-width: 55px;
    height: 20px;
    border: 1px solid rgba(0,0,0,0.1);
}

.fuzzy-details span {
    font-weight: 600;
    color: var(--text-primary);
    margin-right: 0.1rem;
}
        }

        @media (hover: none) {
            .stat-card:hover,
            .status-item:hover {
                transform: none;
            }
        }

        /* Add ISPU specific styles */
        .ispu-value {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
        }
        
        .ispu-category {
            text-align: center;
            font-size: 1.2rem;
            color: var(--text-primary);
        }

        /* Updated Table Styles */
        .table-responsive-wrapper {
            background: var(--glass);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
            background: transparent;
        }

        .data-table thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .data-table th {
            background: rgba(99, 102, 241, 0.1);
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.95rem;
            border-bottom: 2px solid rgba(99, 102, 241, 0.2);
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.95rem;
            transition: background-color 0.2s ease;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.1);
            cursor: default;
        }

        .data-table td:first-child {
            font-weight: 500;
            color: var(--text-primary);
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .table-responsive-wrapper {
                padding: 1rem;
                margin-top: 1rem;
                border-radius: 0.75rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .data-table th {
                font-weight: 600;
            }
        }

        @media screen and (max-width: 480px) {
            .table-responsive-wrapper {
                padding: 0.75rem;
                margin-top: 0.75rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
        }

        /* Add this to your existing <style> section */
        .fuzzy-details {
            margin-top: 1rem; /* Add more space above fuzzy details */
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            font-size: 0.9rem;
        }

        .fuzzy-details div {
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 0.5rem;
            text-align: center;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: fadeIn 0.3s ease-out;
        }

        .notification.error {
            background: var(--danger);
            color: white;
        }

        .notification .close {
            cursor: pointer;
            font-size: 1.2rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-controls select {
            background: var(--glass);
            backdrop-filter: blur(12px);
            color: var(--text-primary);
            font-size: 0.9rem;
            min-width: 150px;
        }

        .chart-controls select:hover {
            border-color: var(--primary);
        }

        @media screen and (max-width: 768px) {
            .chart-controls {
                padding: 0 0.5rem;
            }
            .chart-controls select {
                flex: 1;
                min-width: 120px;
            }
        }

        /* Add to your existing <style> section */
        .recommendation-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .recommendation-header {
            margin-bottom: 0.5rem;
        }

        .recommendation-text {
            line-height: 1.5;
            color: var(--text-primary);
        }

        .news-links {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .news-item {
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: transform 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 600px; /* Add max-width for desktop */
            margin: 0 auto; /* Center the container */
        }

        .news-image {
            position: relative;
            width: 100%;
            padding-top: 45%; /* Reduced height ratio for desktop */
            overflow: hidden;
        }

        .news-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .news-content {
            padding: 1rem;
            flex: 1;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .news-item {
                max-width: 100%; /* Full width on tablets */
            }
            
            .news-image {
                padding-top: 56.25%; /* Standard 16:9 ratio for tablets */
            }
        }

        @media screen and (max-width: 480px) {
            .news-image {
                padding-top: 56.25%; /* Maintain 16:9 ratio for mobile */
            }
            
            .news-content {
                padding: 0.75rem;
            }
        }

        /* Image hover effects */
        .news-item:hover .news-image img {
            transform: scale(1.05);
        }

        /* Loading state */
        .news-image.loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <div class="mobile-nav">
        <div class="nav-items">
            <a href="<?= base_url('') ?>" class="nav-item">
                <span class="material-icons">dashboard</span>
                <span class="nav-text">Home</span>
            </a>
            <a href="<?= base_url('analytics') ?>" class="nav-item active">
                <span class="material-icons">trending_up</span>
                <span class="nav-text">Stats</span>
            </a>
            <a href="<?= base_url('alerts') ?>" class="nav-item">
                <span class="material-icons">notifications</span>
                <span class="nav-text">Alerts</span>
            </a>
            <a href="<?= base_url('settings') ?>" class="nav-item">
                <span class="material-icons">settings</span>
                <span class="nav-text">Settings</span>
            </a>
        </div>
    </div>
    <div class="dashboard-container">
        <div class="header">
            <div class="header-left">
                <h1 class="title">Air Quality Monitoring</h1>
            </div>
            <nav class="side-nav">
                <a href="<?= base_url('') ?>" class="nav-link">
                    <span class="material-icons">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('analytics') ?>" class="nav-link active">
                    <span class="material-icons">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="<?= base_url('settings') ?>" class="nav-link">
                    <span class="material-icons">settings</span>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- ISPU Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">PM2.5</span>
                    <span class="material-icons stat-icon">air</span>
                </div>
                <div id="pm25-value" class="stat-value">-</div>
                <div class="ispu-category">μg/m³</div>
                <div id="pm25-fuzzy" class="fuzzy-details"></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">CO</span>
                    <span class="material-icons stat-icon">air</span>
                </div>
                <div id="co-value" class="stat-value">-</div>
                <div class="ispu-category">ppm</div>
                <div id="co-fuzzy" class="fuzzy-details"></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">NO2</span>
                    <span class="material-icons stat-icon">air</span>
                </div>
                <div id="no2-value" class="stat-value">-</div>
                <div class="ispu-category">ppm</div>
                <div id="no2-fuzzy" class="fuzzy-details"></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">O3</span>
                    <span class="material-icons stat-icon">air</span>
                </div>
                <div id="o3-value" class="stat-value">-</div>
                <div class="ispu-category">ppm</div>
                <div id="o3-fuzzy" class="fuzzy-details"></div>
            </div>
        </div>

        <!-- Real-time Air Quality Status -->
        <div class="status-list">
            <h2>Real-time Air Quality Status</h2>
            <div class="status-item loading-status">
                <div class="status-indicator warning"></div>
                <div>
                    <strong>Status: </strong>
                    <span>Connecting to sensor...</span>
                </div>
            </div>
            <div class="status-item">
                <div class="status-indicator online"></div>
                <div>
                    <strong>Current Status: </strong>
                    <span class="ispu-status">-</span>
                    (<span class="ispu-confidence">-</span> confidence)
                </div>
            </div>
            <div class="status-item">
                <div>
                    <strong>Recommendation: </strong>
                    <span class="recommendation-text">-</span>
                </div>
            </div>
            <div class="status-item">
                <div>
                    <strong>Fuzzy Values:</strong>
                    <div class="fuzzy-details">
                        <div>Baik: <span id="baik-percent">-</span>%</div>
                        <div>Sedang: <span id="sedang-percent">-</span>%</div>
                        <div>Buruk: <span id="buruk-percent">-</span>%</div>
                    </div>
                </div>
            </div>
            <div class="status-item">
                <div>
                    <strong>Related News:</strong>
                    <div class="news-links"></div>
                </div>
            </div>
            <div class="status-item recommendation-container">
                <div class="recommendation-header">
                    <!-- <strong>Recommendation: </strong> -->
                    <!-- <span class="recommendation-text">-</span> -->
                </div>
              
            </div>
        </div>

        <!-- Chart Controls -->
        <div class="chart-controls" style="margin: 1rem 0; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select id="parameterSelect" style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                <option value="all">Semua Parameter</option>
                <option value="PM2.5">PM2.5</option>
                <option value="CO">CO</option>
                <option value="NO2">NO2</option>
                <option value="O3">O3</option>
            </select>
            <select id="timeRange" style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1);">
                <option value="1">1 Jam Terakhir</option>
                <option value="3">3 Jam Terakhir</option>
                <option value="6">6 Jam Terakhir</option>
                <option value="12">12 Jam Terakhir</option>
                <option value="24">24 Jam Terakhir</option>
            </select>
        </div>

        <!-- Charts Grid -->
        <div class="grid-container">
            <div class="chart-container">
                <div id="current_data_chart"></div>
            </div>
            <div class="chart-container">
                <div id="history_chart"></div>
            </div>
        </div>

        <!-- Updated Current Readings Table -->
        <div class="table-responsive-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>PM2.5</th>
                        <th>CO</th>
                        <th>NO2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($current_data as $row): ?>
                    <tr>
                        <td><?= date('H:i:s', strtotime($row->timestamp)) ?></td>
                        <td><?= number_format($row->sharp, 1) ?></td>
                        <td><?= number_format($row->mq7, 1) ?></td>
                        <td><?= number_format($row->mq135, 1) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    const MAX_RETRIES = 3;
    let retryCount = 0;
    let currentDataChart, historyChart;

    function initCharts() {
        currentDataChart = Highcharts.chart('current_data_chart', {
            title: { text: 'Riwayat AQI' },
            xAxis: { 
            type: 'datetime',
            tickPixelInterval: 150
            },
            yAxis: { 
            title: { text: 'Nilai' },
            plotLines: [{ value: 0, width: 1, color: '#808080' }]
            },
            tooltip: {
            formatter: function() {
                return `<b>${this.series.name}</b><br>
                ${Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x)}<br>
                ${Highcharts.numberFormat(this.y, 2)}`;
            }
            },
            series: [
            { 
                name: 'PM2.5', 
                data: <?= json_encode(array_map(function($row) { 
                return [strtotime($row->tgl) * 1000, floatval($row->conc_pm25)];
                }, array_reverse($history2))) ?> 
            },
            { 
                name: 'CO', 
                data: <?= json_encode(array_map(function($row) {
                return [strtotime($row->tgl) * 1000, floatval($row->conc_co)];
                }, array_reverse($history2))) ?> 
            },
            { 
                name: 'NO2', 
                data: <?= json_encode(array_map(function($row) {
                return [strtotime($row->tgl) * 1000, floatval($row->conc_no2)];
                }, array_reverse($history2))) ?> 
            },
            { 
                name: 'O3', 
                data: <?= json_encode(array_map(function($row) {
                return [strtotime($row->tgl) * 1000, floatval($row->conc_o3)];
                }, array_reverse($history2))) ?> 
            }
            ]
        });

        historyChart = Highcharts.chart('history_chart', {
            title: { text: 'Riwayat ISPU' },
            xAxis: { 
                categories: <?= json_encode(array_map(function($row) { return $row->tgl; }, $history)) ?>
            },
            yAxis: { title: { text: 'Nilai ISPU' } },
            series: [
                { name: 'PM2.5', data: <?= json_encode(array_map(function($row) { return floatval($row->ispu_pm25); }, $history)) ?> },
                { name: 'CO', data: <?= json_encode(array_map(function($row) { return floatval($row->ispu_co); }, $history)) ?> },
                { name: 'CO2', data: <?= json_encode(array_map(function($row) { return floatval($row->ispu_no2); }, $history)) ?> },
                { name: 'Rata-rata', data: <?= json_encode(array_map(function($row) { return floatval($row->ispu_udara); }, $history)) ?> }
            ]
        });
    }

    function initChartFilters() {
        // Existing parameter filter for current data chart
        $('#parameterSelect').on('change', function() {
            const selected = $(this).val();
            
            // Filter current data chart
            currentDataChart.series.forEach(series => {
                if (selected === 'all') {
                    series.show();
                } else {
                    series.setVisible(series.name === selected, false);
                }
            });
            currentDataChart.redraw();

            // Filter history chart (ISPU)
            historyChart.series.forEach(series => {
                if (selected === 'all') {
                    series.show();
                } else {
                    // Show selected parameter and average line
                    series.setVisible(
                        series.name === selected || series.name === 'Rata-rata', 
                        false
                    );
                }
            });
            historyChart.redraw();
        });

        // Existing time range filter
        $('#timeRange').on('change', function() {
            const hours = parseInt($(this).val());
            const now = Date.now();
            const minTime = now - (hours * 3600 * 1000);
            
            // Update current data chart time range
            currentDataChart.xAxis[0].setExtremes(minTime, now);
            currentDataChart.redraw();

            // Update history chart time range
            const totalPoints = historyChart.xAxis[0].categories.length;
            const pointsToShow = Math.ceil((hours / 24) * totalPoints);
            const startIndex = Math.max(0, totalPoints - pointsToShow);
            
            historyChart.xAxis[0].setExtremes(startIndex, totalPoints - 1);
            historyChart.redraw();
        });
    }

    function updateSensorReadings(latestData) {
        // Update nilai sensor
        $('#pm25-value').text(latestData.sharp.toFixed(2));
        $('#co-value').text(latestData.mq7.toFixed(2));
        $('#no2-value').text(latestData.mq135.toFixed(2));
        $('#o3-value').text(latestData.mq131.toFixed(2));

        // Update grafik
        const timestamp = new Date(latestData.timestamp).getTime();
        currentDataChart.series[0].addPoint([timestamp, latestData.sharp], true, true);
        currentDataChart.series[1].addPoint([timestamp, latestData.mq7], true, true);
        currentDataChart.series[2].addPoint([timestamp, latestData.mq135], true, true);
        currentDataChart.series[3].addPoint([timestamp, latestData.mq131], true, true);
    }

    function updateFuzzyDetails(fuzzyResults) {
    const sensorMapping = {
        'Sharp (Debu)': '#pm25-fuzzy',
        'MQ7 (CO)': '#co-fuzzy',
        'MQ135 (CO2)': '#no2-fuzzy',
        'MQ131 (O3)': '#o3-fuzzy'
    };

    Object.entries(fuzzyResults).forEach(([sensor, values]) => {
        const element = sensorMapping[sensor];
        if (element) {
            // Cari nilai fuzzy tertinggi
            const categories = [
                {name: 'Baik', value: values.baik},
                {name: 'Sedang', value: values.sedang},
                {name: 'Buruk', value: values.buruk}
            ];
            
            // Urutkan dari nilai tertinggi ke terendah
            const sorted = categories.sort((a, b) => b.value - a.value);
            const highest = sorted[0];
            
            // Tampilkan hanya kategori dengan nilai tertinggi
            $(element).html(`
                <div class="dominant-category">
                    <span>${highest.name}:</span>
                    ${(highest.value * 100).toFixed(1)}%
                </div>
            `);
        }
    });
}

    function updateISPUStatus(overallAqi) {
        const status = overallAqi.status.toUpperCase();
        const confidence = (overallAqi.confidence * 100).toFixed(1);
        
        $('.ispu-status').text(status);
        $('.ispu-confidence').text(`${confidence}%`);
        
        const indicator = $('.status-indicator').not('.loading-status .status-indicator');
        indicator.removeClass('online warning critical').addClass(
            status === 'BAIK' ? 'online' :
            status === 'SEDANG' ? 'warning' : 'critical'
        );
    }

    async function updateRecommendation(status) {
        try {
            const response = await fetch(`/recommendation/get?status=${encodeURIComponent(status)}`);
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                // Update main recommendation text
                $('.recommendation-text').text(data.data.recommendation);
                
                // Update news section
                const newsHtml = `
                    <div class="news-item">
                        ${data.data.image_url ? `
                            <div class="news-image">
                                <img src="${data.data.image_url}" 
                                     alt="Air Quality Image"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='fallback-image-url.jpg';"
                                     onload="this.parentElement.classList.remove('loading')">
                            </div>
                        ` : ''}
                        <div class="news-content">
                            <div class="recommendation-text">${data.data.recommendation}</div>
                            ${data.data.news_url ? `
                                <a href="${data.data.news_url}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="news-link">
                                    Read Related News
                                    <span class="material-icons">open_in_new</span>
                                </a>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                $('.news-links').html(newsHtml);
            } else {
                // Fallback to default
                $('.recommendation-text').text(defaultRecommendations[status.toLowerCase()] || '-');
                $('.news-links').html('<p>No recommendations available</p>');
            }
        } catch (error) {
            console.error('Error getting recommendation:', error);
            $('.recommendation-text').text(defaultRecommendations[status.toLowerCase()] || '-');
            $('.news-links').html('<p>Failed to load recommendations</p>');
        }
    }

    async function fetchLatestData() {
        showLoading(true);
        
        try {
            const response = await fetch('https://udara.unis.ac.id/api/');
            const { data, status } = await response.json();
            
            if (status !== 'success') throw new Error('Respon API tidak valid');
            
            requestAnimationFrame(() => {
                updateSensorReadings(data.latest_data);
                updateISPUStatus(data.overall_aqi);
                updateFuzzyDetails(data.fuzzy_results);
                updateCategoryBreakdown(data.overall_aqi.details);
                updateRecommendation(data.overall_aqi.status);
            });

            retryCount = 0;
            $('.status-indicator').addClass('online').removeClass('warning critical');
        } catch (error) {
            console.error('Error:', error);
            if (retryCount++ < MAX_RETRIES) {
                setTimeout(fetchLatestData, 2000);
            } else {
                handleError('Gagal memuat data setelah beberapa kali percobaan');
            }
        } finally {
            showLoading(false);
        }
    }

    function handleError(message) {
        $('.status-indicator').removeClass('online warning').addClass('critical');
        $('.ispu-status').text('ERROR KONEKSI');
        $('.ispu-confidence').text('-');
        $('.recommendation-text').text(message);
        $('#pm25-value, #co-value, #no2-value, #o3-value').text('-');
        $('#baik-percent, #sedang-percent, #buruk-percent').text('-');
        showNotification('error', message);
    }

    function showLoading(isLoading) {
        const $loading = $('.loading-status');
        isLoading ? $loading.show() : $loading.hide();
    }

    function showNotification(type, message) {
        const notification = $(`
            <div class="notification ${type}">
                <span class="message">${message}</span>
                <span class="close">&times;</span>
            </div>
        `).appendTo('.dashboard-container');
        
        setTimeout(() => notification.remove(), 5000);
        notification.find('.close').on('click', () => notification.remove());
    }

    function updateCategoryBreakdown(details) {
        $('#baik-percent').text((details.baik * 100).toFixed(1) + '%');
        $('#sedang-percent').text((details.sedang * 100).toFixed(1) + '%');
        $('#buruk-percent').text((details.buruk * 100).toFixed(1) + '%');
    }

    function initDashboard() {
        initCharts();
        initChartFilters();
        setTimeout(fetchLatestData, 1000);
        setInterval(fetchLatestData, 15000);
    }

    $(document).ready(initDashboard);
</script>
</body>
</html>