<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT o.id, o.created_at, o.total, o.status,
                GROUP_CONCAT(p.name SEPARATOR ', ') AS products
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = $user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders | Mad Smile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* =========================================
   1. ROOT VARIABLES
   ========================================= */
:root {
  --accent-pink: #f7d4d1;
  --accent-gold: #ffe6b3;
  --accent-text: #d47474;
  --white: #fff;
  --shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 12px 32px rgba(0, 0, 0, 0.1); /* A slightly stronger hover shadow */
}

/* =========================================
   2. GLOBAL & BASE STYLES
   ========================================= */
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #fff8f6, #ffeecb);
  color: #333;
  min-height: 100vh;
}

/* =========================================
   3. LAYOUT & CONTAINERS
   ========================================= */
.orders-container {
  width: 95%; /* Changed from 90% for better use of space on mobile */
  max-width: 1100px;
  margin: 0 auto 4rem;
  display: grid;
  /* Uses minmax(300px, 1fr) to fit better on small mobile screens */
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

footer {
  text-align: center;
  font-size: 0.9rem;
  padding: 1.2rem 0;
  color: #a97a7a;
  background: #fff;
  border-top: 1px solid #f3dede;
  margin-top: 3rem; /* Added margin to ensure it's separate from content */
}

/* =========================================
   4. COMPONENTS
   ========================================= */

/* --- Page Header --- */
.collection-header {
  text-align: center;
  margin: 2.5rem 0 1.5rem;
}
.collection-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--accent-text); /* Fallback color */
  background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* --- Order Card --- */
.order-card {
  background: var(--white);
  border-radius: 14px;
  box-shadow: var(--shadow);
  padding: 1.5rem;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.order-card:hover {
  /* Switched to a scale transform for a smoother feel */
  transform: scale(1.02);
  box-shadow: var(--shadow-hover);
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  gap: 0.5rem; /* Added gap for spacing */
}
.order-header h2 {
  font-size: 1rem;
  color: var(--accent-text);
  font-weight: 600;
  margin: 0; /* Removed default margin */
}

/* --- Status Badge --- */
.status-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: capitalize;
  flex-shrink: 0; /* Prevents badge from shrinking */
}
.status-pending {
  background: #fff0d5;
  color: #a87400;
}
.status-shipped {
  background: #dff7e0;
  color: #1d7927;
}
.status-delivered {
  background: #cce5ff;
  color: #0056b3;
}
.status-cancelled {
  background: #fde2e2;
  color: #a12626;
}

/* --- Order Details --- */
.order-details p {
  margin: 0.3rem 0;
  font-size: 0.95rem;
}
.order-total {
  font-weight: 600;
  color: var(--accent-text);
  margin-top: 0.8rem;
}

/* --- Print Button --- */
.btn-print {
  display: inline-block;
  background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
  color: #fff;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  border-radius: 8px;
  text-decoration: none;
  border: none; /* Ensure it's treated as a button */
  cursor: pointer;
  transition: opacity 0.2s ease, box-shadow 0.2s ease;
}
.btn-print:hover {
  /* Simpler, more professional hover effect */
  opacity: 0.9;
  box-shadow: var(--shadow);
}

/* --- No Orders Placeholder --- */
.no-orders {
  text-align: center;
  color: #999;
  padding: 3rem 1rem;
  font-size: 1.1rem;
  /* Ensures it spans the grid if it's the only item */
  grid-column: 1 / -1;
}

/* =========================================
   5. RESPONSIVE MEDIA QUERIES
   ========================================= */

/* --- Tablet (and large phone) styles --- */
@media (max-width: 768px) {
  .collection-header h1 {
    font-size: 1.5rem;
  }
  .order-card {
    padding: 1.2rem;
  }
  .order-header h2 {
    font-size: 0.95rem;
  }
  .btn-print {
    /* Makes button full-width for easy tapping */
    width: 100%;
    text-align: center;
  }
}

/* --- Small Mobile styles --- */
@media (max-width: 480px) {
  .collection-header h1 {
    font-size: 1.4rem;
  }
  .order-card {
    padding: 1rem; /* Further reduce padding on small screens */
  }
  .order-header {
    /* Stack header and badge vertically */
    flex-direction: column;
    align-items: flex-start; /* Align to the left */
    gap: 0.5rem;
    margin-bottom: 1rem;
  }
  .status-badge {
    /* Ensure badge doesn't look out of place */
    font-size: 0.8rem;
  }
}

/* ==========================
   Return Modal styles (integrated, responsive)
   keep these at the bottom to avoid overriding your main styles
   ========================== */
.return-modal-backdrop {
  position: fixed;
  inset:0;
  background: rgba(0,0,0,0.6);
  display:none;
  align-items:center;
  justify-content:center;
  z-index: 9999;
  padding: 20px;
}
.return-modal-backdrop.open { display:flex; }
.return-modal {
  width: 100%;
  max-width: 920px;
  max-height: 90vh;
  overflow:auto;
  background: #0f0f10; /* dark modal background to contrast inside content provided by request_return.php */
  border-radius: 12px;
  box-shadow: 0 12px 48px rgba(0,0,0,0.6);
  padding: 18px;
  border: 1px solid rgba(255,255,255,0.04);
  color: #efe8df;
}
.return-modal .modal-header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  margin-bottom: 12px;
}
.return-modal .modal-header h3 { margin:0; color: #f7b47d; }
.return-modal .modal-close {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.06);
  color: #f7b47d;
  padding: 6px 10px;
  border-radius:8px;
  cursor:pointer;
}
.return-modal .modal-body { background: rgba(255,255,255,0.02); padding:12px; border-radius:10px; color:#efe8df; }

/* spinner */
.rm-spinner { display:inline-block; width:18px; height:18px; border:3px solid rgba(255,255,255,0.08); border-top-color: #f7b47d; border-radius:50%; animation:spin .8s linear infinite; vertical-align:middle; margin-left:8px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Small screens modal */
@media (max-width:720px) {
  .return-modal { padding:12px; max-width: 96%; }
}
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<section class="collection-header">
  <h1>📦 My Orders</h1>
</section>

<main>
  <?php if ($result->num_rows == 0): ?>
    <div class="no-orders">
      <i class="fa-regular fa-face-frown"></i><br>
      You have no past orders yet.
    </div>
  <?php else: ?>
    <div class="orders-container">
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="order-card">
          <div class="order-header">
            <h2>Order #<?= $row['id']; ?></h2>
            <span class="status-badge status-<?= strtolower($row['status']); ?>">
              <?= ucfirst($row['status']); ?>
            </span>
          </div>
          <div class="order-details">
            <p><strong>Date:</strong> <?= date('d M Y', strtotime($row['created_at'])); ?></p>
            <p><strong>Products:</strong> <?= htmlspecialchars($row['products']); ?></p>
            <p class="order-total"><strong>Total:</strong> ₹<?= number_format($row['total'], 2); ?></p>
          </div>
          <div style="margin-top:1rem;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="print_invoice.php?order_id=<?= $row['id']; ?>" class="btn-print" target="_blank">
              <i class="fa-solid fa-print"></i> Print Invoice
            </a>

            <!-- Return button replaced with JS-opened modal.
                 Fallback: If JS disabled, the link still points to request_return.php?order_id=... -->
            <button class="btn-print" type="button" onclick="openReturnModal(<?= $row['id']; ?>)">
              <i class="fa-solid fa-arrow-rotate-left"></i> Return Order
            </button>

            <!-- (optional) link fallback for no-JS users -->
            <noscript>
              <a href="request_return.php?order_id=<?= $row['id']; ?>" class="btn-print">Return Order</a>
            </noscript>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?> 
</main>

<!-- RETURN MODAL (injected content will be loaded from request_return.php) -->
<div id="returnModalBackdrop" class="return-modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="return-modal" role="document" aria-labelledby="returnModalTitle">
    <div class="modal-header">
      <h3 id="returnModalTitle">Return Request</h3>
      <div>
        <button class="modal-close" id="returnModalClose" title="Close">Close</button>
      </div>
    </div>
    <div class="modal-body" id="returnModalBody">
      <p style="color:var(--muted)">Loading form <span class="rm-spinner" aria-hidden="true"></span></p>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
/**
 * openReturnModal(orderId)
 * Fetches request_return.php?order=ID and injects into modal body.
 * Also supports request_return.php?order_id=ID (both query names included).
 */
function openReturnModal(orderId) {
  var backdrop = document.getElementById('returnModalBackdrop');
  var body = document.getElementById('returnModalBody');
  if (!backdrop || !body) {
    // fallback: if modal not present just navigate to the request page
    window.location.href = 'request_return.php?order=' + orderId + '&order_id=' + orderId;
    return;
  }

  // show modal immediately with spinner
  backdrop.classList.add('open');
  backdrop.setAttribute('aria-hidden','false');
  body.innerHTML = '<p style="color:var(--muted)">Loading form <span class="rm-spinner" aria-hidden="true"></span></p>';

  // safe fetch — adds both query params so server accepts either
  var url = 'request_return.php?order=' + orderId + '&order_id=' + orderId;

  fetch(url, { credentials: 'same-origin' })
    .then(function(resp){
      if (!resp.ok) throw new Error('Network response not ok');
      return resp.text();
    })
    .then(function(html){
      // Replace modal body with returned HTML
      // If request_return.php outputs full page (header/footer), strip those out and keep main form
      // Best-effort: attempt to extract content within the first <div class="card"> ... </div>
      var extracted = html;
      try {
        // create temporary DOM parser
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');

        // prefer an element with class 'card' or 'return-card' if present
        var candidate = doc.querySelector('.card.return-card') || doc.querySelector('.card') || doc.querySelector('main') || doc.body;
        if (candidate) {
          extracted = candidate.innerHTML;
        } else {
          extracted = html;
        }
      } catch(e){
        extracted = html;
      }

      body.innerHTML = extracted;

      // If the form exists inside modal, intercept submit with fetch to preserve modal UI
      var form = body.querySelector('form');
      if (form) {
        form.addEventListener('submit', function(ev){
          ev.preventDefault();
          var fd = new FormData(form);
          // show small submitting spinner / disable button
          var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
          var origHtml = submitBtn ? submitBtn.innerHTML : null;
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Submitting <span class="rm-spinner" aria-hidden="true"></span>';
          }

          fetch(form.action || url, { method: form.method || 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(respHtml){
              // Replace modal body with server response (may include success notice)
              body.innerHTML = respHtml;
            })
            .catch(function(err){
              body.insertAdjacentHTML('afterbegin','<div style="color:#f5b0a0;margin-bottom:8px;">An error occurred. Please try again.</div>');
            })
            .finally(function(){
              if (submitBtn) {
                submitBtn.disabled = false;
                if (origHtml) submitBtn.innerHTML = origHtml;
              }
            });
        });
      }

      // focus first input for accessibility
      var firstInput = body.querySelector('input, select, textarea, button');
      if (firstInput) firstInput.focus();
    })
    .catch(function(err){
      body.innerHTML = '<div style="color:#f8b6b6">Failed to load the form. <a href="request_return.php?order=' + orderId + '">Open in new page</a></div>';
    });
}

// Close handlers
document.getElementById('returnModalClose').addEventListener('click', function(){
  var b = document.getElementById('returnModalBackdrop');
  b.classList.remove('open');
  b.setAttribute('aria-hidden','true');
  document.getElementById('returnModalBody').innerHTML = '<p style="color:var(--muted)">Loading form <span class="rm-spinner" aria-hidden="true"></span></p>';
});

// close when clicking outside the modal box
document.getElementById('returnModalBackdrop').addEventListener('click', function(e){
  if (e.target === this) {
    this.classList.remove('open');
    this.setAttribute('aria-hidden','true');
    document.getElementById('returnModalBody').innerHTML = '<p style="color:var(--muted)">Loading form <span class="rm-spinner" aria-hidden="true"></span></p>';
  }
});

// close with Escape
document.addEventListener('keydown', function(e){
  if (e.key === 'Escape') {
    var b = document.getElementById('returnModalBackdrop');
    if (b.classList.contains('open')) {
      b.classList.remove('open');
      b.setAttribute('aria-hidden','true');
      document.getElementById('returnModalBody').innerHTML = '<p style="color:var(--muted)">Loading form <span class="rm-spinner" aria-hidden="true"></span></p>';
    }
  }
});
</script>
</body>
</html>
