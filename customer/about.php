<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mad Smile - About Us</title>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
  :root{
    --accent-pink: #f7d4d1;
    --accent-gold: #ffe6b3;
    --accent-text: #d47474;
    --white: #fff;
    --shadow: 0 8px 24px rgba(0,0,0,0.06);
    --card-width: 280px;
  }

  *{box-sizing:border-box}
  body{
    font-family:'Poppins',sans-serif;
    margin:0;
    background:linear-gradient(135deg,#fff8f6,#ffeecb);
    color:#333;
  }

  .about-section{
    max-width:960px;
    margin:32px auto;
    background:var(--white);
    padding:28px;
    border-radius:16px;
    box-shadow:var(--shadow);
    text-align:center;
  }
  .about-section h1{
    font-size:28px;
    margin:0 0 10px;
    background:linear-gradient(90deg,#f4b8b4,#ffd9a0);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    font-weight:700;
  }
  .about-section p{ color:#444; line-height:1.6; margin:12px 0 18px; }
  .values-list{ list-style:none; padding:0; margin:0 auto; display:flex; gap:8px; justify-content:center; flex-wrap:wrap; }
  .values-list li{
    background:linear-gradient(135deg,var(--accent-gold),var(--accent-pink));
    color:#fff; padding:8px 12px; border-radius:28px; font-weight:600; font-size:14px;
  }

  .team{
    max-width:1100px;
    margin:28px auto 56px;
    display:flex;
    gap:24px;
    justify-content:center;
    flex-wrap:wrap;
    padding:0 12px;
  }

  .team-member{
    background:var(--white);
    width:var(--card-width);
    border-radius:14px;
    box-shadow:var(--shadow);
    padding:20px 16px 24px;
    text-align:center;
    transition:transform .22s, box-shadow .22s;
  }
  .team-member:hover{ transform:translateY(-6px); box-shadow:0 14px 30px rgba(0,0,0,0.08); }

  .team-member img{
    width:110px; height:110px; object-fit:cover;
    border-radius:50%;
    border:3px solid rgba(247,212,209,0.7);
    margin:0 auto 10px; display:block;
  }
  .team-member h3{ margin:6px 0 4px; color:var(--accent-text); font-weight:700; }
  .team-member .role{ font-size:0.95rem; color:#666; margin-bottom:10px; }
  .team-member .social a{ margin:0 6px; color:var(--accent-text); font-size:18px; text-decoration:none; }
  .team-member .social a:hover{ color:#f3a5a3; transform:scale(1.15); }

  .team-row { display:flex; gap:16px; justify-content:center; align-items:stretch; }

  @media (max-width: 768px){
    .about-section{ margin:18px 12px; padding:18px; }
    .about-section h1{ font-size:20px; }

    .team{
      flex-direction:column;
      gap:18px;
      align-items:center;
      padding:0 10px;
    }

    .team > .team-member:first-child{
      width:100%;
      max-width:720px;
      margin:0 auto;
    }

    .team-row {
      display:flex;
      justify-content:center;
      gap:16px;
      width:100%;
      max-width:700px;
      margin:0 auto;
      flex-wrap:nowrap;
    }

    .team-row .team-member{
      flex:1 1 48%;
      max-width:48%;
      width:auto;
      padding:16px 12px;
    }

    .team-row .team-member img{
      width:95px; height:95px;
      margin-bottom:12px;
    }

    .team-row .team-member h3{ font-size:16px; margin-bottom:6px; }
    .team-row .team-member .role{ font-size:13px; margin-bottom:10px; }
  }

  @media (max-width:420px){
    .team-row{ flex-wrap:wrap; }
    .team-row .team-member{ max-width:100%; flex:1 1 100%; }
  }
  </style>
</head>
<body>

<?php include "header.php"; ?>

<div class="about-section">
  <h1>About Mad Smile</h1>
  <p>
    Mad Smile is dedicated to spreading happiness through thoughtfully curated gift hampers.
    Our mission is to make every occasion memorable with premium products, beautiful packaging, and heartfelt service.
  </p>
  <ul class="values-list">
    <li>Premium Quality</li>
    <li>Eco-Friendly Packaging</li>
    <li>Customizable Hampers</li>
    <li>Fast Delivery</li>
    <li>Customer Happiness</li>
  </ul>
</div>

<div class="team">

<div class="team-member">
    <img src="team1.jpg" alt="Kesha Gabani">
    <h3>Kesha Gabani</h3>
    <div class="role">Founder &amp; CEO</div>
    <div class="social">
      <a href="mailto:kesha@gmail.com"><i class="fas fa-envelope"></i></a>
      <a href="https://instagram.com/kesha0328"><i class="fab fa-instagram"></i></a>
    </div>
  </div>

  <div class="team-row">
    <div class="team-member">
      <img src="team1.jpg" alt="Drashti Patel">
      <h3>Drashti Patel</h3>
      <div class="role">Front-end Developer</div>
      <div class="social">
        <a href="mailto:drashti@email.com"><i class="fas fa-envelope"></i></a>
        <a href="https://instagram.com/drashti"><i class="fab fa-instagram"></i></a>
      </div>
    </div>

    <div class="team-member">
      <img src="team2.jpg" alt="Aditi Bhatt">
      <h3>Aditi Bhatt</h3>
      <div class="role">Back-end Developer</div>
      <div class="social">
        <a href="mailto:aditi@email.com"><i class="fas fa-envelope"></i></a>
        <a href="https://instagram.com/aditi"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
