<<?php
ob_start(); // ✅ Add output buffering to prevent header issues
if (session_status() === PHP_SESSION_NONE) session_start();

include __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin • GiftIQ</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;">

  <style>
    :root {
      --bg: #0e0f11;
      --panel: #14161a;
      --accent: #c58b6a;
      --accent-2: #e9b89a;
      --muted: #a6a6a6;
      --text: #f2f2f2;
      --glass: rgba(255, 255, 255, 0.04);
      --radius: 12px;
      --shadow: 0 4px 30px rgba(0, 0, 0, 0.6);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Inter", "Segoe UI", sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: linear-gradient(180deg, #0b0c0e, #0f1115 100%);
      color: var(--text);
      overflow-x: hidden;
    }

    .sidebar {
      width: 250px;
      background: var(--panel);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      box-shadow: var(--shadow);
      z-index: 100;
      transition: width 0.25s ease, transform 0.3s ease;
    }

    .sidebar .brand {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar .brand img {
      width: 130px;
      height: auto;
      margin-bottom: 10px;
      filter: drop-shadow(0 0 10px rgba(197,139,106,0.4));
    }

    .sidebar .brand span {
      font-weight: 700;
      color: var(--accent-2);
      font-size: 1.2rem;
    }

    .sidebar ul {
      list-style: none;
      width: 100%;
      padding: 0 16px;
    }

    .sidebar ul li {
      margin: 8px 0;
    }

    .sidebar ul li a {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: #ddd;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 10px 14px;
      border-radius: 8px;
      transition: all 0.25s ease;
    }

    .sidebar ul li a .icon {
      font-size: 1.1rem;
      min-width: 28px;
      text-align: center;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
      background: linear-gradient(90deg, rgba(197,139,106,0.15), rgba(233,184,154,0.1));
      color: var(--accent-2);
      transform: translateX(3px);
    }

    .sidebar.collapsed {
      width: 78px;
    }

    .sidebar.collapsed ul li a .label {
      display: none;
    }

    .sidebar.collapsed .brand span {
      display: none;
    }

    .sidebar.collapsed .brand img {
      width: 50px;
    }


    .topbar {
      background: rgba(255, 255, 255, 0.02);
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      position: sticky;
      top: 0;
      z-index: 90;
      box-shadow: 0 2px 20px rgba(0,0,0,0.4);
    }

    .toggle-btn {
      background: transparent;
      border: none;
      color: var(--accent);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.2s ease;
    }

    .toggle-btn:hover {
      color: var(--accent-2);
    }

    .wrapper {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      transition: margin-left 0.25s ease;
    }

    .sidebar.collapsed ~ .wrapper {
      margin-left: 78px;
    }


    .btn {
      background: var(--accent);
      border: none;
      color: #111;
      font-weight: 700;
      padding: 8px 14px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .btn:hover {
      background: var(--accent-2);
      transform: translateY(-2px);
    }

    .btn.ghost {
      background: transparent;
      color: var(--accent-2);
      border: 1px solid rgba(233,184,154,0.2);
    }

    .btn.ghost:hover {
      background: rgba(233,184,154,0.1);
    }


    @media (max-width: 900px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .wrapper {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>

<body>

<nav id="sidebar" class="sidebar">
    <div class="brand">
      <img src="../customer/images/logo.png" alt="logo">
      <span>GiftIQ Admin</span>
    </div>
    <ul>
      <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>"><span class="icon">🏠</span><span class="label">Dashboard</span></a></li>
      <li><a href="manage_orders.php" class="<?= basename($_SERVER['PHP_SELF'])=='manage_orders.php'?'active':'' ?>"><span class="icon">📦</span><span class="label">Orders</span></a></li>
      <li><a href="manage_products.php" class="<?= basename($_SERVER['PHP_SELF'])=='manage_products.php'?'active':'' ?>"><span class="icon">🛍</span><span class="label">Products</span></a></li>
      <li><a href="manage_returns.php" class="<?= basename($_SERVER['PHP_SELF'])=='manage_returns.php'?'active':'' ?>"><span class="icon">↩</span><span class="label">Returns</span></a></li>
      <li><a href="manage_contacts.php" class="<?= basename($_SERVER['PHP_SELF'])=='manage_contacts.php'?'active':'' ?>"><span class="icon">📩</span><span class="label">Contacts</span></a></li>
      <li><a href="send_mail.php" class="<?= basename($_SERVER['PHP_SELF'])=='send_mail.php'?'active':'' ?>"><span class="icon">✉️</span><span class="label">Send Mail</span></a></li>
      <li><a href="feedback_list.php" class="<?= basename($_SERVER['PHP_SELF'])=='feedback_list.php'?'active':'' ?>"><span class="icon">📝</span><span class="label">Feedbacks</span></a></li>
      <li><a href="../index.php" target="_blank"><span class="icon">🌐</span><span class="label">View Site</span></a></li>
      <li><a href="logout.php"><span class="icon">🔒</span><span class="label">Logout</span></a></li>
    </ul>
  </nav>

<div class="wrapper">
  <header class="topbar">
    <div>
      <button id="toggleSidebar" class="toggle-btn" aria-label="Toggle menu">☰</button>
    </div>
    <div style="display:flex;align-items:center;gap:14px">
      <div style="color:var(--muted)">Hello, <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></strong></div>
    </div>
  </header>

  
