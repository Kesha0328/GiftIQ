<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$totalRevenue = $orderCount = $productCount = 0;
$chartData = $productData = $monthlyData = [];

$res = $conn->query("SELECT COALESCE(SUM(total),0) AS rev FROM orders");
if ($res) $totalRevenue = (float)$res->fetch_assoc()['rev'];
$res = $conn->query("SELECT COUNT(*) AS c FROM orders");
if ($res) $orderCount = (int)$res->fetch_assoc()['c'];
$res = $conn->query("SELECT COUNT(*) AS c FROM products");
if ($res) $productCount = (int)$res->fetch_assoc()['c'];

$res = $conn->query("SELECT DATE(created_at) AS d, COALESCE(SUM(total),0) AS s FROM orders GROUP BY DATE(created_at) ORDER BY DATE(created_at)");
if ($res) while($r = $res->fetch_assoc()) $chartData[] = $r;

$res = $conn->query("
  SELECT p.name, COALESCE(SUM(oi.quantity),0) AS qty
  FROM order_items oi JOIN products p ON p.id=oi.product_id
  GROUP BY p.name ORDER BY qty DESC LIMIT 5");
if ($res) while($r = $res->fetch_assoc()) $productData[] = $r;

$res = $conn->query("
  SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COALESCE(SUM(total),0) AS s
  FROM orders GROUP BY m ORDER BY m");
if ($res) while($r = $res->fetch_assoc()) $monthlyData[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Security-Policy"
      content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://kit.fontawesome.com https://cdnjs.cloudflare.com;">
<title>Admin Dashboard</title>
<link rel="icon" type="image/png" href="../uploads/favicon.png" />
<?php include "admin_header.php" ?>
<style>
body{font-family:'Poppins',sans-serif;background:#0e0e0e;color:#fff;margin:0;}
.container{padding:25px;}
h1{font-size:1.6rem;color:#e9b89a;margin-bottom:25px;}
.cards{display:flex;flex-wrap:wrap;gap:20px;margin-bottom:30px;}
.card{flex:1;min-width:260px;background:rgba(255,255,255,0.05);
  border-radius:16px;padding:22px;border:1px solid rgba(233,184,154,0.1);
  box-shadow:0 6px 20px rgba(0,0,0,0.25);}
.card h3{font-size:1rem;color:#f7bfa5;margin-bottom:6px;}
.value{font-size:1.8rem;font-weight:700;}
.chart-card{background:rgba(255,255,255,0.03);border:1px solid rgba(233,184,154,0.1);
  border-radius:16px;padding:25px;margin-bottom:30px;}
.chart-card h3{color:#f7bfa5;margin-bottom:10px;font-size:1.1rem;}
#liveOrders{font-size:2.2rem;font-weight:800;color:#90ee90;}
@media(max-width:900px){.cards{flex-direction:column;}}
</style>
</head>
<body>
<div class="container">
  <h1>📊 Dashboard</h1>
  <div class="cards">
    <div class="card"><h3>Total Revenue</h3><div class="value">₹<?=number_format($totalRevenue,2)?></div></div>
    <div class="card"><h3>Orders</h3><div class="value"><?=$orderCount?></div></div>
    <div class="card"><h3>Products</h3><div class="value"><?=$productCount?></div></div>
    <div class="col-md-3">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Return Orders</h5>
      <h3 id="return-orders-count">0</h3>
    </div>
  </div>
</div>


    <div class="card">
      <h3>Today's Live Orders</h3>
      <div id="liveOrders">0</div>
      <small style="color:#aaa;"></small>
    </div>
  </div>

  <div class="chart-card"><h3>📅 Daily Sales</h3><canvas id="dailyChart" height="120"></canvas></div>
  <div class="chart-card"><h3>📈 Monthly Revenue</h3><canvas id="monthlyChart" height="120"></canvas></div>
</div>

<?php include "admin_footer.php" ?>


<script>
window.dashboardData={
  daily:<?=json_encode($chartData)?>,
  products:<?=json_encode($productData)?>,
  monthly:<?=json_encode($monthlyData)?>
};

async function fetchReturnOrders() {
  try {
    const res = await fetch('get_return_count.php', { cache: 'no-store' });
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    if (data && typeof data.total_returns !== 'undefined') {
      document.getElementById('return-orders-count').textContent = data.total_returns;
    }
  } catch (err) {
    console.error('Failed to fetch return orders:', err);
  }
}

fetchReturnOrders();

setInterval(fetchReturnOrders, 5000);

</script>



<script src="js/chart.umd.min.js"></script>
<script src="js/app.js"></script>

</body>
</html>
