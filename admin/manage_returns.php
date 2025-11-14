<?php
require 'admin_header.php';
require 'mail_template.php';
    

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$usePHPMailer = file_exists(__DIR__ . '/../PHPMailer/src/PHPMailer.php');

if ($usePHPMailer) {
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

}

function sendReturnEmail($toEmail, $toName, $subject, $bodyHtml) {
    global $usePHPMailer;
    if ($usePHPMailer) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denishsaliya@gmail.com';
            $mail->Password = 'byzr lpev fsbb fvvs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ Admin');
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $bodyHtml;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer error sending return email: {$mail->ErrorInfo}");
            return false;
        }
    } else {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: GiftIQ Admin <no-reply@localhost>\r\n";
        return mail($toEmail, $subject, $bodyHtml, $headers);
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    if (!in_array($action, ['approve','reject'])) {
        $action = null;
    }

    if ($action) {
        $q = $conn->prepare("SELECT r.*, u.email, u.name AS customer_name, o.total, o.id AS order_id
                            FROM order_returns r
                            LEFT JOIN users u ON r.user_id = u.id
                            LEFT JOIN orders o ON r.order_id = o.id
                            WHERE r.id = ? LIMIT 1");
        $q->bind_param('i', $id);
        $q->execute();
        $res = $q->get_result();
        if ($res && $res->num_rows) {
            $row = $res->fetch_assoc();
            $newStatus = $action === 'approve' ? 'Approved' : 'Rejected';
            $admin_note = $conn->real_escape_string($_GET['admin_note'] ?? ($newStatus === 'Approved' ? 'Your return has been approved.' : 'Your return request has been rejected.'));
            $now = date('Y-m-d H:i:s');

            $upd = $conn->prepare("UPDATE order_returns SET status = ?, admin_note = ?, updated_at = ? WHERE id = ?");
            $upd->bind_param('sssi', $newStatus, $admin_note, $now, $id);
            $ok = $upd->execute();

            // ---- REPLACE FROM HERE ----
if ($ok) {
    $subject = "Return request #{$row['id']} - {$newStatus}";

    // Build main message (plain HTML fragment)
    $msg  = "<p>Hi <strong>" . htmlspecialchars($row['customer_name']) . "</strong>,</p>";
    $msg .= "<p>Your return request for <strong>Order #{$row['order_id']}</strong> has been <strong>{$newStatus}</strong>.</p>";

    $msg .= "<table style='width:100%;border-collapse:collapse;margin-top:12px;font-size:14px;'>";
    $msg .= "<tr><td style='padding:8px;border:1px solid #eee;'><strong>Return ID</strong></td><td style='padding:8px;border:1px solid #eee;'>#" . intval($row['id']) . "</td></tr>";
    $msg .= "<tr><td style='padding:8px;border:1px solid #eee;'><strong>Order ID</strong></td><td style='padding:8px;border:1px solid #eee;'>#" . intval($row['order_id']) . "</td></tr>";
    $msg .= "<tr><td style='padding:8px;border:1px solid #eee;'><strong>Quantity</strong></td><td style='padding:8px;border:1px solid #eee;'>" . intval($row['quantity']) . "</td></tr>";
    $msg .= "<tr><td style='padding:8px;border:1px solid #eee;'><strong>Reason</strong></td><td style='padding:8px;border:1px solid #eee;'>" . htmlspecialchars($row['reason']) . "</td></tr>";
    $msg .= "</table>";

    if (!empty($row['details'])) {
        $msg .= "<p style='margin-top:12px;'><strong>Details</strong></p>";
        $msg .= "<div style='background:#fafafa;padding:10px;border-left:4px solid #c87941;'>" . nl2br(htmlspecialchars($row['details'])) . "</div>";
    }

    $imagesHtml = '';
    if (!empty($row['images'])) {
        $imgs = @json_decode($row['images'], true);
        if (!is_array($imgs)) {
            $imgs = array_filter(array_map('trim', explode(',', $row['images'])));
        }
        if (!empty($imgs)) {
            $imagesHtml .= "<p style='margin-top:12px;'><strong>Uploaded images</strong></p><div style='display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;'>";
            foreach ($imgs as $im) {
                $safe = ltrim($im, "/\\"); 
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                if (strpos($safe, 'http://') === 0 || strpos($safe, 'https://') === 0) {
                    $imgUrl = $safe;
                } else {
                    if (strpos($safe, 'uploads/') !== false) {
                        $imgUrl = $protocol . $host . '/' . $safe;
                    } else {
                        $imgUrl = $protocol . $host . '/GiftIQ-main/uploads/returns/' . rawurlencode(basename($safe));
                    }
                }
                $imagesHtml .= "<a href=\"" . htmlspecialchars($imgUrl) . "\" target=\"_blank\" style='display:inline-block'><img src=\"" . htmlspecialchars($imgUrl) . "\" alt=\"return-image\" style='width:120px;border-radius:8px;border:1px solid #ddd;object-fit:cover;margin-bottom:6px;'></a>";
            }
            $imagesHtml .= "</div>";
        }
    }

    if ($imagesHtml) $msg .= $imagesHtml;

    $msg .= "<p style='margin-top:12px;'><strong>Admin note:</strong><br>" . nl2br(htmlspecialchars($admin_note)) . "</p>";

    if (function_exists('giftIQMailTemplate')) {
        $mailBody = giftIQMailTemplate($subject, $msg);
    } else {
        $mailBody = "<html><body>" . $msg . "</body></html>";
    }

    sendReturnEmail($row['email'], $row['customer_name'], $subject, $mailBody);

    $_SESSION['notice'] = "Return request #{$row['id']} marked as {$newStatus} and customer notified.";

            } else {
                $_SESSION['error'] = "Failed to update return request: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "Return request not found.";
        }

        header("Location: manage_returns.php");
        exit;
    }
}

$filters = [];
$params = [];
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $filters[] = "r.status = '{$status}'";
}
if (!empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $filters[] = "(u.name LIKE '%{$q}%' OR r.reason LIKE '%{$q}%' OR r.details LIKE '%{$q}%' OR r.id LIKE '%{$q}%')";
}
$whereSQL = count($filters) ? implode(' AND ', $filters) : '1';

$sql = "SELECT r.*, u.name AS customer_name, o.total AS order_total, o.status AS order_status
        FROM order_returns r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN orders o ON r.order_id = o.id
        WHERE {$whereSQL}
        ORDER BY r.created_at DESC";

$res = $conn->query($sql);
if (!$res) {
    echo "<div class='card notice error'>DB error: " . htmlspecialchars($conn->error) . "</div>";
}

?>
<style>
.card { padding:20px; margin:20px 0; border-radius:12px; background:var(--card); }
.header-bar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.table { width:100%; border-collapse:collapse; margin-top:10px; color:#e7e7e7;}
.table th { text-align:left; padding:12px; color:var(--accent-2); border-bottom:1px solid rgba(255,255,255,0.04); }
.table td { padding:10px; border-bottom:1px dashed rgba(255,255,255,0.03); vertical-align:middle; }
.img-thumb { width:80px; height:60px; object-fit:cover; border-radius:8px; }
.badge { padding:6px 10px; border-radius:8px; font-weight:700; font-size:0.85rem; color:#fff; }
.badge.requested { background:#b07f35; }
.badge.approved { background:#2ea44f; }
.badge.rejected { background:#912e2e; }
.btn { display:inline-block; padding:8px 12px; border-radius:8px; font-weight:700; text-decoration:none; color:#111; background:linear-gradient(90deg,var(--accent),var(--accent-2)); box-shadow:0 6px 18px rgba(197,139,106,0.12); }
.btn.ghost { background:transparent; color:var(--accent-2); border:1px solid rgba(233,184,154,0.08); }
.btn.danger { background:linear-gradient(90deg,#b33b3b,#d24c4c); color:#fff; }
.actions a { margin-right:8px; }
.notice { padding:10px; border-radius:8px; margin-bottom:12px; }
.notice.success { background:rgba(46,164,79,0.08); color:#dff3e0; }
.notice.error { background:rgba(210,76,76,0.08); color:#fbdcdc; }
.small { font-size:0.9rem; color:#cfc6c0; }
.view-img { width:110px; height:80px; object-fit:cover; border-radius:6px; margin-right:6px; border:1px solid rgba(255,255,255,0.04); }
@media (max-width:760px) {
  .table th, .table td { font-size:0.9rem; padding:8px; }
  .img-thumb{ width:64px; height:48px; }
}
</style>

<div class="card">
  <div class="header-bar">
    <h3>📥 Manage Return Requests</h3>
    <div>
      <form method="get" style="display:flex;gap:8px;align-items:center;">
        <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="padding:8px;border-radius:8px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);color:#fff;">
        <select name="status" style="padding:8px;border-radius:8px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);color:#fff;">
          <option value="">All statuses</option>
          <?php foreach(['Requested','Approved','Rejected','Received'] as $s): ?>
            <option value="<?= $s ?>" <?= (isset($_GET['status']) && $_GET['status']==$s)?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn ghost" type="submit">Filter</button>
        <a class="btn ghost" href="manage_returns.php">Reset</a>
      </form>
    </div>
  </div>

  <?php if (!empty($_SESSION['notice'])): ?>
    <div class="notice success"><?= htmlspecialchars($_SESSION['notice']); unset($_SESSION['notice']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['error'])): ?>
    <div class="notice error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <table class="table">
    <thead>
      <tr><th>ID</th><th>Order</th><th>Customer</th><th>Qty</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
<?php if ($res && $res->num_rows): ?>
  <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td>#<?= intval($r['id']) ?></td>
      <td>
        <div class="small">Order #<?= intval($r['order_id']) ?></div>
        <div class="small">Total: ₹<?= number_format($r['order_total'],2) ?></div>
      </td>
      <td><?= htmlspecialchars($r['customer_name']) ?></td>
      <td><?= intval($r['quantity']) ?></td>
      <td style="max-width:260px;white-space:normal;"><?= htmlspecialchars($r['reason']) ?></td>
      <td>
        <?php
          $sclass = strtolower($r['status']);
          $sclass = preg_replace('/[^a-z]/','',$sclass);
        ?>
        <span class="badge <?= $sclass ?>"><?= htmlspecialchars($r['status']) ?></span>
      </td>
      <td><?= htmlspecialchars($r['created_at']) ?></td>
      <td class="actions">
        <a class="btn ghost" href="manage_returns.php?view=<?= $r['id'] ?>">View</a>

        <?php if ($r['status'] !== 'Approved'): ?>
          <a class="btn" href="manage_returns.php?action=approve&id=<?= $r['id'] ?>" onclick="return confirm('Approve this return request?')">Approve</a>
        <?php endif; ?>

        <?php if ($r['status'] !== 'Rejected'): ?>
          <a class="btn danger" href="manage_returns.php?action=reject&id=<?= $r['id'] ?>" onclick="return confirm('Reject this return request?')">Reject</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="8" class="small">No return requests found.</td></tr>
<?php endif; ?>
    </tbody>
  </table>
</div>

<?php
if (isset($_GET['view'])) {
    $vid = intval($_GET['view']);
    $q = $conn->prepare("SELECT r.*, u.name AS customer_name, u.email, o.total AS order_total
                        FROM order_returns r
                        LEFT JOIN users u ON r.user_id = u.id
                        LEFT JOIN orders o ON r.order_id = o.id
                        WHERE r.id = ? LIMIT 1");
    $q->bind_param('i', $vid);
    $q->execute();
    $rv = $q->get_result();
    if ($rv && $rv->num_rows) {
        $row = $rv->fetch_assoc();
        ?>
        <div class="card" style="margin-top:16px;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <h3>Return Request — #<?= intval($row['id']) ?></h3>
            <div class="small">Status: <strong><?= htmlspecialchars($row['status']) ?></strong></div>
          </div>

          <p class="small"><strong>Order:</strong> #<?= intval($row['order_id']) ?> — ₹<?= number_format($row['order_total'],2) ?></p>
          <p class="small"><strong>Customer:</strong> <?= htmlspecialchars($row['customer_name']) ?> (<?= htmlspecialchars($row['email']) ?>)</p>
          <p class="small"><strong>Quantity:</strong> <?= intval($row['quantity']) ?></p>
          <p class="small"><strong>Reason:</strong> <?= htmlspecialchars($row['reason']) ?></p>
          <?php if (!empty($row['details'])): ?>
            <p class="small"><strong>Details:</strong><br><?= nl2br(htmlspecialchars($row['details'])) ?></p>
          <?php endif; ?>

          <?php if (!empty($row['images'])): 
              $imgs = json_decode($row['images'], true);
              if (!is_array($imgs)) $imgs = [];
          ?>
            <div style="margin-top:10px;">
              <strong class="small">Images:</strong>
              <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                <?php foreach($imgs as $im): ?>
                  <?php $safe = htmlspecialchars($im); ?>
                  <a href="/GiftIQ-main/uploads/returns/<?= $safe ?>" target="_blank"><img class="view-img" src="/GiftIQ-main/uploads/returns/<?= $safe ?>" alt=""></a>
                <?php endforeach; ?>
              </div>
            </div>  
          <?php endif; ?>

          <?php if (!empty($row['admin_note'])): ?>
            <p class="small" style="margin-top:10px;"><strong>Admin note:</strong><br><?= nl2br(htmlspecialchars($row['admin_note'])) ?></p>
          <?php endif; ?>

          <div style="margin-top:12px;">
            <a class="btn ghost" href="manage_returns.php">Close</a>
            <?php if ($row['status'] !== 'Approved'): ?>
              <a class="btn" href="manage_returns.php?action=approve&id=<?= $row['id'] ?>" onclick="return confirm('Approve this return request?')">Approve</a>
            <?php endif; ?>
            <?php if ($row['status'] !== 'Rejected'): ?>
              <a class="btn danger" href="manage_returns.php?action=reject&id=<?= $row['id'] ?>" onclick="return confirm('Reject this return request?')">Reject</a>
            <?php endif; ?>
          </div>
        </div>
        <?php
    } else {
        echo "<div class='card notice error'>Return request not found.</div>";
    }
}

require 'admin_footer.php';
