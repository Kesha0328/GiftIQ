<?php
session_start();
include '../config.php';

$base_price = 500;

$collections = $conn->query("SELECT * FROM products WHERE type='collection' ORDER BY name");
$chocolates  = $conn->query("SELECT * FROM products WHERE type='chocolate' ORDER BY name");
$accessories = $conn->query("SELECT * FROM products WHERE type='accessory' ORDER BY name");
$flowers     = $conn->query("SELECT * FROM products WHERE type='flower' ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collection = $_POST['collection'] ?? '';
    $chocolate  = $_POST['chocolate'] ?? '';
    $accessory  = $_POST['accessory'] ?? '';
    $flower     = $_POST['flower'] ?? '';
    $message    = trim($_POST['message'] ?? '');
    $quantity   = max(1, intval($_POST['quantity'] ?? 1));

    $price = $base_price;
    foreach (['collection'=>$collection,'chocolate'=>$chocolate,'accessory'=>$accessory,'flower'=>$flower] as $pid) {
        if (!empty($pid)) {
            $res = $conn->query("SELECT price FROM products WHERE id=".(int)$pid);
            if ($res && $res->num_rows>0) $price += $res->fetch_assoc()['price'];
        }
    }

    $custom_id = 'custom_' . substr(md5(time() . rand()), 0, 12);
    $_SESSION['cart'][$custom_id] = [
        'product_id' => 0,
        'name' => 'Custom Gift',
        'price' => $price,
        'quantity' => $quantity,
        'custom' => true,
        'details' => compact('collection','chocolate','accessory','flower','message')
    ];

    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Build Your Custom Gift | GiftIQ</title>
  <link rel="stylesheet" href="assets/customize.css">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />

</head>
<body>
<?php include 'header.php'; ?>

<section class="collection-header fadeInUp">
  <h1><span class="header-icon">🎁</span> Build Your Custom Gift</h1>
</section>

<main class="customize-container">
  <section class="customize-section">
    <h2><i class="fa-solid fa-gift"></i> Customize Your Gift</h2>
    <form method="post" id="customGiftForm" aria-label="Custom Gift Builder">

      <label for="collection">🎀 Select Collection</label>
      <select name="collection" id="collection" title="Select Collection">
        <option value="">-- Choose --</option>
        <?php while($c=$collections->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>" data-price="<?= $c['price'] ?>">
            <?= htmlspecialchars($c['name']) ?> (₹<?= number_format($c['price'],2) ?>)
          </option>
        <?php endwhile; ?>
      </select>

      <label for="chocolate">🍫 Choose Chocolates</label>
      <select name="chocolate" id="chocolate" title="Choose Chocolates">
        <option value="">None</option>
        <?php while($ch=$chocolates->fetch_assoc()): ?>
          <option value="<?= $ch['id'] ?>" data-price="<?= $ch['price'] ?>">
            <?= htmlspecialchars($ch['name']) ?> (₹<?= number_format($ch['price'],2) ?>)
          </option>
        <?php endwhile; ?>
      </select>

      <label for="accessory">🎁 Choose Accessories</label>
      <select name="accessory" id="accessory" title="Choose Accessories">
        <option value="">None</option>
        <?php while($a=$accessories->fetch_assoc()): ?>
          <option value="<?= $a['id'] ?>" data-price="<?= $a['price'] ?>">
            <?= htmlspecialchars($a['name']) ?> (₹<?= number_format($a['price'],2) ?>)
          </option>
        <?php endwhile; ?>
      </select>

      <label for="flower">🌸 Choose Flowers</label>
      <select name="flower" id="flower" title="Choose Flowers">
        <option value="">None</option>
        <?php while($f=$flowers->fetch_assoc()): ?>
          <option value="<?= $f['id'] ?>" data-price="<?= $f['price'] ?>">
            <?= htmlspecialchars($f['name']) ?> (₹<?= number_format($f['price'],2) ?>)
          </option>
        <?php endwhile; ?>
      </select>

      <label for="message">💌 Custom Message</label>
      <textarea name="message" id="message" placeholder="Write your personal note..." title="Custom Message"></textarea>

      <label for="quantity">📦 Quantity</label>
      <input type="number" name="quantity" id="quantity" min="1" value="1" title="Gift Quantity">

      <button type="submit" class="btn-primary" title="Add your custom gift to the cart">✨ Add Custom Gift to Cart</button>
    </form>
  </section>

  <aside class="live-preview" id="livePreview">
    <h3>✨ Live Preview</h3>
    <p><strong>Collection:</strong> <span id="pCollection">None</span></p>
    <p><strong>Chocolates:</strong> <span id="pChocolate">None</span></p>
    <p><strong>Accessories:</strong> <span id="pAccessory">None</span></p>
    <p><strong>Flowers:</strong> <span id="pFlower">None</span></p>
    <p><strong>Message:</strong> <span id="pMessage">None</span></p>
    <p><strong>Quantity:</strong> <span id="pQuantity">1</span></p>
    <p class="price-tag"><strong>Total Price:</strong> ₹<span id="pPrice"><?= $base_price ?></span></p>
  </aside>
</main>

<?php include 'footer.php'; ?>

<script>
const basePrice = <?= $base_price ?>;
const form = document.getElementById('customGiftForm');
function getPrice(sel){return parseFloat(sel.options[sel.selectedIndex]?.dataset.price||0);}
function updatePreview(){
  const c=form.collection, ch=form.chocolate, a=form.accessory, f=form.flower;
  const total=(basePrice+getPrice(c)+getPrice(ch)+getPrice(a)+getPrice(f))*Math.max(1,form.quantity.value);
  document.getElementById('pCollection').textContent=c.options[c.selectedIndex]?.text||'None';
  document.getElementById('pChocolate').textContent=ch.options[ch.selectedIndex]?.text||'None';
  document.getElementById('pAccessory').textContent=a.options[a.selectedIndex]?.text||'None';
  document.getElementById('pFlower').textContent=f.options[f.selectedIndex]?.text||'None';
  document.getElementById('pMessage').textContent=form.message.value.trim()||'None';
  document.getElementById('pQuantity').textContent=form.quantity.value;
  document.getElementById('pPrice').textContent=total.toFixed(2);
}
['collection','chocolate','accessory','flower','message','quantity'].forEach(id=>{
  form[id].addEventListener('change',updatePreview);
  form[id].addEventListener('input',updatePreview);
});
updatePreview();
</script>
</body>
</html>
