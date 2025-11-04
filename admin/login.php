<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
  $stmt->bind_param("s",$username);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res && $res->num_rows === 1) {
    $admin = $res->fetch_assoc();
    if (hash('sha256', $password) === $admin['password_hash']){
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_user'] = $admin['username'];
      header("Location: dashboard.php");
      exit;
    } else $error = "Invalid credentials.";
  } else $error = "Invalid credentials.";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>GiftIQ Admin Login</title>

  <style>
    :root {
      --bg-dark: #0f0f0f;
      --card-bg: #181818;
      --accent: #f7b47d;
      --text-light: #eaeaea;
      --error: #ff6464;
    }

    body.login-body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top left, #1c1c1c, #000);
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      color: var(--text-light);
    }

    .login-container {
      background: var(--card-bg);
      padding: 40px 35px;
      border-radius: 14px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.05);
      width: 100%;
      max-width: 380px;
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    .login-container h2 {
      color: var(--accent);
      margin-bottom: 25px;
      letter-spacing: 1px;
    }

    .login-form input {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      background: #111;
      border: 1px solid #2a2a2a;
      border-radius: 8px;
      color: var(--text-light);
      font-size: 15px;
      outline: none;
      transition: border 0.2s, box-shadow 0.2s;
    }

    .login-form input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 6px rgba(247, 180, 125, 0.4);
    }

    .login-form button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      background: linear-gradient(90deg, var(--accent), #e87c7c);
      border: none;
      border-radius: 8px;
      font-size: 16px;
      color: #111;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .login-form button:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 18px rgba(247, 180, 125, 0.3);
    }

    .error-msg {
      background: rgba(255, 100, 100, 0.08);
      color: var(--error);
      border-left: 3px solid var(--error);
      padding: 10px;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 15px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
        border-radius: 10px;
      }
    }
  </style>
</head>

<body class="login-body">
  <div class="login-container">
    <h2>🔐 Admin Login</h2>
    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="login-form">
      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>
