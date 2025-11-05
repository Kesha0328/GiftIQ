<?php
session_start();
include '../config.php';

if (!isset($_GET['order_id'])) {
  header("Location: index.php");
  exit;
}

$order_id = intval($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Success - GiftIQ</title>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fff8f6, #fff2e1);
      text-align: center;
      overflow-x: hidden;
    }

    .success-box {
      max-width: 600px;
      margin: 100px auto;
      padding: 40px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1.2s ease;
      position: relative;
      z-index: 5;
    }

    h1 {
      color: #45c15f;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    p {
      color: #b67d5d;
      font-size: 1.1rem;
      margin: 5px 0;
    }

    a.btn-primary {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: linear-gradient(90deg, #e9b89a, #b67d5d);
      color: #fff;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(233, 184, 154, 0.3);
      transition: 0.3s ease;
    }

    a.btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(233, 184, 154, 0.4);
    }

    canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 10;
    }

    .emoji {
      position: fixed;
      font-size: 2rem;
      opacity: 0;
      animation: floatUp 4s ease-out forwards;
      z-index: 15;
    }

    @keyframes floatUp {
      0% { transform: translateY(0) scale(1); opacity: 1; }
      100% { transform: translateY(-200px) scale(1.5); opacity: 0; }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
      .success-box {
        margin: 60px 15px;
        padding: 30px 20px;
      }
      h1 { font-size: 1.6rem; }
      p { font-size: 1rem; }
      a.btn-primary { font-size: 0.95rem; padding: 10px 18px; }
    }
  </style>
</head>
<body>

  <canvas id="confettiCanvas"></canvas>

  <div class="success-box fadeInUp">
    <h1>🎉 Order Placed Successfully!</h1>
    <p>Your order <strong>#<?= $order_id; ?></strong> has been received.</p>
    <p>We’ll notify you once it’s processed and shipped.</p>
    <a href="print_invoice.php?order_id=<?= $order_id; ?>" class="btn-primary">🧾 View Invoice</a>
    <a href="/GiftIQ-main/index.php" class="btn-primary" style="margin-left:10px;">🏠 Back to Home</a>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    const canvas = document.getElementById('confettiCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const confetti = [];
    const colors = ['#f87171', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa', '#f472b6'];

    for (let i = 0; i < 120; i++) {
      const angle = Math.random() * 2 * Math.PI;
      const speed = Math.random() * 5 + 3;
      confetti.push({
        x: canvas.width / 2,
        y: canvas.height / 2,
        r: Math.random() * 5 + 2,
        dx: Math.cos(angle) * speed,
        dy: Math.sin(angle) * speed,
        color: colors[Math.floor(Math.random() * colors.length)],
        opacity: 1
      });
    }

    let duration = 0;
    function drawConfetti() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      confetti.forEach(p => {
        ctx.globalAlpha = p.opacity;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.fill();
      });

      confetti.forEach(p => {
        p.x += p.dx;
        p.y += p.dy;
        p.dy += 0.15;
        p.opacity -= 0.01;
      });

      duration++;
      if (duration < 300) requestAnimationFrame(drawConfetti);
      else ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    drawConfetti();


  </script>

</body>
</html>
