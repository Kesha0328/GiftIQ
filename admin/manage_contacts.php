<?php
require 'admin_header.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require 'mail_template.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'], $_POST['contact_id'])) {
    $contact_id = intval($_POST['contact_id']);
    $reply_text = trim($_POST['reply_text']);

    $cinfo = $conn->query("SELECT * FROM contacts WHERE id=$contact_id")->fetch_assoc();
    if ($cinfo && $reply_text) {
        $stmt = $conn->prepare("INSERT INTO contact_replies (contact_id, admin_name, message, created_at) VALUES (?, ?, ?, NOW())");
        $admin_name = $_SESSION['admin_user'] ?? 'Admin';
        $stmt->bind_param("iss", $contact_id, $admin_name, $reply_text);
        $stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denishsaliya@gmail.com';
            $mail->Password = 'byzr lpev fsbb fvvs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ Support');
            $mail->addAddress($cinfo['email'], $cinfo['name']);
            $mail->isHTML(true);

            $subject = "Reply from GiftIQ Support";
            $content = nl2br(htmlspecialchars($reply_text));
            $mail->Subject = $subject;
            $mail->Body = giftIQMailTemplate($subject, $content);

            $mail->send();
            echo "<div class='notice success'>✅ Reply sent to {$cinfo['email']}.</div>";
        } catch (Exception $e) {
            echo "<div class='notice error'>❌ Mail Error: {$mail->ErrorInfo}</div>";
        }
    }
}

$res = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>

<style>
.card{ padding:20px; border-radius:10px; background:#181818; margin:20px 0; color:#eee; }
h3{color:#f7b47d;}
.btn{padding:6px 12px; border-radius:6px; cursor:pointer; text-decoration:none;}
.btn.reply{background:linear-gradient(90deg,#f7b47d,#fbcaa2);color:#111;}
textarea{width:100%;border-radius:8px;padding:8px;background:#111;border:1px solid #333;color:#eee;resize:vertical;}
.notice{margin-bottom:10px;padding:10px;border-radius:8px;}
.notice.success{background:rgba(46,164,79,0.06);color:#b9e4b1;}
.notice.error{background:rgba(255,0,0,0.06);color:#ffb3b3;}
.reply-box{margin-top:10px;border-top:1px dashed #333;padding-top:10px;}
</style>

<div class="card">
  <h3>📬 Manage Contact Messages</h3>

  <?php while($c = $res->fetch_assoc()): ?>
    <div style="border-bottom:1px dashed #333; padding:10px 0;">
      <p><strong><?= htmlspecialchars($c['name']) ?></strong> (<?= htmlspecialchars($c['email']) ?>)</p>
      <p><?= nl2br(htmlspecialchars($c['message'])) ?></p>
      <small style="color:#888;">Sent: <?= $c['created_at'] ?></small>

      <?php
        $replies = $conn->query("SELECT * FROM contact_replies WHERE contact_id=".$c['id']." ORDER BY created_at ASC");
        while($r = $replies->fetch_assoc()):
      ?>
        <div class="reply-box">
          <strong style="color:#f7b47d;">Reply from <?= htmlspecialchars($r['admin_name']) ?>:</strong>
          <div><?= nl2br(htmlspecialchars($r['message'])) ?></div>
          <small style="color:#777;"><?= $r['created_at'] ?></small>
        </div>
      <?php endwhile; ?>

      <form method="POST" style="margin-top:10px;">
        <input type="hidden" name="contact_id" value="<?= $c['id'] ?>">
        <textarea name="reply_text" placeholder="Write a reply..." required></textarea>
        <button class="btn reply" type="submit">Send Reply</button>
      </form>
    </div>
  <?php endwhile; ?>
</div>

<?php require 'admin_footer.php'; ?>
