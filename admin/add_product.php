<?php
require 'admin_header.php';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $collection_id = !empty($_POST['collection_id']) && $_POST['collection_id'] != "0"
        ? intval($_POST['collection_id'])
        : null;
    $type = $_POST['type'] ?? 'standard';

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageName = uniqid('p_').'.'.$ext;
        $targetPath = __DIR__."/../uploads/".$imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $error = "❌ Failed to upload image.";
        }
    }
if ($collection_id === null) {
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, collection_id, type)
                            VALUES (?, ?, ?, ?, NULL, ?)");
    $stmt->bind_param("sdsss", $name, $price, $description, $imageName, $type);
} else {
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, collection_id, type)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssis", $name, $price, $description, $imageName, $collection_id, $type);
}

if ($stmt->execute()) {
    $success = "✅ Product added successfully!";
} else {
    $error = "❌ Database error: " . $stmt->error;
}

}

$cols = $conn->query("SELECT id, name FROM collections");
?>
  <head><link rel="icon" type="image/png" href="../uploads/favicon.png" />

<style>
.card {
  padding: 30px 28px;
  margin: 40px auto;
  border-radius: 16px;
  background: rgba(255,255,255,0.03);
  box-shadow: 0 8px 25px rgba(0,0,0,0.3), inset 0 0 15px rgba(233,184,154,0.05);
  backdrop-filter: blur(10px);
  max-width: 650px;
  color: #f1f1f1;
  animation: fadeIn 0.6s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px);}
  to { opacity: 1; transform: translateY(0);}
}

.card h3 {
  font-size: 1.4rem;
  color: var(--accent-2);
  margin-bottom: 22px;
  display: flex;
  align-items: center;
  gap: 8px;
}

form { display: flex; flex-direction: column; gap: 16px; }

label {
  font-size: 0.9rem;
  color: #eee8e8ff;
  margin-bottom: 6px;
  letter-spacing: 0.3px;
}

input[type="text"],
input[type="number"],
select,
textarea {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  color: #fff;
  padding: 10px 12px;
  border-radius: 10px;
  outline: none;
  font-size: 0.95rem;
  transition: all 0.25s ease;
}
input:focus, select:focus, textarea:focus {
  border-color: var(--accent);
  box-shadow: 0 0 12px rgba(233,184,154,0.4);
  background: rgba(245,244,244,0.07);
}

select {
  appearance: none;
  background-color: rgba(40, 40, 40, 0.85);
  color: #f5f5f5;
  border: 1px solid rgba(255,255,255,0.1);
  padding: 10px 36px 10px 12px;
  border-radius: 10px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.25s ease;
  background-image: url("data:image/svg+xml;utf8,<svg fill='%23E9B89A' height='16' width='16' viewBox='0 0 24 24'><path d='M7 10l5 5 5-5z'/></svg>");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
}
select:hover { background-color: rgba(60, 60, 60, 0.9); }
select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 12px rgba(233,184,154,0.4);
}
select option { background-color: #1e1e1e; color: #f5f5f5; }

.img-preview {
  width: 140px;
  height: 100px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.08);
  margin-top: 8px;
  display: none;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.img-preview.show {
  display: block;
  transform: scale(1.03);
  box-shadow: 0 0 20px rgba(233,184,154,0.25);
}

.btn {
  background: linear-gradient(90deg,var(--accent),var(--accent-2));
  color: #111;
  font-weight: 700;
  padding: 10px 18px;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(233,184,154,0.25);
  transition: all 0.25s ease;
  margin-top: 8px;
}
.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(233,184,154,0.4);
}

.notice {
  margin-bottom: 14px;
  padding: 12px 14px;
  border-radius: 10px;
  font-weight: 500;
  letter-spacing: 0.3px;
}
.notice.success {
  background: rgba(46,164,79,0.12);
  border: 1px solid rgba(46,164,79,0.3);
  color: #b8f0c0;
}
.notice.error {
  background: rgba(255,0,0,0.08);
  border: 1px solid rgba(255,0,0,0.2);
  color: #ff9a9a;
}
</style>
</head>
<div class="card">
  <h3>➕ Add Product</h3>

  <?php if ($success): ?><div class="notice success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-row">
      <label>Product Name</label>
      <input type="text" name="name" placeholder="Enter product name" required>
    </div>

    <div class="form-row">
      <label>Price (₹)</label>
      <input type="number" step="0.01" name="price" placeholder="Enter price" required>
    </div>

    <div class="form-row">
      <label>Product Type</label>
      <select name="type" required>
        <option value="standard">Standard Product</option>
        <option value="collection">Collection</option>
        <option value="chocolate">Chocolate</option>
        <option value="accessory">Accessory</option>
        <option value="flower">Flower</option>
      </select>
    </div>

    <div class="form-row">
      <label>Parent Collection (optional)</label>
      <select name="collection_id">
        <option value="0">-- None --</option>
        <?php while($c = $cols->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-row">
      <label>Description</label>
      <textarea name="description" rows="4" placeholder="Describe the product..."></textarea>
    </div>

    <div class="form-row">
      <label>Image</label>
      <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
      <img id="imgPreview" class="img-preview" alt="Preview">
    </div>

    <button class="btn">💾 Save Product</button>
  </form>
</div>

<script>
function previewImage(e){
  const p = document.getElementById('imgPreview');
  const f = e.target.files[0];
  if (!f){ p.style.display='none'; return; }
  p.src = URL.createObjectURL(f);
  p.classList.add('show');
}
</script>

<?php require 'admin_footer.php'; ?>
