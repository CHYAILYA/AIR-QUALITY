<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Monitoring Kualitas Air - Full Monitoring</title>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 2rem;
    }
    .container {
      max-width: 1000px;
      margin: auto;
    }
    .card {
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 5px 15px rgba(0,0,0,.05);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    .title {
      font-weight: 600;
      font-size: 1.4rem;
      margin-bottom: 1rem;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 1rem;
    }
    .item {
      background: #f9fafb;
      padding: 1rem;
      border-radius: 0.75rem;
      text-align: center;
    }
    .item-title {
      font-size: 0.9rem;
      color: #666;
    }
    .item-value {
      font-weight: 600;
      font-size: 1.2rem;
      color: #111827;
    }
    .badge {
      display: inline-block;
      padding: 0.4rem 0.7rem;
      border-radius: 9999px;
      font-weight: 600;
      font-size: 0.85rem;
      text-transform: capitalize;
    }
    .baik {
      background: #10b981;
      color: #fff;
    }
    .buruk {
      background: #ef4444;
      color: #fff;
    }
    .chart-container {
      height: 300px;
      margin: 1rem 0;
    }
    .controller {
      margin: 1rem 0;
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }
    select {
      padding: 0.5rem;
      border-radius: 0.5rem;
      border: 1px solid #ddd;
      background: white;
    }
    /* Styles for the new table */
    .table-responsive {
      overflow-x: auto; /* Allows horizontal scrolling for table */
      -webkit-overflow-scrolling: touch; /* Improves scrolling on touch devices */
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      min-width: 600px; /* Ensures table doesn't shrink too much on small screens */
    }
    .data-table th, .data-table td {
      border: 1px solid #eee;
      padding: 0.8rem;
      text-align: left;
      font-size: 0.9rem;
      white-space: nowrap; /* Prevents text from wrapping within cells */
    }
    .data-table th {
      background-color: #f2f2f2;
      font-weight: 600;
      color: #333;
    }
    .data-table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .data-table tbody tr:hover {
      background-color: #f0f0f0;
    }
    /* New styles for the two charts layout */
    .chart-row {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem; /* Space between charts */
      justify-content: center; /* Center charts if they don't fill the row */
      margin-bottom: 2rem;
    }
    .chart-row .card {
      flex: 1; /* Allows cards to grow and shrink */
      min-width: 300px; /* Minimum width for each chart card */
    }
    .btn-primary {
      background: #2F39C2;
      color: #fff;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 8px rgba(47,57,194,0.08);
      transition: background 0.2s, box-shadow 0.2s;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }
    .btn-primary:hover,
    .btn-primary:focus {
      background: #1d256e;
      color: #fff;
      box-shadow: 0 4px 16px rgba(47,57,194,0.15);
      text-decoration: none;
    }

  </style>
</head>
<body>
  <div class="container">
    <!-- Selamat datang dan tombol dashboard -->
    <div class="d-flex align-items-center mb-3" style="gap:1rem;">
      <span style="font-weight:600; font-size:1.1rem;">
        Selamat datang, <?= esc(session()->get('userData')['name'] ?? 'User') ?>
      </span>
      <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dashboardDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          Menu
        </button>
        <ul class="dropdown-menu" aria-labelledby="dashboardDropdown">
          <li>
            <a class="dropdown-item" href="/admin/dashboard">
              <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="/logout">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <div class="chart-row">
      <!-- Hapus Skor Kualitas Air -->
      <!-- Card Distribusi Nilai Parameter Saat Ini -->
      <div class="card">
        <div class="title">Distribusi Nilai Parameter Saat Ini</div>
        <div class="controller">
          <label>Rentang Waktu:</label>
          <select id="pieChartTimeRange">
            <option value="1">1 Jam</option>
            <option value="3">3 Jam</option>
            <option value="6">6 Jam</option>
            <option value="24">24 Jam</option>
          </select>
        </div>
        <div id="allParametersChart" class="chart-container"></div>
      </div>
    </div>

    <div id="fuzzy-result-container">Memuat data...</div>

    <div class="card">
      <div class="title">Grafik Historis</div>
      <div class="controller">
        <label>Parameter:</label>
        <select id="parameterSelect">
          <option value="all">Tampilkan Semua</option>
          <option value="TDS_ppm">TDS (ppm)</option>
          <option value="Turbidity_NTU">Kekeruhan (NTU)</option>
          <option value="pH">pH</option>
          <option value="suhu">Suhu (°C)</option>
        </select>
        <label>Rentang Waktu:</label>
        <select id="timeRange">
          <option value="1">1 Jam</option>
          <option value="3">3 Jam</option>
          <option value="6">6 Jam</option>
        </select>
      </div>
      <div id="historyChart" class="chart-container"></div>
    </div>

    <div class="card">
      <div class="title">Data Historis</div>
      <div class="controller">
        <label>Rentang Waktu Tabel:</label>
        <select id="tableTimeRange">
          <option value="1">1 Jam</option>
          <option value="3">3 Jam</option>
          <option value="6">6 Jam</option>
          <option value="24">24 Jam</option>
        </select>
      </div>
      <div class="table-responsive">
        <div id="historyTableContainer">Memuat data tabel...</div>
      </div>
    </div>
  </div>

  <script>
    const formatTime = (timestamp) => {
      // Format WIB (Asia/Jakarta)
      const date = new Date(timestamp);
      return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta'
      });
    };

    const createHistoryChart = () => {
      const paramSelector = document.getElementById('parameterSelect');
      const timeSelector = document.getElementById('timeRange');
      let chart;

      const updateChart = () => {
        const selectedParam = paramSelector.value;
        const hours = parseInt(timeSelector.value);

        fetch(`https://udara.unis.ac.id/history?hours=${hours}`)
          .then(response => response.json())
          .then(historyData => {
            if (!historyData.success || historyData.data.length === 0) {
              document.getElementById("historyChart").innerHTML = "Tidak ada data untuk rentang waktu yang dipilih.";
              return;
            }

            let series = [];

            if (selectedParam === 'all') {
              const tds = historyData.data.map(d => ({ x: new Date(d.timestamp).getTime(), y: parseFloat(d.TDS_ppm) }));
              const turb = historyData.data.map(d => ({ x: new Date(d.timestamp).getTime(), y: parseFloat(d.Turbidity_NTU) }));
              const ph = historyData.data.map(d => ({ x: new Date(d.timestamp).getTime(), y: parseFloat(d.pH) }));
              const suhu = historyData.data.map(d => ({ x: new Date(d.timestamp).getTime(), y: parseFloat(d.suhu) }));

              series = [
                { name: 'TDS (ppm)', data: tds, color: '#3b82f6' },
                { name: 'Kekeruhan (NTU)', data: turb, color: '#f59e0b' },
                { name: 'pH', data: ph, color: '#10b981' },
                { name: 'Suhu (°C)', data: suhu, color: '#6366f1' }
              ];
            } else {
              const data = historyData.data.map(d => ({
                x: new Date(d.timestamp).getTime(),
                y: parseFloat(d[selectedParam]),
                name: formatTime(d.timestamp)
              }));

              const colorMap = {
                TDS_ppm: '#3b82f6',
                Turbidity_NTU: '#f59e0b',
                pH: '#10b981',
                suhu: '#6366f1'
              };

              series = [{
                name: selectedParam,
                data: data,
                color: colorMap[selectedParam] || '#6366f1'
              }];
            }

            if (!chart) {
              chart = Highcharts.chart('historyChart', {
                chart: { type: 'line' },
                title: { text: `Data Historis (${selectedParam})` },
                xAxis: {
                  type: 'datetime',
                  title: { text: 'Waktu (WIB)' },
                  labels: {
                    formatter: function() {
                      // Ambil jam dan menit dari waktu lokal
                      const date = new Date(this.value);
                      return date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Asia/Jakarta'
                      });
                    }
                  }
                },
                yAxis: { title: { text: 'Nilai' } },
                tooltip: {
                  shared: true,
                  xDateFormat: '%H:%M',
                  formatter: function() {
                    return `<b>${this.series.name}</b><br/>${formatTime(this.x)} WIB<br/>${this.y}`;
                  }
                },
                series: series
              });
            } else {
              chart.update({
                title: { text: `Data Historis (${selectedParam})` },
                series: series
              }, true, true);
            }
          })
          .catch(err => {
            document.getElementById("historyChart").innerHTML = `<p>Gagal memuat data: ${err.message}</p>`;
          });
      };

      paramSelector.addEventListener('change', updateChart);
      timeSelector.addEventListener('change', updateChart);
      updateChart();
    };

    // Function to load historical data into a table with selectable time range
    const loadHistoryTable = () => {
      const tableTimeSelector = document.getElementById('tableTimeRange');
      const hours = parseInt(tableTimeSelector.value); // Get selected hours for the table

      fetch(`https://udara.unis.ac.id/history?hours=${hours}`) // Fetch data based on selected hours
        .then(response => response.json())
        .then(historyData => {
          const tableContainer = document.getElementById('historyTableContainer');
          if (!historyData.success || historyData.data.length === 0) {
            tableContainer.innerHTML = "<p>Tidak ada data historis yang tersedia untuk ditampilkan dalam tabel.</p>";
            return;
          }

          let tableHtml = `
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Waktu</th>
                  <th>TDS (ppm)</th>
                  <th>Turbidity (NTU)</th>
                  <th>pH</th>
                  <th>Suhu (°C)</th>
                </tr>
              </thead>
              <tbody>
          `;

          historyData.data.forEach(d => {
            const dateTime = new Date(d.timestamp);
            const formattedDateTime = dateTime.toLocaleString('id-ID', {
              year: 'numeric',
              month: '2-digit',
              day: '2-digit',
              hour: '2-digit',
              minute: '2-digit',
              second: '2-digit'
            });

            tableHtml += `
              <tr>
                <td>${d.id}</td>
                <td>${formattedDateTime}</td>
                <td>${d.TDS_ppm}</td>
                <td>${d.Turbidity_NTU}</td>
                <td>${d.pH}</td>
                <td>${d.suhu}</td>
              </tr>
            `;
          });

          tableHtml += `
              </tbody>
            </table>
          `;
          tableContainer.innerHTML = tableHtml;
        })
        .catch(err => {
          document.getElementById("historyTableContainer").innerHTML = `<p>Gagal memuat data tabel: ${err.message}</p>`;
        });
    };

    // New function for the "Distribusi Nilai Parameter Saat Ini" pie chart
    const createAllParametersPieChart = () => {
      const pieChartTimeSelector = document.getElementById('pieChartTimeRange');
      const hours = parseInt(pieChartTimeSelector.value);

      fetch(`https://udara.unis.ac.id/history?hours=${hours}`)
        .then(response => response.json())
        .then(historyData => {
          const allParametersChartContainer = document.getElementById('allParametersChart');
          if (!historyData.success || historyData.data.length === 0) {
            allParametersChartContainer.innerHTML = "<p>Tidak ada data parameter untuk rentang waktu yang dipilih.</p>";
            return;
          }

          // Get the latest data point for the pie chart
          const latestData = historyData.data[historyData.data.length - 1];

          Highcharts.chart('allParametersChart', {
            chart: { type: 'pie' },
            title: { text: `Distribusi Nilai Parameter (Data Terbaru dalam ${hours} Jam)` }, // Updated title
            plotOptions: {
              pie: {
                innerSize: '60%',
                dataLabels: {
                  enabled: true,
                  format: '{point.name}: {point.y}'
                }
              }
            },
            series: [{
              name: 'Nilai Parameter',
              data: [
                { name: 'TDS (ppm)', y: parseFloat(latestData.TDS_ppm) },
                { name: 'Turbidity (NTU)', y: parseFloat(latestData.Turbidity_NTU) },
                { name: 'pH', y: parseFloat(latestData.pH) },
                { name: 'Suhu (°C)', y: parseFloat(latestData.suhu) }
              ]
            }]
          });
        })
        .catch(err => {
          document.getElementById("allParametersChart").innerHTML = `<p>Gagal memuat grafik parameter: ${err.message}</p>`;
        });
    };


    const initDashboard = () => {
      fetch('https://udara.unis.ac.id/air/')
        .then(response => response.json())
        .then(res => {
          const d = res.data[0];
          const fuzzyHtml = ` 
            <div class="card">
              <div class="title">Detail Parameter</div>
              <div class="grid">
                <div class="item"><div class="item-title">TDS</div><div class="item-value">${d.TDS_ppm} ppm</div></div>
                <div class="item"><div class="item-title">Turbidity</div><div class="item-value">${d.Turbidity_NTU} NTU</div></div>
                <div class="item"><div class="item-title">pH</div><div class="item-value">${d.pH}</div></div>
                <div class="item"><div class="item-title">Suhu</div><div class="item-value">${d.suhu} °C</div></div>
                <div class="item"><div class="item-title">Kategori</div><div class="item-value"><span class="badge ${d.kategori.toLowerCase()}">${d.kategori}</span></div></div>
              </div>
            </div>
            <div class="card">
              <div class="title">Membership Fuzzy</div>
              <div class="grid">
                <div class="item"><div class="item-title">TDS Baik</div><div class="item-value">${d.TDS_baik}</div></div>
                <div class="item"><div class="item-title">TDS Buruk</div><div class="item-value">${d.TDS_buruk}</div></div>
                <div class="item"><div class="item-title">Turbidity Baik</div><div class="item-value">${d.Turbidity_baik}</div></div>
                <div class="item"><div class="item-title">Turbidity Buruk</div><div class="item-value">${d.Turbidity_buruk}</div></div>
                <div class="item"><div class="item-title">pH Ideal</div><div class="item-value">${d.pH_ideal}</div></div>
                <div class="item"><div class="item-title">pH Asam</div><div class="item-value">${d.pH_asam}</div></div>
                <div class="item"><div class="item-title">pH Basa</div><div class="item-value">${d.pH_basa}</div></div>
              </div>
            </div>`;
          document.getElementById("fuzzy-result-container").innerHTML = fuzzyHtml;

          // Hapus chart Skor Kualitas Air

          createHistoryChart();
          loadHistoryTable();
          createAllParametersPieChart();
        })
        .catch(err => {
          document.getElementById("fuzzy-result-container").innerHTML = `<div class="card"><p>Gagal memuat data: ${err.message}</p></div>`;
        });
    };

    document.addEventListener('DOMContentLoaded', initDashboard);
    setInterval(initDashboard, 60000);

    document.addEventListener('DOMContentLoaded', () => {
      const tableTimeSelector = document.getElementById('tableTimeRange');
      if (tableTimeSelector) {
        tableTimeSelector.addEventListener('change', loadHistoryTable);
      }
      const pieChartTimeSelector = document.getElementById('pieChartTimeRange');
      if (pieChartTimeSelector) {
        pieChartTimeSelector.addEventListener('change', createAllParametersPieChart);
      }
    });
  </script>
</body>
</html>
