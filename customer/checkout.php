<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
  exit;
}

if (empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['shipping'] = [
    'name' => trim($_POST['name']),
    'phone' => trim($_POST['phone']),
    'address' => trim($_POST['address']),
    'city' => trim($_POST['city']),
    'postal_code' => trim($_POST['postal_code']),
    'country' => trim($_POST['country'])
  ];

  header("Location: payment.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - GiftIQ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <style>
    :root {
      --accent-pink: #f7d4d1;
      --accent-gold: #ffe6b3;
      --accent-text: #d47474;
      --white: #fff;
      --shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fff8f6, #ffeecb);
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .checkout-card {
      width: 100%;
      max-width: 600px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 2.5rem;
      animation: fadeInUp 0.8s ease;
    }

    h1 {
      text-align: center;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--accent-text);
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: var(--accent-text);
    }

    input, textarea {
      width: 100%;
      padding: 0.9rem;
      border: 2px solid #f3dede;
      border-radius: 10px;
      background: #fffdfb;
      font-size: 1rem;
      margin-bottom: 1rem;
      transition: border 0.3s ease;
    }

    input:focus, textarea:focus {
      border-color: var(--accent-pink);
      outline: none;
      box-shadow: 0 0 5px rgba(247, 212, 209, 0.5);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .btn-primary {
      width: 100%;
      background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(212, 116, 116, 0.15);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
    }

    @media (max-width: 768px) {
      main {
        padding: 1.5rem;
      }

      .checkout-card {
        padding: 1.5rem;
        border-radius: 16px;
      }

      h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }

      input, textarea {
        font-size: 0.95rem;
        padding: 0.8rem;
      }

      .btn-primary {
        padding: 0.9rem;
        font-size: 1rem;
      }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="checkout-card fadeInUp">
    <h1>🚚 Shipping Details</h1>
    <form method="post">
      <label>Full Name</label>
      <input type="text" name="name" required>

      <label>Phone</label>
      <input type="text" name="phone" required>

      <label>Address</label>
      <textarea name="address" rows="3" required></textarea>

      <label>City</label>
      <input type="text" name="city" required>

      <label>Postal Code</label>
      <input type="text" name="postal_code" required>

      <label>Country</label>
      <input type="text" name="country" required value="India">

      <button type="submit" class="btn-primary">Proceed to Payment 💳</button>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
