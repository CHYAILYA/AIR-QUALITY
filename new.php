<?php
require 'config.php'; 

$sql = "SELECT * FROM data_udara ORDER BY id DESC LIMIT 30";
$result1 = $db->query($sql);
if (!$result1) {
    echo "Error: " . $sql . "<br>" . $db->error;
    exit;
}
function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
function getIspuCategory($ispu_value) {
  if ($ispu_value >= 0 && $ispu_value <= 50) {
      return ['Baik', 'green'];
  } elseif ($ispu_value >= 51 && $ispu_value <= 100) {
      return ['Sedang', 'blue'];
  } elseif ($ispu_value >= 101 && $ispu_value <= 200) {
      return ['Tidak Sehat', 'yellow'];
  } elseif ($ispu_value >= 201 && $ispu_value <= 300) {
      return ['Sangat Tidak Sehat', 'red'];
  } else {
      return ['Berbahaya', 'black'];
  }
}

$tglnow=date('Y-m-d');
$tglbef=date('Y-m-d', strtotime("-1 day", strtotime($tglnow)));
$cek = $db->query("SELECT * FROM histori where tgl='$tglnow' ")->fetch_assoc();
if($cek){
  
  $ispu_pm25_rounded=$cek['ispu_pm25'];
  $ispu_co_rounded=$cek['ispu_co'];
  $ispu_no2_rounded=$cek['ispu_no2'];
  $category_pm25 = getIspuCategory($ispu_pm25_rounded);
  $category_co = getIspuCategory($ispu_co_rounded);
  $category_no2 = getIspuCategory($ispu_no2_rounded);
  
  // Calculate average ISPU
  $avg_ispu = ($ispu_pm25_rounded + $ispu_co_rounded + $ispu_no2_rounded) / 3;
  $category_avg = getIspuCategory(round($avg_ispu));
}else{


//die("SELECT AVG(mq7) as avg_mq7, AVG(mq135) as avg_mq135, AVG(sharp) as avg_sharp FROM data_udara where date(timestamp)='$tglbef'");
$sql_avg = "SELECT AVG(mq7) as avg_mq7, AVG(mq135) as avg_mq135, AVG(sharp) as avg_sharp FROM data_udara where date(timestamp)='$tglbef' ";
$result_avg = $db->query($sql_avg);
if (!$result_avg) {
    echo "Error: " . $sql_avg . "<br>" . $db->error;
    exit;
}
$avg_values = $result_avg->fetch_assoc();


function calculateAQI($concentration, $breakpoints) {
  foreach ($breakpoints as $range) {
      if ($concentration >= $range['c_low'] && $concentration <= $range['c_high']) {
          return (($range['I_high'] - $range['I_low']) / ($range['c_high'] - $range['c_low'])) * ($concentration - $range['c_low']) + $range['I_low'];
      }
  }
  return null;
}



$breakpoints_no2 = [
    ['c_low' => 0, 'c_high' => 80, 'I_low' => 0, 'I_high' => 50],
    ['c_low' => 80, 'c_high' => 200, 'I_low' => 51, 'I_high' => 100],
    ['c_low' => 200, 'c_high' => 1130, 'I_low' => 101, 'I_high' => 150],
    ['c_low' => 1130, 'c_high' => 2260, 'I_low' => 151, 'I_high' => 200],
    ['c_low' => 2260, 'c_high' => 3000, 'I_low' => 201, 'I_high' => 300],
];

$breakpoints_co = [
    ['c_low' => 0, 'c_high' => 4000, 'I_low' => 0, 'I_high' => 50],
    ['c_low' => 4000, 'c_high' => 8000, 'I_low' => 51, 'I_high' => 100],
    ['c_low' => 8000, 'c_high' => 15000, 'I_low' => 101, 'I_high' => 150],
    ['c_low' => 15000, 'c_high' => 30000, 'I_low' => 151, 'I_high' => 200],
    ['c_low' => 30000, 'c_high' => 45000, 'I_low' => 201, 'I_high' => 300],
];

$breakpoints_pm25 = [
    ['c_low' => 0, 'c_high' => 15.4, 'I_low' => 0, 'I_high' => 50],
    ['c_low' => 15.4, 'c_high' => 55.4, 'I_low' => 51, 'I_high' => 100],
    ['c_low' => 55.4, 'c_high' => 150.4, 'I_low' => 101, 'I_high' => 150],
    ['c_low' => 150.4, 'c_high' => 250.4, 'I_low' => 151, 'I_high' => 200],
    ['c_low' => 250.4, 'c_high' => 500, 'I_low' => 201, 'I_high' => 300],
];

$avg_pm25 = round($avg_values['avg_sharp']);
$avg_co = round($avg_values['avg_mq7']);
$avg_no2 = round($avg_values['avg_mq135']);

$ispu_pm25 = calculateAQI($avg_pm25, $breakpoints_pm25);
$ispu_co = calculateAQI($avg_co, $breakpoints_co);
$ispu_no2 = calculateAQI($avg_no2, $breakpoints_no2);

$ispu_pm25_rounded = round($ispu_pm25);
$ispu_co_rounded = round($ispu_co);
$ispu_no2_rounded = round($ispu_no2);

$category_pm25 = getIspuCategory($ispu_pm25_rounded);
$category_co = getIspuCategory($ispu_co_rounded);
$category_no2 = getIspuCategory($ispu_no2_rounded);

// Calculate average ISPU
$avg_ispu = ($ispu_pm25_rounded + $ispu_co_rounded + $ispu_no2_rounded) / 3;
$category_avg = getIspuCategory(round($avg_ispu));
$saveispu = $db->query("INSERT INTO histori(tgl,ispu_pm25,ispu_co,ispu_no2,ispu_udara) 
			VALUES(curdate(),'".$ispu_pm25_rounded."','".$ispu_co_rounded."','".$ispu_no2_rounded."','".round($avg_ispu)."')");

}
$itemsPerPage = 30;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$sql = "SELECT * FROM data_udara where date(timestamp)='$tglnow' ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset";
$result = $db->query($sql);

$totalItemsSql = "SELECT COUNT(*) as total FROM data_udara  where date(timestamp)='$tglnow' ";
$totalItemsResult = $db->query($totalItemsSql);
$totalItems = $totalItemsResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

$sql_histori = "SELECT tgl, ispu_pm25, ispu_co, ispu_no2, ispu_udara FROM histori ORDER BY tgl ASC";
$result_histori = $db->query($sql_histori);

$histori_dates = [];
$histori_pm25 = [];
$histori_co = [];
$histori_no2 = [];
$histori_udara = [];

while ($row = $result_histori->fetch_assoc()) {
    $histori_dates[] = $row['tgl'];
    $histori_pm25[] = (int) $row['ispu_pm25'];
    $histori_co[] = (int) $row['ispu_co'];
    $histori_no2[] = (int) $row['ispu_no2'];
    $histori_udara[] = (int) $row['ispu_udara'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sistem Monitoring Udara</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .chart {
      width: 100%; 
      min-height: 400px;
    }
    .row {
      margin:0 !important;
    }
    .ispu-category {
      font-weight: bold;
    }
  </style>
</head>
<body>
  
<div class="container">
  <!-- Tabel ISPU -->
  <div class="row">
  <div class="col-md-12 text-center">
      <h1 style= "
      font-family: monospace;
      display:inline-block;
      padding: 5px;
      ">AIR QUALITY UNIS </h1>
      <p style = "font-family: monospace;">Created By: Vierlee Eka Kusuma</p>
      <p style = "font-family: monospace;">Teknik Informatika</p>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12"><center>
      
      <table class="table text-center">
        
        <tbody>
          <tr>
            <td width='30%'></td>
            <td style="background-color: <?php echo $category_avg[1];?>;" width='40%'>
            <h2 style="color:white"><?php echo strtoupper($category_avg[0]);?></h2></td>
            <td width='30%'></td>
            
          </tr>
          <tr>
          <td style="background-color: #019587;" colspan='3'>
          <h6 style="
          font-family: monospace;
          font-size: 24px
          ">ISPU UDARA UNIS HARI INI</h6>
          <h9 style = "font-family: monospace;">Data dikumpulkan pada tanggal <?php echo tgl_indo($tglbef); ?> dari jam 00:00 - 24:00</h9><br> 
          </td>
        </tr>
        </tbody>
      </table>
  </div>
    <div class="col-md-12">
      <table class="table text-center">
        <thead>
          <tr>
            <th scope="col">Parameter</th>
            <th scope="col">Nilai ISPU</th>
            <th scope="col">Kategori</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">CO</th>
            <td><?php echo $ispu_co_rounded !== null ? number_format($ispu_co_rounded, 0) : 'N/A'; ?></td>
            <td class="ispu-category" style="color: <?php echo $category_co[1]; ?>;"><?php echo $category_co[0]; ?></td>
          </tr>
          <tr>
            <th scope="row">NO2</th>
            <td><?php echo $ispu_no2_rounded !== null ? number_format($ispu_no2_rounded, 0) : 'N/A'; ?></td>
            <td class="ispu-category" style="color: <?php echo $category_no2[1]; ?>;"><?php echo $category_no2[0]; ?></td>
          </tr>
          <tr>
            <th scope="row">Debu (PM 2.5)</th>
            <td><?php echo $ispu_pm25_rounded !== null ? number_format($ispu_pm25_rounded, 0) : 'N/A'; ?></td>
            <td class="ispu-category" style="color: <?php echo $category_pm25[1]; ?>;"><?php echo $category_pm25[0]; ?></td>
          </tr>
          <tr>
            <th scope="row">Rata-rata ISPU</th>
            <td><?php echo $avg_ispu !== null ? number_format($avg_ispu, 0) : 'N/A'; ?></td>
            <td class="ispu-category" style="color: <?php echo $category_avg[1]; ?>;"><?php echo $category_avg[0]; ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    
    
    <div class="col-md-4">
      <div id="chart_CO" class="chart"></div>
    </div>
    
    <div class="col-md-4">
      <div id="chart_NO2" class="chart"></div>
    </div>
    
    <div class="col-md-4">
      <div id="chart_Debu" class="chart"></div>
    </div>  
    <div id="histori_chart" class="chart"></div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table text-center">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">CO</th>
            <th scope="col">NO2</th>
            <th scope="col">Debu</th>
            <th scope="col">Ozone</th>
            <th scope="col">date time</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <th scope="row"><?php echo $row['id']; ?></th>
            <td><?php echo $row['mq7']; ?></td>
            <td><?php echo $row['mq135']; ?></td>
            <td><?php echo $row['sharp']; ?></td>
            <!-- <td><?php echo $row['mq131']; ?></td> -->
            <td><?php echo number_format($row['mq131'], 3); ?></td>
            <td><?php echo $row['timestamp']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if($currentPage <= 1){ echo 'disabled'; } ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if($currentPage == $i){ echo 'active'; } ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if($currentPage >= $totalPages){ echo 'disabled'; } ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
    </div>
  </div>

</div>

<style>
  body {
      background: linear-gradient(to bottom, #E6FF94, #9DDE8B, #40A578, #006769);
      min-height: 100vh;
  }
  .chart {
      width: 100%; 
      min-height: 400px;
  }
  .row {
      margin: 0 !important;
  }
  .ispu-category {
      font-weight: bold;
  }
</style>

<style>
.pagination a, .pagination span {
    color: black;
    background-color: #E6FF94;
    border-color: #E6FF94;
}

.pagination a:hover, .pagination span:hover {
    color: white;
    background-color: #16423C;
    border-color: #16423C;
}

.pagination .active span {
    background-color: #16423C;
    color: #16423C;
    border-color: #16423C;
}
</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    setTimeout(function(){
      location.reload();
    }, 15000);
    

    var data_co = [];
    var data_no2 = [];
    var data_debu = [];
    
     <?php
      $result->data_seek(0); // Reset result pointer to the beginning
      while($row = $result->fetch_assoc()):
    ?>
      data_co.push(<?php echo $row['mq7']; ?>);
      data_no2.push(<?php echo $row['mq135']; ?>);
      data_debu.push(<?php echo $row['sharp']; ?>);
    <?php endwhile; ?>

    Highcharts.chart('chart_CO', {
  chart: {
    type: 'line',
    backgroundColor: '#6A9C89',
    animation: {
      duration: 2000
    }
  },
  title: {
    text: 'CO',
    style: {
      fontSize: '24px',
      fontWeight: 'bold',
      color: '#16423C'
    }
  },
  xAxis: {
    categories: [],
    title: {
      text: 'Waktu'
    }
  },
  yAxis: {
    title: {
      text: 'Nilai'
    }
  },
  series: [{
    name: 'CO',
    data: data_co,
    color: '#16423C',
    marker: {
      enabled: true,
      radius: 5,
      fillColor: '#F6FB7A',
      lineWidth: 2,
      lineColor: '#6A9C8'
    },
    lineWidth: 3,
  }],
});

Highcharts.chart('chart_NO2', {
  chart: {
    type: 'line',
    backgroundColor: '#6A9C89',
    animation: {
      duration: 2000
    }
  },
  title: {
    text: 'NO2',
    style: {
      fontSize: '24px',
      fontWeight: 'bold',
      color: '#16423C'
    }
  },
  xAxis: {
    categories: [],
    title: {
      text: 'Waktu'
    }
  },
  yAxis: {
    title: {
      text: 'Nilai'
    }
  },
  series: [{
    name: 'NO2',
    data: data_no2,
    color: '#16423C',
    marker: {
      enabled: true,
      radius: 5,
      fillColor: '#F6FB7A',
      lineWidth: 2,
      lineColor: '#6A9C89'
    },
    lineWidth: 3,
  }],
});

    Highcharts.chart('chart_Debu', {
  chart: {
    type: 'line',
    backgroundColor: '#6A9C89',
    animation: {
      duration: 2000
    }
  },
  title: {
    text: 'Debu',
    style: {
      fontSize: '24px',
      fontWeight: 'bold',
      color: '#16423C'
    }
  },
  xAxis: {
    categories: [],
    title: {
      text: 'Waktu'
    }
  },
  yAxis: {
    title: {
      text: 'Nilai'
    }
  },
  series: [{
    name: 'Debu',
    data: data_debu,
    color: '#16423C',    marker: {
      enabled: true,
      radius: 5,
      fillColor: '#F6FB7A',
      lineWidth: 2,
      lineColor: '#6A9C89'
    },
    lineWidth: 3,
  }],
});

    Highcharts.chart('histori_chart', {
    chart: {
        type: 'line',
        backgroundColor: '#6A9C89'
    },
    title: {
        text: 'Histori ISPU Udara',
        fontSize: '24px',
      fontWeight: 'bold',
      color: '#16423C'
    },
    xAxis: {
        categories: <?php echo json_encode($histori_dates); ?>,
        title: {
            text: 'Tanggal'
            
        }
    },
    yAxis: {
        title: {
            text: 'Nilai ISPU'
        }
    },
    series: [
        {
            name: 'PM 2.5',
            data: <?php echo json_encode($histori_pm25); ?>
        },
        {
            name: 'CO',
            data: <?php echo json_encode($histori_co); ?>
        },
        {
            name: 'NO2',
            data: <?php echo json_encode($histori_no2); ?>
        },
        {
            name: 'Rata-rata ISPU',
            data: <?php echo json_encode($histori_udara); ?>
        }
    ]
    });
  });
</script>

</body>
</html>