<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <title>UNIS KUALITAS UDARA</title>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* ====== (CSS sama persis seperti yang Anda berikan) ====== */
        /* … semua style dari root sampai @keyframes pulse dan seterusnya … */
    </style>
</head>
<body>
    <!-- Mobile Nav -->
    <div class="mobile-nav">
      <div class="nav-items">
        <a href="<?= base_url('') ?>" class="nav-item <?= service('uri')->getSegment(1)==''?'active':'' ?>">
          <span class="material-icons">dashboard</span>
          <span class="nav-text">Home</span>
        </a>
        <a href="<?= base_url('analytics') ?>" class="nav-item <?= service('uri')->getSegment(1)=='analytics'?'active':'' ?>">
          <span class="material-icons">trending_up</span>
          <span class="nav-text">Stats</span>
        </a>
        <a href="<?= base_url('alerts') ?>" class="nav-item <?= service('uri')->getSegment(1)=='alerts'?'active':'' ?>">
          <span class="material-icons">notifications</span>
          <span class="nav-text">Alerts</span>
        </a>
        <a href="<?= base_url('settings') ?>" class="nav-item <?= service('uri')->getSegment(1)=='settings'?'active':'' ?>">
          <span class="material-icons">settings</span>
          <span class="nav-text">Settings</span>
        </a>
      </div>
    </div>

    <div class="dashboard-container">
      <!-- Header -->
      <div class="header">
        <div class="header-left">
          <h1 class="title">System Dashboard</h1>
          <button id="theme-toggle" class="theme-toggle">
            <span class="material-icons light-icon">light_mode</span>
            <span class="material-icons dark-icon">dark_mode</span>
          </button>
        </div>
        <nav class="side-nav">
          <a href="<?= base_url('') ?>" class="nav-link active">
            <span class="material-icons">dashboard</span><span>Dashboard</span>
          </a>
          <a href="<?= base_url('analytics') ?>" class="nav-link">
            <span class="material-icons">analytics</span><span>Analytics</span>
          </a>
          <a href="<?= base_url('settings') ?>" class="nav-link">
            <span class="material-icons">settings</span><span>Settings</span>
          </a>
        </nav>
      </div>

      <!-- Content Wrapper -->
      <div class="dashboard-content-wrapper">
        <!-- Banner -->
        <div class="banner-container">
          <a href="https://whatsapp.com/channel/0029Vb5gDxgIXnllF5WzOP3J">
            <img src="<?= base_url('assets/images/banner.gif') ?>" alt="System Banner" class="banner-image">
          </a>
        </div>
        <!-- Air Quality Card -->
        <div class="air-quality-container">
          <div class="header">
          <h1>Kualitas udara di Kota Tangerang</h1>

            <div class="sub-header">
            <span>192.4K Pengikut</span>
              <span>•</span>
              <span>Updated: <?= esc($api_timestamp) ?></span>
            </div>
          </div>

          <div class="status-card <?= strtolower(str_replace(' ', '-', $aqi['status'])) ?>">
            <div class="status-title <?= strtolower(str_replace(' ', '-', $aqi['status'])) ?>">
              <?= esc(ucfirst($aqi['status'])) ?> AQI⁺ US: <?= esc($aqi['value']) ?> (<em><?= esc(ucfirst($aqi['status'])) ?></em>)
            </div>
       <div class="status-desc">
  <?= esc($aqiInfo['desc']) ?>
</div>


            <div class="metrics">
              <div class="metric-item">
                <span class="material-icons">air</span>
                <span class="metric-value"><?= esc($latest_data['sharp']) ?></span>
                <span>PM₂.₅</span>
              </div>
              <div class="metric-item">
                <span class="material-icons">co2</span>
                <span class="metric-value"><?= esc($latest_data['mq7']) ?></span>
                <span>CO</span>
              </div>
              <div class="metric-item">
                <span class="material-icons">cloud</span>
                <span class="metric-value"><?= esc($latest_data['mq135']) ?></span>
                <span>NO₂</span>
              </div>
              <div class="metric-item">
                <span class="material-icons">water_drop</span>
                <span class="metric-value"><?= esc($latest_data['mq131']) ?></span>
                <span>O₃</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">PM₂.₅</span>
            <span class="material-icons stat-icon">air</span>
        </div>
        <div class="stat-value" data-sensor="sharp"><?= esc($latest_data['sharp'] ?? 'N/A') ?></div>
        <div class="stat-subtitle">Kualitas Udara</div>
        <div class="stat-status">
            <span class="status-label" data-status="sharp"><?= esc($sensor_status['sharp'] ?? 'Unknown') ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">CO</span>
            <span class="material-icons stat-icon">co2</span>
        </div>
        <div class="stat-value" data-sensor="mq7"><?= esc($latest_data['mq7'] ?? 'N/A') ?></div>
        <div class="stat-subtitle">Kualitas Udara</div>
        <div class="stat-status">
            <span class="status-label" data-status="mq7"><?= esc($sensor_status['mq7'] ?? 'Unknown') ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">NO₂</span>
            <span class="material-icons stat-icon">cloud</span>
        </div>
        <div class="stat-value" data-sensor="mq135"><?= esc($latest_data['mq135'] ?? 'N/A') ?></div>
        <div class="stat-subtitle">Kualitas Udara</div>
        <div class="stat-status">
            <span class="status-label" data-status="mq135"><?= esc($sensor_status['mq135'] ?? 'Unknown') ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">O₃</span>
            <span class="material-icons stat-icon">water_drop</span>
        </div>
        <div class="stat-value" data-sensor="mq131"><?= esc($latest_data['mq131'] ?? 'N/A') ?></div>
        <div class="stat-subtitle">Kualitas Udara</div>
        <div class="stat-status">
            <span class="status-label" data-status="mq131"><?= esc($sensor_status['mq131'] ?? 'Unknown') ?></span>
        </div>
    </div>
</div>
<script>
    // Theme Toggle Logic
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-theme', savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        htmlElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Force chart redraw
        if(window.myChart){
            window.myChart.destroy();
            window.myChart = new Chart(ctx, chartConfig);
        }
    });
</script>
<script>
    function updateData() {
        fetch('https://udara.unis.ac.id/api/')
            .then(response => response.json())
            .then(data => {
                // Update nilai sensor
                const sensors = ['sharp', 'mq7', 'mq135', 'mq131'];
                sensors.forEach(sensor => {
                    const valueEl = document.querySelector(`.stat-value[data-sensor="${sensor}"]`);
                    if (valueEl && data[sensor] !== undefined) {
                        valueEl.textContent = data[sensor];
                    }

                    // Jika juga ingin update status sensor
                    const statusEl = document.querySelector(`.status-label[data-status="${sensor}"]`);
                    if (statusEl && data.sensor_status && data.sensor_status[sensor] !== undefined) {
                        statusEl.textContent = data.sensor_status[sensor];
                    }
                });
            })
            .catch(error => console.error('Gagal fetch data sensor:', error));
    }

    // Update pertama kali saat load
    updateData();

    // Update otomatis tiap 10 detik
    setInterval(updateData, 10000);
</script>



<style>
.time-filter {
  margin: 0 1rem 1rem;
  text-align: right;
}

#timeRange {
  padding: 0.8rem 1.2rem;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 1rem;
  width: 100%;
  max-width: 250px;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 1em;
  transition: all 0.3s ease;
}

#timeRange:hover {
  border-color: var(--primary-color);
}

.chart-container {
  position: relative;
  width: 100%;
  min-height: 300px;
  padding: 1rem;
  background: var(--bg-secondary);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

@media (max-width: 768px) {
  .grid-container {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 0.5rem;
  }
  
  .chart-container {
    min-height: 250px;
    padding: 0.5rem;
  }
  
  #timeRange {
    max-width: 100%;
    font-size: 0.9rem;
    padding: 0.6rem 1rem;
  }
}


</style>

<!-- HTML -->
<div class="time-filter">
  <select id="timeRange" onchange="updateChart(this.value)">
    <option value="30">30 Menit Terakhir</option>
    <option value="60">1 Jam Terakhir</option>
    <option value="120">2 Jam Terakhir</option>
    <option value="180">3 Jam Terakhir</option>
    <option value="360">6 Jam Terakhir</option>
    <option value="720">12 Jam Terakhir</option>
    <option value="1440">24 Jam Terakhir</option>
  </select>
</div>
      <!-- Charts & Status List -->
      <div class="grid-container">
        <div class="chart-container">
          <canvas id="airQualityChart"></canvas>
        </div>
       <!-- Ganti bagian status list dengan ini -->
<div class="health-recommendations">
    <h3 style="margin-bottom:1rem; color: var(--text-primary)">
        <!-- <span class="material-icons">health_and_safety</span> -->
        Rekomendasi Kesehatan
    </h3>
    
    <?= $health_recommendations ?>
</div>

    <!-- Chart.js Init -->
    <!-- Ubah bagian Chart.js Init menjadi: -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variabel global untuk menyimpan instance chart
let airQualityChart = null;

// Fungsi inisialisasi chart
function initChart(labels, data) {
  const ctx = document.getElementById('airQualityChart');
  
  // Hancurkan chart sebelumnya jika ada
  if (airQualityChart) {
    airQualityChart.destroy();
  }

  airQualityChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'AQI⁺ US',
        data: data,
        borderColor: '#6366f1',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.4,
        fill: true,
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (context) => 'Jam ' + context[0].label
          }
        }
      },
      scales: {
        x: {
          title: { text: 'Waktu', display: true },
          grid: { display: false }
        },
        y: {
          title: { text: 'Nilai AQI', display: true },
          beginAtZero: true
        }
      }
    }
  });
}

// Fungsi untuk update chart
async function updateChart(minutes) {
  try {
    const response = await fetch(`/getChartData?minutes=${minutes}`);
    const data = await response.json();
    
    const labels = data.map(item => item.time);
    const aqiData = data.map(item => item.aqi);
    
    initChart(labels, aqiData);
  } catch (error) {
    console.error('Error fetching chart data:', error);
  }
}

// Inisialisasi pertama dengan data 30 menit
document.addEventListener('DOMContentLoaded', () => {
  updateChart(30);
});
</script>
</body>
</html>
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

            /* Light mode (default) */
            --bg-color: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            --card-bg: white;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
            --text-color: #1d1d1f;
            --text-secondary: #666;
            --border-color: #eee;
            --status-bg: #fff4e5;
            --status-text: #ff9500;
        }

        [data-theme="dark"] {
            --bg-color: linear-gradient(45deg, #1a1a1a, #2d2d2d);
            --card-bg: #333;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.3);
            --text-color: #ffffff;
            --text-secondary: #b0b0b0;
            --border-color: #404040;
            --status-bg: #433122;
            --status-text: #ff9500;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: var(--bg-color);
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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: var(--text-primary);
            font-weight: 700;
            line-height: 1.2;
            margin-right: 2rem;
        }

        .theme-toggle {
            background: transparent;
            border: none;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
            transition: all 0.3s ease;
            margin-left: 1rem;
        }

        .theme-toggle:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .theme-toggle .material-icons {
            font-size: 24px;
        }

        .theme-toggle .dark-icon {
            display: none;
        }

        [data-theme="dark"] .light-icon {
            display: none;
        }

        [data-theme="dark"] .dark-icon {
            display: block;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--glass);
            padding: 1.5rem;
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: clamp(0.9rem, 2vw, 1rem);
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-icon {
            font-size: clamp(1.5rem, 3vw, 1.8rem);
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-value {
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -1px;
        }

        .stat-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        /* Main Content Grid */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
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
        #airQualityChart {
    padding: 16px;
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

        /* Mobile Navbar Updated Styles */
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
            padding: 1rem;
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
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-secondary);
        }

        .nav-item .material-icons {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            transition: color 0.3s ease;
        }

        .nav-text {
            font-size: 0.75rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        /* Update active state to match desktop nav */
        .nav-item.active {
            background: transparent;
        }

        .nav-item.active .material-icons,
        .nav-item.active .nav-text {
            color: var(--primary);
        }

        .nav-item:hover {
            background: transparent;
        }

        .nav-item:hover .material-icons,
        .nav-item:hover .nav-text {
            color: var(--primary);
        }

        /* Responsive Breakpoints */
        @media screen and (min-width: 769px) {
            .side-nav {
                display: flex;
            }
            
            .mobile-nav {
                display: none; /* Keep mobile nav hidden on larger screens */
            }
        }

        @media screen and (max-width: 768px) {
            .side-nav {
                display: none; /* Hide desktop nav on mobile */
            }
            
            .mobile-nav {
                display: block; /* Show mobile nav on mobile screens */
            }

            body {
                padding: 0.5rem;
                padding-bottom: 5rem; /* Add space for mobile nav */
            }

            .header {
                margin-bottom: 1rem;
                padding: 0.75rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                padding: 0 0.5rem;
            }

            .stat-card {
                padding: 0.5rem;
                min-width: 0; /* Add this to allow cards to shrink */
            }

            .stat-header {
                margin-bottom: 0.5rem;
            }

            .stat-title {
                font-size: 0.85rem;
            }

            .stat-icon {
                font-size: 1.25rem;
            }

            .stat-value {
                font-size: 1.25rem;
                margin: 0.25rem 0;
            }

            .stat-subtitle {
                font-size: 0.75rem;
            }

            .grid-container {
                gap: 1rem;
            }

            .dashboard-content-wrapper {
                flex-direction: column;
                padding: 0 1rem;
            }

            .air-quality-container {
                width: 100%;
                max-width: 400px;
                margin: 0 auto 1rem;
                padding: 1rem;
                border-radius: 10px;
            }

            .banner-container {
                width: 100%;
                margin-bottom: 1rem;
            }

            .dashboard-content-wrapper {
                flex-direction: column;
            }

            .air-quality-container {
                width: calc(100% - 2rem);
                max-width: none;
                margin: 0 auto 5rem auto; /* Changed to auto for horizontal centering */
                padding: 1rem;
            }

            .status-card {
                margin: 0.75rem 0;
                width: 100%;
            }

            .metrics {
                justify-content: space-between;
                width: 100%;
            }
        }

        @media screen and (max-width: 480px) {
            .air-quality-container {
                width: calc(100% - 1.5rem);
                margin: 0 auto 1rem auto; /* Changed to auto for horizontal centering */
                padding: 0.875rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr); /* Keep 2 columns for better layout */
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.5rem;
            }

            .stat-title {
                font-size: 0.8rem;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            .stat-subtitle {
                font-size: 0.7rem;
            }

            .status-item {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            .chart-container {
                padding: 1rem;
                min-height: 300px;
            }

            .metrics {
                gap: 8px;
            }

            .dashboard-content-wrapper {
                padding: 0 0.5rem;
            }

            .air-quality-container {
                width: 100%;
                margin: 0 auto 1rem;
                padding: 0.875rem;
            }
        }

        @media (hover: none) {
            .stat-card:hover,
            .status-item:hover {
                transform: none;
            }
        }

        .air-quality-container {
            background: var(--card-bg);
            box-shadow: var(--card-shadow);
            max-width: 400px;
            margin: 0 0 1rem auto; /* Changed from 0 auto 1rem auto to align right */
            padding: 16px;
            border-radius: 10px;
            margin-right: 20px; /* Added right margin for spacing */
        }

        .air-quality-container .header {
            margin-bottom: 12px; /* Reduced from 16px */
        }

        .air-quality-container h1 {
            font-size: 20px; /* Reduced from 24px */
            color: var(--text-color);
            margin: 0;
        }

        .sub-header {
            color: var(--text-secondary);
            font-size: 12px; /* Reduced from 14px */
        }

        .data-source {
            color: var(--text-secondary);
            font-size: 12px; /* Reduced from 14px */
            margin: 8px 0; /* Reduced from 12px */
            padding-bottom: 12px; /* Reduced from 16px */
            border-bottom: 1px solid var(--border-color);
        }

        .status-card {
            background: var(--status-bg);
            padding: 12px; /* Reduced from 16px */
            border-radius: 8px;
            margin: 12px 0; /* Reduced from 16px */
        }

        .status-title {
            color: var(--status-text);
            font-weight: 600;
            margin-bottom: 8px; /* Reduced from 12px */
            font-size: 14px; /* Added size */
        }

        .pollutant {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px; /* Reduced from 16px */
            font-size: 13px; /* Added size */
            color: var(--text-secondary);
        }

        .pollutant-value {
            font-size: 24px; /* Reduced from 32px */
            font-weight: 700;
            color: var(--text-color);
        }
        .metrics {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 0.8rem;
  padding: 0.5rem;
  flex-grow: 1;
}

.metric-item {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 1rem;
  white-space: nowrap;
}

        .metric-value {
  font-weight: 600;
  font-size: 1.0rem;
  color: var(--text-color);
}
.metric-item .material-icons {
  font-size: 1.2rem;
  color: var(--text-color);
}
/* Untuk layar sedang (tablet) */
@media (max-width: 768px) {
  .metric-value {
    font-size: 1.0rem;
  }
}

/* Untuk layar kecil (HP) */
@media (max-width: 480px) {
  .metric-value {
    font-size: 1.0rem;
  }
}

        /* New styles added */
        .dashboard-content-wrapper {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            align-items: stretch; /* Changed from flex-start */
        }

        /* Banner styles for desktop */
        .banner-container {
            flex: 1;
            max-width: 1600px;
            height: auto;
            margin-right: 1rem;
        }

        .banner-image {
            width: 100%;
            height: auto;
            max-height: 600px;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Add responsive styling */
        @media screen and (max-width: 768px) {
            .dashboard-content-wrapper {
                flex-direction: column;
            }
            
            .banner-container {
                max-width: 100%;
                margin: 0 auto 1rem auto;
                padding: 0 1rem;
            }
            
            .banner-image {
                width: 100%;
                height: auto;
                max-height: 400px;
                object-fit: contain;
            }
        }

        /* Air Quality Status Background Colors */
        .status-title.baik {
            background-color: #00e400;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-title.sedang {
            background-color: #ffff00;
            color: #000;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-title.tidak-sehat-untuk-kelompok-sensitif {
            background-color: #ff7e00;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-title.tidak-sehat {
            background-color: #ff0000;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-title.sangat-tidak-sehat {
            background-color: #8f3f97;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-title.berbahaya {
            background-color: #7e0023;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Status Card Background Colors */
        .status-card.baik {
            background-color: rgba(0, 228, 0, 0.1);
        }

        .status-card.sedang {
            background-color: rgba(255, 255, 0, 0.1);
        }

        .status-card.tidak-sehat-untuk-kelompok-sensitif {
            background-color: rgba(255, 126, 0, 0.1);
        }

        .status-card.tidak-sehat {
            background-color: rgba(255, 0, 0, 0.1);
        }

        .status-card.sangat-tidak-sehat {
            background-color: rgba(143, 63, 151, 0.1);
        }

        .status-card.berbahaya {
            background-color: rgba(126, 0, 35, 0.1);
        }


        .health-recommendations {
  background: var(--glass);
  padding: 1.5rem;
  border-radius: 1rem;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.recommendation-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  margin: 0.75rem 0;
  border-radius: 0.75rem;
  background: rgba(255, 255, 255, 0.6);
  gap: 1rem;
}

.recommendation-item .material-icons {
  font-size: 1.8rem;
  color: var(--primary);
}

.recommendation-text {
  flex: 1;
  font-size: 0.95rem;
  color: var(--text-primary);
}

.recommendation-link {
  display: block;
  margin-top: 0.5rem;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.85rem;
}

@media (max-width: 768px) {
  .recommendation-item {
    padding: 0.75rem;
  }
  
  .recommendation-text {
    font-size: 0.85rem;
  }
  
  .recommendation-item .material-icons {
    font-size: 1.5rem;
  }
}
    </style>