<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

$totalRevenue = $conn->query("SELECT COALESCE(SUM(total),0) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$orderCount   = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'] ?? 0;
$productCount = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'] ?? 0;
$userCount    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;

$chartData = [];
$res = $conn->query("SELECT DATE(created_at) AS d, SUM(total) AS s FROM orders GROUP BY DATE(created_at) ORDER BY DATE(created_at)");
while ($r = $res->fetch_assoc()) $chartData[] = $r;

$monthlyData = [];
if ($res = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total) AS total FROM orders GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month")) {
  while ($r = $res->fetch_assoc()) $monthlyData[] = $r;
}


$productData = [];
$res2 = $conn->query("SELECT p.name, SUM(i.quantity * i.price) AS total_sales 
  FROM order_items i 
  JOIN products p ON i.product_id = p.id 
  GROUP BY p.id ORDER BY total_sales DESC LIMIT 6");
while ($r = $res2->fetch_assoc()) $productData[] = $r;

$orders = [];
$res3 = $conn->query("SELECT o.id, u.name AS customer, o.total, o.status, o.created_at 
  FROM orders o 
  LEFT JOIN users u ON o.user_id=u.id 
  ORDER BY o.id DESC LIMIT 5");
while ($r = $res3->fetch_assoc()) $orders[] = $r;

include 'admin_header.php';
?>
<head>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <style>
    .dashboard-container { padding:25px; color:#fff; }
    .cards { display:flex; flex-wrap:wrap; gap:20px; margin-bottom:30px; }
    .card {
      flex:1; min-width:240px; background:rgba(255,255,255,0.05);
      border-radius:12px; border:1px solid rgba(255,255,255,0.1);
      padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.2);
    }
    .card h3 { color:#f4cbaa; font-size:1rem; margin-bottom:8px; }
    .value { font-size:1.8rem; font-weight:700; color:#fff; }
    .chart-box {
      background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);
      border-radius:16px; padding:25px; margin-bottom:30px;
    }
    canvas { width:100%!important; height:350px!important; }
    @media(max-width:768px){ .cards{flex-direction:column;} canvas{height:250px!important;} }

    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th, td { padding:10px 14px; text-align:left; border-bottom:1px solid rgba(255,255,255,0.08); }
    th { color:#f4cbaa; text-transform:uppercase; font-size:0.85rem; letter-spacing:0.5px; }
    td { color:#ddd; font-size:0.9rem; }
    .status { padding:4px 10px; border-radius:6px; font-size:0.8rem; }
    .pending { background:#ffc10733; color:#ffc107; }
    .completed { background:#28a74533; color:#28a745; }
    .cancelled { background:#dc354533; color:#dc3545; }
  </style>
</head>

<div class="dashboard-container">
  <h1 style="margin-bottom:20px;">📊 Sales Overview</h1>

  <div class="cards">
    <div class="card"><h3>Total Revenue</h3><div class="value">₹<?= number_format($totalRevenue,2) ?></div></div>
    <div class="card"><h3>Total Orders</h3><div class="value"><?= $orderCount ?></div></div>
    <div class="card"><h3>Products</h3><div class="value"><?= $productCount ?></div></div>
    <div class="card"><h3>Customers</h3><div class="value"><?= $userCount ?></div></div>
  </div>

  <div class="chart-box">
    <h3>📈 Daily Sales Overview</h3>
    <canvas id="salesChart"></canvas>
  </div>

  <div class="chart-box">
    <h3>🎁 Top Selling Products</h3>
    <canvas id="productChart"></canvas>
  </div>

  <div class="chart-card card" style="margin-top:30px;">
  <h3 style="margin-bottom:12px;">📊 Monthly Sales Overview</h3>
  <canvas id="monthChart" height="120"></canvas>
  </div>


  <div class="chart-box">
    <h3>🧾 Latest Orders</h3>
    <table>
      <thead>
        <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php if ($orders): foreach($orders as $o): ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer'] ?? 'Guest') ?></td>
            <td>₹<?= number_format($o['total'],2) ?></td>
            <td><span class="status <?= strtolower($o['status']) ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" style="text-align:center;color:#999;">No orders found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

<script src="js/app.js"></script>
<script>

async function updateCharts() {
    try {
      const response = await fetch('get_chart_data.php');
      const data = await response.json();


      dailyChart.data.labels = data.daily.labels;
      dailyChart.data.datasets[0].data = data.daily.values;
      dailyChart.update();


      monthlyChart.data.labels = data.monthly.labels;
      monthlyChart.data.datasets[0].data = data.monthly.values;
      monthlyChart.update();


      productChart.data.labels = data.top.labels;
      productChart.data.datasets[0].data = data.top.values;
      productChart.update();
    } catch (err) {
      console.error('Error updating chart:', err);
    }
  }

  setInterval(updateCharts, 10000);
</script>



