<?php
session_start();
include("header.php");

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // 🔴 Replace these with your Gmail credentials
            $mail->Username   = 'denishsaliya@gmail.com';   // your Gmail
            $mail->Password   = 'byzr lpev fsbb fvvs';     // Gmail App Password (see below)
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('yourgmail@gmail.com', 'GiftIQ Website');
            $mail->addAddress('yourgmail@gmail.com', 'Admin'); // where you’ll receive messages
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "New Contact Message from $name - GiftIQ";
            $mail->Body    = "
                <h2>New Inquiry from GiftIQ</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong></p>
                <p>{$message}</p>
                <hr><p style='color:#888;'>Sent from GiftIQ Website</p>
            ";

            $mail->send();
            $success = "✅ Thank you, $name! Your message has been sent successfully.";
        } catch (Exception $e) {
            $error = "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "⚠️ Please fill out all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - GiftIQ</title>
  <link rel="stylesheet" href="assets/contact.css">
</head>
<body>

<div class="contact-main-container fadeInUp">
  <!-- Left Section -->
  <section class="contact-info-panel">
    <h2>📍 Get in Touch</h2>
    <p>Have a question or feedback? We’re here to help you make gifting magical!</p>

    <div class="contact-details">
      <strong>📍 Address</strong>
      123 GiftIQ Street, Mumbai, India
    </div>

    <div class="contact-details">
      <strong>📞 Phone</strong>
      +91 98765 43210
    </div>

    <div class="contact-details">
      <strong>✉️ Email</strong>
      support@giftiq.com
    </div>

    <div class="contact-hours">
      🕒 <strong>Hours:</strong> Mon – Sat: 9:00 AM – 6:00 PM
    </div>
  </section>

  <!-- Right Section -->
  <section class="form-container">
    <h3>💌 Send Us a Message</h3>
    <p class="form-desc">Fill out the form below and we’ll respond soon.</p>

    <?php if ($success): ?>
      <div class="contact-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="contact-error"><?= $error ?></div>
    <?php endif; ?>

    <form class="contact-form" method="POST" action="">
      <input type="text" name="name" placeholder="Your Name *" required>
      <input type="email" name="email" placeholder="Your Email *" required>
      <input type="text" name="subject" placeholder="Subject">
      <textarea name="message" placeholder="Your Message *" rows="5" required></textarea>
      <button type="submit">Send Message ✨</button>
    </form>
  </section>
</div>

<div class="map-section fadeInUp">
  <iframe
    src="https://maps.google.com/maps?q=Surat&t=&z=13&ie=UTF8&iwloc=&output=embed"
    allowfullscreen="" loading="lazy"></iframe>
</div>

<?php include 'footer.php'; ?>



</body>
</html>
