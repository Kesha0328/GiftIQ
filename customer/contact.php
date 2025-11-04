<?php
session_start();
include("header.php");
include __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../admin/mail_template.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            $stmt->execute();
            $stmt->close();
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

            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ Website');
            $mail->addAddress('denishsaliya@gmail.com', 'GiftIQ Admin');
            $mail->addReplyTo($email, $name);

            $content = '
              <p><strong>Name:</strong> '.htmlspecialchars($name).'</p>
              <p><strong>Email:</strong> '.htmlspecialchars($email).'</p>
              <p><strong>Subject:</strong> '.htmlspecialchars($subject).'</p>
              <p><strong>Message:</strong></p>
              <p style="border-left:3px solid #f7b47d;padding-left:10px;">'.nl2br(htmlspecialchars($message)).'</p>
            ';

            $mail->isHTML(true);
            $mail->Subject = "New Inquiry from $name - GiftIQ";
            $mail->Body    = giftIQMailTemplate("New Inquiry from $name", $content);
            $mail->send();

            $success = "✅ Thank you, " . htmlspecialchars($name) . "! Your message has been sent successfully.";
        } catch (Exception $e) {

          $error = "❌ Network error. Please try again later.";
        }
    } else {
        $error = "⚠️ Please fill out all required fields.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Contact Us - GiftIQ</title>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    :root{
      --bg: linear-gradient(135deg,#fff8f6,#ffeecb);
      --card:#fff;
      --accent: #f7d4d1;
      --accent-2:#ffe6b3;
      --accent-text:#d47474;
      --muted:#666;
      --shadow: 0 8px 24px rgba(0,0,0,0.06);
      --radius: 14px;
    }
    *{box-sizing:border-box}
    html,body{height:100%;}
    body{
      margin:0;
      font-family:'Poppins',sans-serif;
      background:var(--bg);
      color:#333;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .page-title {
      text-align:center;
      margin: 2rem 0 1rem;
    }
    .page-title h1{
      margin:0;
      font-size:1.5rem;
      color:var(--accent-text);
      background: linear-gradient(90deg,#f4b8b4,#ffd9a0);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      font-weight:700;
    }

    .contact-wrap{
      width:92%;
      max-width:1100px;
      margin: 0 auto 3rem;
      display:flex;
      gap:1.8rem;
      align-items:flex-start;
      padding: 1.2rem;
    }

    .card {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding:1.6rem;
    }

    .form-panel{
      flex:1 1 55%;
      min-width:280px;
    }
    .form-panel h2{ color:var(--accent-text); margin:0 0 8px; font-size:1.05rem}
    .form-desc{ color:var(--muted); margin-bottom:12px; }

    .form-row { display:flex; flex-direction:column; gap:0.75rem; }

    .form-row input[type="text"],
    .form-row input[type="email"],
    .form-row textarea {
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:2px solid #f3dede;
      background:#fffdfb;
      font-size:1rem;
      resize:vertical;
    }
    .form-row textarea{ min-height:110px; }

    .submit-btn {
      display:inline-block;
      width:100%;
      padding:12px;
      border-radius:12px;
      border:0;
      color:#fff;
      font-weight:700;
      cursor:pointer;
      background: linear-gradient(90deg,var(--accent),var(--accent-2));
      margin-top:8px;
      box-shadow: 0 6px 18px rgba(247,180,163,0.12);
    }
    .submit-btn:active{ transform: translateY(1px); }

    .info-panel{
      flex:1 1 38%;
      min-width:260px;
    }
    .info-panel h3{ color:var(--accent-text); margin:0 0 8px; font-size:1rem; }
    .info-list p{ margin:8px 0; color:var(--muted); line-height:1.3; }

    .alert {
      padding:10px 12px;
      border-radius:8px;
      margin-bottom:12px;
      font-weight:600;
    }
    .success{ background:#d4edda; color:#155724; }
    .error{ background:#f8d7da; color:#721c24; }

    .map-wrap{ width:100%; text-align:center; margin-top:1.2rem; }
    .map-wrap iframe{
      width:100%;
      max-width:720px;
      height:320px;
      border:0;
      border-radius:10px;
    }

    .page-bottom-spacer{
      height:110px;
    }

    @media (max-width: 768px){
      .contact-wrap{
        flex-direction:column;
        padding: 1rem;
        gap:1rem;
      }

      .form-panel{ order: 1; }
      .info-panel{ order: 2; }
      .map-wrap{ order: 3; }

      .form-panel, .info-panel { width:100%; box-shadow: var(--shadow); padding:1rem; border-radius:12px; }
      .map-wrap iframe { height:260px; }

      body { padding-bottom: 120px; }

      .page-title h1{ font-size:1.25rem; margin-top:0.6rem; }
      .form-row textarea{ min-height:120px; }
    }

    @media (max-width:420px){
      .form-row input[type="text"], .form-row input[type="email"] { padding:10px; font-size:0.95rem; }
      .submit-btn { padding:11px; font-size:0.98rem; }
    }
  </style>
</head>
<body>

<div class="page-title">
  <h1>📞 Contact Us</h1>
</div>

<div class="contact-wrap">

  <section class="card form-panel" aria-labelledby="contact-form-title">
    <h2 id="contact-form-title">💌 Send Us a Message</h2>
    <p class="form-desc">Fill out the form below and we’ll respond soon.</p>

    <?php if ($success): ?>
      <div class="alert success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="" novalidate aria-label="Contact form">
      <div class="form-row">
        <label class="sr-only" for="name">Your name</label>
        <input id="name" name="name" type="text" placeholder="Your Name *" required aria-required="true">

        <label class="sr-only" for="email">Your email</label>
        <input id="email" name="email" type="email" placeholder="Your Email *" required aria-required="true">

        <label class="sr-only" for="subject">Subject</label>
        <input id="subject" name="subject" type="text" placeholder="Subject">

        <label class="sr-only" for="message">Message</label>
        <textarea id="message" name="message" placeholder="Your Message *" required aria-required="true"></textarea>

        <button type="submit" class="submit-btn" aria-label="Send Message">✨ Send Message</button>
      </div>
    </form>
  </section>

  <aside class="card info-panel" aria-labelledby="contact-info-title">
    <h3 id="contact-info-title">📍 Get in Touch</h3>
    <div class="info-list">
      <p>Have questions or feedback? We’d love to hear from you!</p>

      <p><strong>📍 Address</strong><br>123 GiftIQ Street, Mumbai, India</p>
      <p><strong>📞 Phone</strong><br>+91 98765 43210</p>
      <p><strong>✉️ Email</strong><br>support@giftiq.com</p>
      <p><strong>🕒 Hours</strong><br>Mon – Sat: 9:00 AM – 6:00 PM</p>
    </div>
  </aside>

</div>



<div class="page-bottom-spacer" aria-hidden="true"></div>

<?php include 'footer.php'; ?>

<script>
  (function(){
    const iframe = document.querySelector('.map-wrap iframe');
    if(!iframe) return;

    iframe.style.pointerEvents = 'auto';

    iframe.addEventListener('touchstart', function(){ iframe.style.pointerEvents='auto'; }, {passive:true});
  })();
</script>

</body>
</html>
