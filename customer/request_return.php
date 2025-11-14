<?php
session_start();
require __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

$mailTemplatePath = __DIR__ . '/../admin/mail_template.php';
if (file_exists($mailTemplatePath)) require_once $mailTemplatePath;

function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response($arr) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);

    $user_id = $_SESSION['user_id'] ?? null;

    $order_id = intval($_POST['order_id'] ?? ($_GET['order_id'] ?? 0));
    $order_item_id  = intval($_POST['item_id'] ?? 0); // map to order_item_id column
    $quantity = intval($_POST['quantity'] ?? 1);
    $reason   = trim($_POST['reason'] ?? '');
    $details  = trim($_POST['details'] ?? '');

    if ($order_id <= 0) {
        if (is_ajax()) json_response(['status'=>'error','message'=>'Invalid order.']);
        http_response_code(400);
        die('Invalid order.');
    }

    if ($user_id) {
        $stmt = $conn->prepare("SELECT id, user_id FROM orders WHERE id=? LIMIT 1");
        if (!$stmt) {
            $err = $conn->error;
            if (is_ajax()) json_response(['status'=>'error','message'=>'DB prepare failed: '.$err]);
            die('DB error: '.$err);
        }
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $ord = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$ord) {
            if (is_ajax()) json_response(['status'=>'error','message'=>'Order not found.']);
            die('Order not found.');
        }
        if (intval($ord['user_id']) !== intval($user_id)) {
            if (is_ajax()) json_response(['status'=>'error','message'=>'You cannot request return for this order.']);
            die('Permission denied.');
        }
    }

    $uploadedFiles = [];
    $uploadDir = __DIR__ . '/../uploads/returns/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $count = count($_FILES['images']['name']);
        for ($i=0;$i<$count && $i<3;$i++) {
            if (empty($_FILES['images']['name'][$i])) continue;
            $tmp = $_FILES['images']['tmp_name'][$i];
            $orig = basename($_FILES['images']['name'][$i]);
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
            $safe = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $dest = $uploadDir . $safe;
            if (move_uploaded_file($tmp, $dest)) {
                $uploadedFiles[] = 'uploads/returns/' . $safe;
            } else {
                error_log("Failed to move uploaded file: $tmp -> $dest");
            }
        }
    }

    $imgs = !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null;

    $sql = "INSERT INTO `order_returns` (order_id, order_item_id, user_id, quantity, reason, details, images, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Requested')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $err = $conn->error;
        error_log("Prepare failed: $err");
        if (is_ajax()) json_response(['status'=>'error','message'=>'DB prepare failed: '.$err]);
        die('DB prepare failed: '.$err);
    }

    $stmt->bind_param("iiiisss", $order_id, $order_item_id, $user_id, $quantity, $reason, $details, $imgs);
    $ok = $stmt->execute();
    if (!$ok) {
        $sErr = $stmt->error;
        error_log("Execute failed: $sErr -- SQL: $sql");
        if (is_ajax()) json_response(['status'=>'error','message'=>'DB execute failed: '.$sErr]);
        die('DB execute failed: '.$sErr);
    }
    $return_id = $stmt->insert_id;
    $stmt->close();

    if (!$ok) {
        if (is_ajax()) json_response(['status'=>'error','message'=>'Failed to save return request.']);
        die('Failed to save return request.');
    }

    $order = $conn->query("SELECT o.*, u.email AS user_email, u.name AS user_name FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.id=" . intval($order_id))->fetch_assoc();
    $user_email = $order['user_email'] ?? ($_POST['email'] ?? '');
    $user_name = $order['user_name'] ?? ($user_email);

    $admin_email = 'denishsaliya@gmail.com';

    $subject_admin = "Return request #{$return_id} — Order #{$order_id}";
    $body_admin = "<p>A new return request was submitted.</p>
      <ul>
        <li><strong>Return ID:</strong> {$return_id}</li>
        <li><strong>Order ID:</strong> {$order_id}</li>
        <li><strong>Customer:</strong> " . htmlspecialchars($user_name) . " (" . htmlspecialchars($user_email) . ")</li>
        <li><strong>Order Item ID:</strong> " . ($order_item_id ?: '&mdash;') . "</li>
        <li><strong>Quantity:</strong> " . intval($quantity) . "</li>
        <li><strong>Reason:</strong> " . htmlspecialchars($reason) . "</li>
        <li><strong>Details:</strong> " . nl2br(htmlspecialchars($details)) . "</li>
      </ul>";

    if (!empty($imgs)) {
        $body_admin .= "<p>Uploaded images:</p><ul>";
        foreach (explode(',', $imgs) as $im) {
            $url = rtrim((isset($_SERVER['HTTPS'])?'https://':'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']), '/') . '/' . ltrim($im, '/');
            $body_admin .= "<li><a href=\"" . htmlspecialchars($url) . "\" target=\"_blank\">" . htmlspecialchars($im) . "</a></li>";
        }
        $body_admin .= "</ul>";
    }

    $subject_cust = "We've received your return request (Order #{$order_id})";
    $body_cust = "<p>Hi " . htmlspecialchars($user_name) . ",</p>
      <p>We've received your return request for Order #{$order_id}. Our team will review it and contact you shortly.</p>
      <p><strong>Return ID:</strong> {$return_id}<br>
      <strong>Reason:</strong> " . htmlspecialchars($reason) . "</p>
      <p><p>Thank you for choosing <strong>GiftIQ</strong> — where every gift tells a story! 🎁.</p>";

    if (function_exists('giftIQMailTemplate')) {
        $mailBodyAdmin = giftIQMailTemplate($subject_admin, $body_admin);
        $mailBodyCust = giftIQMailTemplate($subject_cust, $body_cust);
    } else {
        $mailBodyAdmin = "<html><body>{$body_admin}</body></html>";
        $mailBodyCust  = "<html><body>{$body_cust}</body></html>";
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'denishsaliya@gmail.com';
        $mail->Password = 'byzr lpev fsbb fvvs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ');
        $mail->addAddress($admin_email);
        $mail->isHTML(true);
        $mail->Subject = $subject_admin;
        $mail->Body = $mailBodyAdmin;
        $mail->send();

        $mail->clearAllRecipients();
        $mail->addAddress($user_email ?: $admin_email);
        $mail->Subject = $subject_cust;
        $mail->Body = $mailBodyCust;
        $mail->send();
    } catch (Exception $e) {
        error_log("Return email error (return id {$return_id}): " . $e->getMessage());
    }

    if (is_ajax()) {
        json_response(['status'=>'success','message'=>'Return request submitted','return_id'=>$return_id]);
    } else {
        echo "<!doctype html><html><head><meta charset='utf-8'><title>Return submitted</title></head><body>";
        echo "<div style='max-width:700px;margin:3rem auto;font-family:Arial,sans-serif;'>";
        echo "<h2>Return request submitted</h2>";
        echo "<p>Return ID: <strong>{$return_id}</strong></p>";
        echo "<p>We will contact you shortly.</p>";
        echo "<p><a href='my_order.php'>Back to Orders</a></p>";
        echo "</div></body></html>";
    }
    exit;
}


$order_id = intval($_GET['order'] ?? ($_GET['order_id'] ?? 0));
if ($order_id <= 0) {
    if (is_ajax()) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<div class='card return-card'><div style='padding:12px;color:#f8b6b6'>Error: No order specified.</div></div>";
        exit;
    } else {
        die("Error: No order specified.");
    }
}

$stmt = $conn->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.id=? LIMIT 1");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    if (is_ajax()) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<div class='card return-card'><div style='padding:12px;color:#f8b6b6'>Error: Order not found.</div></div>";
        exit;
    } else {
        die("Order not found.");
    }
}

$items = [];
$r = $conn->query("SELECT oi.*, p.name AS product_name FROM order_items oi LEFT JOIN products p ON oi.product_id=p.id WHERE oi.order_id=" . intval($order_id));
while ($it = $r->fetch_assoc()) $items[] = $it;

ob_start();
?>

<style>
    /* ===========================
   Return Request — Dark Card
   Matches order page look: black + gold/pink accents
   Responsive & self-contained
   =========================== */

.return-card {
  background: linear-gradient(180deg, #0b0b0c 0%, #0f0f10 100%);
  border-radius: 12px;
  padding: 20px;
  color: #efe8df;
  box-shadow: 0 18px 40px rgba(3,3,3,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
  max-width: 900px;
  margin: 10px auto;
  font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* header/title */
.return-card h3 {
  margin: 0 0 10px;
  color: #f7b47d;           /* gold/pink */
  font-weight: 700;
  font-size: 1.25rem;
  letter-spacing: 0.2px;
}

/* small meta text */
.return-card p { color: #d8d0c8; margin: 6px 0 12px; font-size: 0.95rem; }

/* form layout */
.return-card form {
  display: block;
  gap: 12px;
}

/* labels */
.return-card label {
  display: block;
  color: #d8d0c8;
  font-weight: 600;
  margin: 10px 0 6px;
  font-size: 0.95rem;
}

/* selects / inputs / textarea */
.return-card select,
.return-card input[type="text"],
.return-card input[type="number"],
.return-card input[type="file"],
.return-card textarea {
  width: 100%;
  background: #0b0b0c;
  color: #efe8df;
  border: 1px solid rgba(255,255,255,0.04);
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: box-shadow .18s ease, transform .12s ease;
  box-shadow: 0 4px 14px rgba(0,0,0,0.6);
}

/* smaller inline inputs (quantity) */
.return-card .inline-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.return-card .inline-row input[type="number"] { width:96px; }

/* textarea sizing */
.return-card textarea { min-height:90px; resize:vertical; }

/* file input styling - hide default and show simple button */
.return-card input[type="file"] {
  padding: 6px 10px;
}

/* action buttons */
.return-card .btn-submit,
.return-card .btn-cancel,
.return-card .btn-outline {
  display:inline-block;
  padding: 8px 14px;
  border-radius: 8px;
  cursor:pointer;
  border: 0;
  font-weight:700;
  font-size:0.95rem;
  text-decoration:none;
  transition: transform .12s ease, box-shadow .14s ease, opacity .12s ease;
}

/* primary gradient button (gold/pink) */
.return-card .btn-submit {
  background: linear-gradient(135deg,#ffe6b3,#f7b4a3);
  color:#111;
  box-shadow: 0 6px 18px rgba(233,184,154,0.12);
}
.return-card .btn-submit:hover { transform: translateY(-3px); }

/* subtle ghost / cancel */
.return-card .btn-cancel,
.return-card .btn-outline {
  background: transparent;
  color: #c9b6a5;
  border: 1px solid rgba(233,184,154,0.06);
}
.return-card .btn-cancel:hover { background: rgba(255,255,255,0.02); color:#f7b47d; }

/* small link-like cancel */
.return-card a.cancel-link { color:#c9b6a5; text-decoration:underline; margin-left:8px; }

/* uploaded images list preview (if present) */
.return-card .uploaded-list { margin-top:8px; display:flex; gap:8px; flex-wrap:wrap; }
.return-card .uploaded-list img { width:64px; height:64px; object-fit:cover; border-radius:6px; border:1px solid rgba(255,255,255,0.04); }

/* success/error message block */
.return-card .msg {
  margin-top:12px;
  padding:10px 12px;
  border-radius:8px;
  font-weight:600;
}
.return-card .msg.success { background: rgba(46,164,79,0.08); color:#cdebd6; }
.return-card .msg.error   { background: rgba(210,76,76,0.08); color:#f6c6c6; }

/* small helper text */
.return-card .hint { color:#bdb1a3; font-size:0.88rem; margin-top:6px; }

/* RESPONSIVE tweaks */
@media (max-width: 900px) {
  .return-card { padding:16px; margin: 8px; }
  .return-card h3 { font-size:1.12rem; }
  .return-card .inline-row { gap:8px; }
}

@media (max-width: 600px) {
  .return-card { padding:14px; border-radius:10px; }
  .return-card .inline-row { flex-direction:column; align-items:stretch; }
  .return-card .inline-row input[type="number"] { width:100%; }
  .return-card .btn-submit { width:100%; text-align:center; }
  .return-card .btn-cancel { width:100%; margin-top:8px; text-align:center; }
}

/* Accessibility: focus outline */
.return-card select:focus,
.return-card input:focus,
.return-card textarea:focus,
.return-card .btn-submit:focus {
  box-shadow: 0 0 0 4px rgba(247,180,163,0.14);
  outline: none;
}

/* Keep modal-backdrop compatible (if your modal wrapper uses .modal/backdrop) */
#returnModalBackdrop { display:block; }
#returnModalBackdrop.open { display:block; }

</style>

<div class="card return-card" style="background:#0f0f10;padding:18px;border-radius:10px;color:#efe8df;">
  <h3 style="color:#f7b47d;margin-top:0;">Return Request — Order #<?= htmlspecialchars($order_id) ?></h3>
  <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?><br>
     <strong>Total:</strong> ₹<?= number_format($order['total'],2) ?></p>

  <form method="post" action="request_return.php" enctype="multipart/form-data" id="returnForm">
    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

    <label style="display:block;margin:10px 0 6px;">Select item to return</label>
    <select name="item_id" required style="padding:8px;border-radius:6px;background:#111;color:#fff;border:1px solid rgba(255,255,255,0.06);">
      <option value="">-- select item --</option>
      <?php foreach($items as $it): ?>
        <option value="<?= intval($it['product_id']) ?>"><?= htmlspecialchars($it['product_name'] ?: 'Item #' . intval($it['product_id'])) ?></option>
      <?php endforeach; ?>
    </select>

    <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
      <label style="margin:0;">Quantity</label>
      <input type="number" name="quantity" value="1" min="1" max="999" style="width:90px;padding:8px;border-radius:6px;background:#111;color:#fff;border:1px solid rgba(255,255,255,0.06);">
      <label style="margin:0 8px;">Reason</label>
      <select name="reason" required style="padding:8px;border-radius:6px;background:#111;color:#fff;border:1px solid rgba(255,255,255,0.06);">
        <option value="">-- select reason --</option>
        <option>Damaged / Defective</option>
        <option>Wrong item delivered</option>
        <option>Missing parts</option>
        <option>Changed my mind</option>
        <option>Other</option>
      </select>
    </div>

    <label style="display:block;margin-top:12px;">Details (optional)</label>
    <textarea name="details" rows="4" placeholder="Add more details..." style="width:100%;padding:8px;border-radius:6px;background:#111;color:#fff;border:1px solid rgba(255,255,255,0.06);"></textarea>

    <label style="display:block;margin-top:10px;">Upload images (optional, up to 3)</label>
    <input type="file" name="images[]" accept="image/*" multiple style="color:#fff;background:transparent;border:0">

    <div style="margin-top:12px;display:flex;gap:12px;align-items:center;">
      <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#ffe6b3,#f7d4d1);padding:8px 12px;border-radius:8px;border:0;cursor:pointer;">Submit Return Request</button>
      <a href="#" onclick="document.getElementById('returnModalBackdrop')?.classList.remove('open'); return false;" style="color:#bca2ff;text-decoration:underline;">Cancel</a>
    </div>
  </form>
</div>

<script>

(function(){
  var form = document.getElementById('returnForm');
  if (!form) return;
  form.addEventListener('submit', function(e){
    if (!window.fetch || !window.location || !window.opener && !window.parent) {
      return;
    }
    e.preventDefault();
    var btn = form.querySelector('button[type=submit]');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Submitting...';

    var data = new FormData(form);

    fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(function(r){ return r.json ? r.json() : r.text(); })
      .then(function(res){
        if (typeof res === 'object' && res.status === 'success') {
          form.outerHTML = '<div style="padding:12px;background:#0b0b0b;border-radius:8px;color:#d1ffd1;">✅ Return request submitted successfully. Return ID: ' + (res.return_id || '') + '</div>';
          setTimeout(function(){
            var b = document.getElementById('returnModalBackdrop');
            if (b) b.classList.remove('open');
          }, 1200);
        } else if (typeof res === 'object' && res.status === 'error') {
          alert(res.message || 'Error submitting return.');
          btn.disabled = false;
          btn.innerHTML = orig;
        } else {
          var html = typeof res === 'string' ? res : JSON.stringify(res);
          form.outerHTML = '<div style="padding:12px;background:#0b0b0b;border-radius:8px;color:#f8b6b6;">' + html + '</div>';
        }
      })
      .catch(function(err){
        alert('Submission failed. Please try again.');
        btn.disabled = false;
        btn.innerHTML = orig;
      });
  });
})();
</script>
<?php
$formHtml = ob_get_clean();

if (is_ajax()) {
    header('Content-Type: text/html; charset=utf-8');
    echo $formHtml;
    exit;
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Return Request — Order #<?= htmlspecialchars($order_id) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../customer/assets/main.css">
  <style>
    body { background: linear-gradient(135deg,#fff8f6,#ffeecb); font-family: 'Poppins',sans-serif; margin:0; padding:20px; color:#333; }
    .container { max-width:900px; margin:30px auto; }
  </style>
</head>
<body>
  <?php include __DIR__ . 'header.php'; ?>
  <div class="container">
    <?= $formHtml ?>
  </div>
  <?php include __DIR__ . 'footer.php'; ?>
</body>
</html>
