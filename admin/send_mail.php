<?php
require 'admin_header.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require 'mail_template.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = trim($_POST['to']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($to && $subject && $message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denishsaliya@gmail.com';
            $mail->Password = 'byzr lpev fsbb fvvs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ Admin');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = giftIQMailTemplate($subject, nl2br(htmlspecialchars($message)));

            $mail->send();
            $success = "✅ Email sent successfully to <strong>$to</strong>!";
        } catch (Exception $e) {
            $error = "❌ Mail failed: {$mail->ErrorInfo}";
        }
    } else {
        $error = "⚠️ Please fill out all required fields.";
    }
}
?>

<style>
.card {
  background: #181818;
  border-radius: 12px;
  padding: 20px;
  color: #eee;
  margin: 30px auto;
  max-width: 600px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}
h3 {
  color: #f7b47d;
  margin-bottom: 16px;
}
.form-row {
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
}
label {
  color: #ccc;
  font-size: 0.95rem;
  margin-bottom: 6px;
}
input[type="text"],
input[type="email"],
textarea {
  background: #111;
  color: #eee;
  border: 1px solid #333;
  border-radius: 8px;
  padding: 10px;
  width: 100%;
  resize: vertical;
  font-size: 0.95rem;
  transition: 0.2s;
}
input:focus,
textarea:focus {
  border-color: #f7b47d;
  outline: none;
}
button {
  background: linear-gradient(90deg,#f7b47d,#fbcaa2);
  color: #111;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  padding: 10px 18px;
  cursor: pointer;
  transition: 0.2s;
}
button:hover {
  transform: translateY(-2px);
  background: linear-gradient(90deg,#f9c38b,#f7b47d);
}
.notice {
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 10px;
}
.notice.success {
  background: rgba(46,164,79,0.06);
  color: #b9e4b1;
}
.notice.error {
  background: rgba(255,0,0,0.06);
  color: #ffb3b3;
}
</style>

<div class="card">
  <h3>✉️ Send Custom Email</h3>

  <?php if ($success): ?><div class="notice success"><?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="notice error"><?= $error ?></div><?php endif; ?>

  <form method="POST">
    <div class="form-row">
      <label>Recipient Email</label>
      <input type="email" name="to" placeholder="Enter recipient email" required>
    </div>

    <div class="form-row">
      <label>Subject</label>
      <input type="text" name="subject" placeholder="Subject" required>
    </div>

    <div class="form-row">
      <label>Message</label>
      <textarea name="message" rows="6" placeholder="Write your message..." required></textarea>
    </div>

    <button type="submit">Send Email</button>
  </form>
</div>

<?php require 'admin_footer.php'; ?>
