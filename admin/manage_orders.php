<?php
require 'admin_header.php';
require 'mail_template.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id=$oid");

    $order = $conn->query("
        SELECT o.*, u.name AS customer, u.email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id=$oid
    ")->fetch_assoc();

    if ($order && !empty($order['email'])) {
        $items = $conn->query("SELECT * FROM order_items WHERE order_id=$oid");
        $itemList = "";
        $total = 0;
        while ($it = $items->fetch_assoc()) {
            $pname = "Custom Gift";
            if (empty($it['custom_data'])) {
                $p = $conn->query("SELECT name FROM products WHERE id=" . intval($it['product_id']))->fetch_assoc();
                $pname = $p['name'] ?? 'Product';
            }
            $subtotal = $it['price'] * $it['quantity'];
            $total += $subtotal;
            $itemList .= "<tr>
                <td>{$pname}</td>
                <td>{$it['quantity']}</td>
                <td>₹" . number_format($it['price'], 2) . "</td>
                <td>₹" . number_format($subtotal, 2) . "</td>
            </tr>";
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denishsaliya@gmail.com';
            $mail->Password = 'byzr lpev fsbb fvvs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ Orders');
            $mail->addAddress($order['email'], $order['customer']);
            $mail->isHTML(true);

            $statusColor = [
                'Pending' => '#b07f35',
                'Processing' => '#c58b6a',
                'Shipped' => '#3c91e6',
                'Delivered' => '#2ea44f',
                'Cancelled' => '#912e2e'
            ][$status] ?? '#f7b47d';

            $subject = "Your GiftIQ Order #{$oid} is now {$status}";
            $body = "
                <p>Hi <strong>{$order['customer']}</strong>,</p>
                <p>Your order <strong>#{$oid}</strong> has been
                <strong style='color:{$statusColor}'>{$status}</strong>.</p>

                <h3 style='margin-top:20px;color:#f7b47d;'>Order Summary</h3>
                <table style='width:100%;border-collapse:collapse;margin-top:10px;'>
                    <thead>
                        <tr style='background:#111;color:#f7b47d;'>
                            <th align='left' style='padding:8px;'>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemList}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' align='right' style='padding:8px;font-weight:bold;color:#f7b47d;'>Total</td>
                            <td style='padding:8px;color:#fff;'>₹" . number_format($total, 2) . "</td>
                        </tr>
                    </tfoot>
                </table>

                <p style='margin-top:20px;'>We’ll keep you updated with further progress.</p>
                <p>Thank you for shopping with <strong>GiftIQ</strong> 🎁</p>
            ";

            $mail->Subject = $subject;
            $mail->Body = giftIQMailTemplate($subject, $body);
            $mail->send();
        } catch (Exception $e) {
            error_log("Email send error for Order #$oid: {$mail->ErrorInfo}");
        }
    }

    echo "<div class='card notice success'>✅ Order #$oid updated & customer notified.</div>";
}


if (isset($_GET['view'])) {
    $oid = intval($_GET['view']);
    $order = $conn->query("
        SELECT o.*, u.name AS customer, u.email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id=$oid
    ")->fetch_assoc();

    if (!$order) {
        echo "<div class='card'>Order not found.</div>";
        require 'admin_footer.php';
        exit;
    }

    $itemsRes = $conn->query("SELECT * FROM order_items WHERE order_id=$oid");
?>
<style>
.card {padding:20px;margin:20px 0;border-radius:12px;background:var(--card);}
.meta{display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;color:var(--muted);}
.meta div{background:var(--glass);padding:8px 10px;border-radius:8px;}
.notice.success{background:rgba(46,164,79,0.08);color:#dff3e0;padding:10px;border-radius:8px;}
.custom-edit {background:rgba(255,255,255,0.03);padding:15px;border-radius:10px;margin-top:10px;}
.custom-edit input[type=text]{width:100%;padding:8px;border-radius:6px;margin:4px 0;border:1px solid rgba(233,184,154,0.2);background:#111;color:#eee;}
.custom-edit label{font-weight:600;color:var(--accent-2);}
.custom-edit img{max-width:120px;border-radius:6px;margin-top:8px;}
.btn{display:inline-block;padding:8px 14px;border-radius:8px;font-weight:700;cursor:pointer;border:0;transition:all .2s}
.btn{background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#111;box-shadow:0 6px 18px rgba(197,139,106,0.18)}
.btn:hover{transform:translateY(-3px)}
.btn.ghost{background:transparent;color:var(--accent-2);border:1px solid rgba(233,184,154,0.08)}
select {
  background: var(--glass);
  color: #eee;
  border: 1px solid rgba(233,184,154,0.15);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
}
</style>

<div class="card">
  <h3>Order #<?= $order['id'] ?> — 
    <span class="status-badge <?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
  </h3>
  <div class="meta">
    <div><strong>Customer:</strong> <?= htmlspecialchars($order['customer']) ?></div>
    <div><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></div>
    <div><strong>Total:</strong> ₹<?= number_format($order['total'],2) ?></div>
    <div><strong>Date:</strong> <?= $order['created_at'] ?></div>
  </div>

  <form method="post" style="margin-top:12px;">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <label>Status</label>
    <select name="status">
      <?php foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
      <option <?= $order['status']==$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" name="update_status" type="submit">Update</button>
    <a class="btn ghost" href="manage_orders.php">Back</a>
  </form>

  <h4 style="margin-top:18px;">Items</h4>
  <?php while($it = $itemsRes->fetch_assoc()):
      $custom = !empty($it['custom_data']) ? json_decode($it['custom_data'], true) : null;
  ?>
  <div class="custom-edit">
    <h4><?= htmlspecialchars($custom ? "🎨 Custom Gift" : "🛍️ Standard Product") ?></h4>

    <?php if ($custom): ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="item_id" value="<?= $it['id'] ?>">
      <?php foreach($custom as $k=>$v): ?>
        <label><?= htmlspecialchars(ucwords($k)) ?>:</label>
        <input type="text" name="custom_fields[<?= htmlspecialchars($k) ?>]" value="<?= htmlspecialchars($v) ?>">
      <?php endforeach; ?>
    </form>
    <?php endif; ?>
  </div>
  <?php endwhile; ?>
</div>
<?php
require 'admin_footer.php';
exit;
}


$filter = $_GET['filter'] ?? 'all';
$whereSQL = '';

if ($filter === 'custom') {
    $whereSQL = "WHERE o.id IN (SELECT DISTINCT order_id FROM order_items WHERE custom_data IS NOT NULL AND custom_data != '')";
} elseif ($filter === 'standard') {
    $whereSQL = "WHERE o.id NOT IN (SELECT DISTINCT order_id FROM order_items WHERE custom_data IS NOT NULL AND custom_data != '')";
}

$sql = "SELECT o.id, o.created_at, o.total, o.status, u.name AS customer 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        $whereSQL 
        ORDER BY o.created_at DESC";
$res = $conn->query($sql);
?>
<style>
.table-compact {width:100%;border-collapse:collapse;font-size:0.95rem;border-radius:12px;overflow:hidden;background:rgba(255,255,255,0.02);box-shadow:0 0 18px rgba(0,0,0,0.4);}
.table-compact thead {background:linear-gradient(90deg,rgba(233,184,154,0.08),rgba(233,184,154,0.02));color:var(--accent-2);text-transform:uppercase;font-weight:600;}
.table-compact th, .table-compact td {padding:14px 16px;text-align:left;border-bottom:1px solid rgba(255,255,255,0.06);}
.table-compact tr:hover {background:rgba(233,184,154,0.06);}
.status-badge {padding:6px 12px;border-radius:8px;font-weight:600;font-size:0.85rem;color:#fff;}
.status-badge.pending{background:#b07f35;}
.status-badge.processing{background:#c58b6a;}
.status-badge.shipped{background:#3c91e6;}
.status-badge.delivered{background:#2ea44f;}
.status-badge.cancelled{background:#912e2e;}
.btn {display:inline-block;padding:6px 12px;border-radius:8px;font-weight:600;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#111;box-shadow:0 4px 12px rgba(197,139,106,0.2);}
.btn.ghost {background:rgba(233,184,154,0.08);color:var(--accent-2);border:1px solid rgba(233,184,154,0.15);}
.btn.active {background:linear-gradient(90deg,#f7b47d,#c58b6a);color:#111;font-weight:700;}
.filter-tabs {display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;}
</style>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
    <h3>📦 Manage Orders</h3>
  </div>

  <div class="filter-tabs">
    <a class="btn ghost <?= $filter=='all'?'active':'' ?>" href="?filter=all">All Orders</a>
    <a class="btn ghost <?= $filter=='custom'?'active':'' ?>" href="?filter=custom">🎨 Custom Orders</a>
    <a class="btn ghost <?= $filter=='standard'?'active':'' ?>" href="?filter=standard">🛍️ Standard Orders</a>
  </div>

  <table class="table-compact">
    <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($o = $res->fetch_assoc()): ?>
      <tr>
        <td>#<?= $o['id'] ?></td>
        <td><?= $o['created_at'] ?></td>
        <td><?= htmlspecialchars($o['customer'] ?: 'Guest') ?></td>
        <td>₹<?= number_format($o['total'],2) ?></td>
        <td><span class="status-badge <?= strtolower($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span></td>
        <td><a class="btn ghost" href="manage_orders.php?view=<?= $o['id'] ?>">View</a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require 'admin_footer.php'; ?>
