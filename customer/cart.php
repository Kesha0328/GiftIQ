<?php
session_start();
include '../config.php';

// Initialize cart if it doesn't exist
if (empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle 'add_to_cart' (Your existing logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));

    $res = $conn->query("SELECT * FROM products WHERE id=$pid");
    if ($res && $res->num_rows > 0) {
        $p = $res->fetch_assoc();
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$pid] = [
                'product_id' => $pid,
                'name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $qty,
                'image' => $p['image'] ?? '',
                'custom' => false
            ];
        }
        header("Location: cart.php?added=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart - GiftIQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../uploads/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* =========================================
           1. ROOT VARIABLES
           ========================================= */
        :root {
            --accent-pink: #f7d4d1;
            --accent-gold: #ffe6b3;
            --accent-text: #d47474;
            --white: #fff;
            --border-light: #f0e0e0;
            --shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            --shadow-hover: 0 8px 25px rgba(212, 116, 116, 0.15);
        }

        /* =========================================
           2. GLOBAL & BASE STYLES
           ========================================= */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #fff8f6, #ffeecb);
            min-height: 100vh;
            color: #333;
        }
        * {
            box-sizing: border-box;
        }

        /* =========================================
           3. LAYOUT & HEADER
           ========================================= */
        .collection-header {
            text-align: center;
            margin: 2rem 0 1rem;
        }
        .collection-header h1 {
            background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            font-weight: 700;
        }
        .cart-container {
            max-width: 900px;
            margin: 20px auto 4rem;
            background: var(--white);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        /* =========================================
           4. CART TABLE (Desktop)
           ========================================= */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }
        th {
            background: #fff4f2;
            color: var(--accent-text);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            text-align: left; /* More professional */
        }
        /* Align numerical/action data to the right */
        th:not(:first-child),
        td:not(:first-child) {
            text-align: right;
        }
        /* Product Info Cell */
        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-align: left; /* Keep product info left-aligned */
        }
        .product-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-info strong {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        /* =========================================
           5. BUTTONS & FORMS
           ========================================= */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
            color: #fff;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }
        .btn-update, .btn-remove {
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .btn-update {
            background: #ffd6b3;
            color: #333;
        }
        .btn-update:hover {
            background: #ffc599;
            transform: scale(1.05);
        }
        .btn-remove {
            background: #fcd3d3;
            color: #222;
        }
        .btn-remove:hover {
            background: #f9bebe;
            transform: scale(1.05);
        }
        /* Quantity Form */
        .quantity-form {
            display: flex;
            justify-content: flex-end; /* Aligns with `text-align: right` */
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-form input[type='number'] {
            width: 60px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            text-align: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            -moz-appearance: textfield; /* Hides spinner in Firefox */
        }
        /* Hides spinner in Chrome, Safari */
        .quantity-form input[type='number']::-webkit-inner-spin-button,
        .quantity-form input[type='number']::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* =========================================
           6. SUMMARY & NOTIFICATIONS
           ========================================= */
        .cart-empty {
            text-align: center;
            font-weight: 600;
            padding: 2rem 0;
        }
        .cart-summary {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: flex-end; /* Aligns total and button to the right */
            gap: 1rem;
        }
        .total-box {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-text);
        }
        .checkout-container {
            text-align: right;
        }
        .notice.success {
            background: #eafbea;
            color: #1b5e20;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin: 10px auto;
            max-width: 400px;
            font-weight: 500;
        }

        /* =========================================
           7. RESPONSIVE (Tablet & Mobile)
           ========================================= */
        @media (max-width: 768px) {
            .cart-container {
                padding: 0;
                box-shadow: none;
                background: transparent;
            }
            /* --- Responsive Table: "Card" Mode --- */
            table {
                border-collapse: separate; /* Required for border-radius on tr */
                border-spacing: 0 1rem;  /* Creates space between cards */
            }
            /* Hide desktop headers */
            thead {
                display: none;
            }
            /* Each row becomes a card */
            tr {
                display: block;
                background: var(--white);
                border-radius: 12px;
                box-shadow: var(--shadow);
                padding: 1rem;
                border-bottom: none;
            }
            /* Each cell becomes a block-level element */
            td {
                display: flex;
                justify-content: space-between; /* Creates "Key: Value" pair */
                align-items: center;
                border: none;
                padding: 0.75rem 0.5rem;
                text-align: right; /* Aligns the value */
                border-bottom: 1px solid #f9f2f2; /* Light separator line */
            }
            /* Add the "header" using the data-label */
            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--accent-text);
                text-align: left;
                margin-right: 1rem;
            }
            /* The last cell in each card doesn't need a border */
            tr td:last-child {
                border-bottom: none;
            }

            /* --- Special Styling for Product Cell --- */
            td:first-child {
                display: block; /* Don't use flex for this cell */
                text-align: center;
                padding-bottom: 1rem;
            }
            /* Hide data-label for the first cell (Product) */
            td:first-child::before {
                display: none;
            }
            .product-info {
                flex-direction: column; /* Stack image and name */
                gap: 0.5rem;
            }
            .product-info img {
                width: 120px; /* Larger image for mobile card */
                height: 120px;
            }

            /* --- Special Styling for Form Cells --- */
            .quantity-form {
                justify-content: flex-end;
                width: 100%;
            }
            /* Full-width remove button on its own line */
            td[data-label="Action"] {
                padding-top: 1rem;
            }
            td[data-label="Action"] form {
                width: 100%;
            }
            .btn-remove {
                width: 100%;
            }
            
            /* --- Summary & Checkout Button --- */
            .cart-summary {
                align-items: center; /* Center total and button */
                background: var(--white);
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: var(--shadow);
                margin-top: 1rem;
            }
            .checkout-container {
                text-align: center;
                width: 100%;
            }
            .btn-primary {
                display: block;
                width: 100%; /* Full-width checkout button */
                text-align: center;
                padding: 14px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="collection-header"><h1>🛒 My Cart</h1></div>
    <?php if (isset($_GET['added'])): ?>
        <div class="notice success">✅ Item added to your cart!</div>
    <?php endif; ?>

    <div class="cart-container">
    <?php
    if (empty($_SESSION['cart'])) {
        echo "<p class='cart-empty'>Your cart is empty.</p>";
    } else {
        $total = 0;
        echo "<table>";
        echo "<thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>";
        
        foreach($_SESSION['cart'] as $key => $it) {
            $qty = intval($it['quantity']);
            $price = floatval($it['price']);
            $name = htmlspecialchars($it['name']);
            $subtotal = $price * $qty;
            $total += $subtotal;
            $key_safe = htmlspecialchars($key);

            echo "<tr>";
            
            // Item Column
            echo "<td data-label='Item'>";
            echo "<div class='product-info'>";
            if (!empty($it['image'])) {
                echo "<img src='../uploads/".htmlspecialchars($it['image'])."' alt='{$name}'>";
            }
            echo "<strong>{$name}</strong>";
            echo "</div>";
            echo "</td>";
            
            // Price Column
            echo "<td data-label='Price'>₹".number_format($price, 2)."</td>";
            
            // Quantity Column
            echo "<td data-label='Qty'>";
            echo "<form method='post' action='cart_update.php' class='quantity-form'>
                    <input type='hidden' name='key' value='{$key_safe}'>
                    <input type='number' name='quantity' value='{$qty}' min='1'>
                    <button name='action' value='update' class='btn-update'>Update</button>
                  </form>";
            echo "</td>";
            
            // Subtotal Column
            echo "<td data-label='Subtotal'>₹".number_format($subtotal, 2)."</td>";
            
            // Action Column
            echo "<td data-label='Action'>
                    <form method='post' action='cart_update.php'>
                      <input type='hidden' name='key' value='{$key_safe}'>
                      <button name='action' value='remove' class='btn-remove'>Remove</button>
                    </form>
                  </td>";
            
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        
        // --- Cart Summary Section ---
        echo "<div class='cart-summary'>";
        echo "<div class='total-box'>Total: ₹".number_format($total, 2)."</div>";

        if (isset($_SESSION['user_id'])) {
            echo "<div class='checkout-container'>
                    <a href='checkout.php' class='btn-primary'>Proceed to Checkout</a>
                  </div>";
        } else {
            echo "<div class='checkout-container'>
                    <a href='login.php?redirect=checkout.php' class='btn-primary'>Login to Checkout</a>
                  </div>";
        }
        echo "</div>"; // End .cart-summary
    }
    ?>
    </div> </main>

</body>
</html>