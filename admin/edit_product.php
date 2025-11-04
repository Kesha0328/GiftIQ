<?php
require 'admin_header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo "<div class='card'>Invalid product id.</div>"; require 'admin_footer.php'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $collection_id = intval($_POST['collection_id'] ?? 0);

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('p_').'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__."/../uploads/".$imageName);
        $imgSql = ", image='".$conn->real_escape_string($imageName)."'";
    } else $imgSql = "";

    $sql = "UPDATE products SET name='".$conn->real_escape_string($name)."', price=$price, description='".$conn->real_escape_string($description)."', collection_id=$collection_id $imgSql WHERE id=$id";
    if ($conn->query($sql)) $msg = "Updated";
    else $err = $conn->error;
}

$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$cols = $conn->query("SELECT id, name FROM collections");
?>
<head>
    <link rel="icon" type="image/png" href="../uploads/favicon.png" />
</head>

<style>

body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(180deg, #0c0c0e, #101012);
  color: #f8f8f8;
  padding: 40px 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(233, 184, 154, 0.12);
  border-radius: 18px;
  padding: 28px 36px;
  max-width: 700px;
  margin: 25px auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  transition: all 0.3s ease;

}

.card:hover {
  border-color: rgba(233, 184, 154, 0.25);
}

.card h3 {
  font-size: 1.3rem;
  color: var(--accent, #f0a77c);
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 18px;
}
.card h3::before {
  content: "✏️";
}

label {
  font-weight: 600;
  font-size: 0.95rem;
  color: #f7bfa5;
  margin-bottom: 6px;
  display: inline-block;
}

input[type="text"],
input[type="number"],
textarea,
select {
  width: 100%;
  padding: 10px 12px;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: #fff;
  font-size: 0.95rem;
  transition: 0.3s ease;
  outline: none;
}

input:focus,
textarea:focus,
select:focus {
  border-color: #e9b89a;
  box-shadow: 0 0 10px rgba(233, 184, 154, 0.4);
}

textarea {
  min-height: 80px;
  resize: vertical;
}

select {
  appearance: none;
  background: rgba(255, 255, 255, 0.07) url("data:image/svg+xml;utf8,<svg fill='white' height='14' width='14' xmlns='http://www.w3.org/2000/svg'><path d='M2 4l5 6 5-6z'/></svg>") no-repeat right 12px center;
  background-size: 12px;
  cursor: pointer;
}
select option {
  background: #1b1b1b;
  color: #fff;
  padding: 10px;
}

.card img {
  width: 300px;
  border-radius: 12px;
  margin-top: 8px;
  border: 1px solid rgba(255, 255, 255, 0.05);
  transition: transform 0.3s ease;
}
.card img:hover {
  transform: scale(1.01);
}

input[type="file"] {
  color: #ccc;
  border: none;
  background: transparent;
  font-size: 0.9rem;
  margin-top: 8px;

}

.btn {
  display: inline-block;
  background: linear-gradient(135deg, #e9b89a, #d38b5d);
  color: #0d0d0d;
  font-weight: 600;
  padding: 10px 18px;
  border-radius: 10px;
  margin-top: 10px;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
}
.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(233, 184, 154, 0.35);
}

.btn.ghost {
  background: transparent;
  border: 1px solid rgba(233, 184, 154, 0.25);
  color: #e9b89a;
}
.btn.ghost:hover {
  background: rgba(233, 184, 154, 0.1);
  box-shadow: 0 0 12px rgba(233, 184, 154, 0.25);
}

/* --- Responsive --- */
@media (max-width: 600px) {
  .card {
    padding: 22px;
    width: 90%;
  }
}
</style>

<div class="card">
  <h3>✏️ Edit Product #<?= $id ?></h3>
  <?php if (!empty($msg)): ?><div class="notice success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if (!empty($err)): ?><div class="notice error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Name</label><input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required></div>
    <div class="form-row"><label>Price</label><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required></div>
    <div class="form-row"><label>Collection</label>
      <select name="collection_id">
        <option value="0">-- Select --</option>
        <?php while($c=$cols->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>" <?= $product['collection_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-row"><label>Description</label><textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea></div>

    <div class="form-row">
      <label>Existing Image</label>
      <?php if (!empty($product['image'])): ?>
        <div><img src="/../uploads/<?= htmlspecialchars($product['image']) ?>" class="img-preview"></div>
      <?php else: ?>
        <div style="color:var(--muted)">No image</div>
      <?php endif; ?>
    </div>

    <div class="form-row"><label>Replace Image</label><input type="file" name="image" accept="/../uploads/*" onchange="previewImage(event)"></div>
    <div style="margin-top:8px;"><img id="imgPreview" class="img-preview" style="display:none;"></div>

    <button class="btn ghost" >Update Product</button>
    <a class="btn ghost" href="manage_products.php">Back</a>
  </form>
</div>

<script>
function previewImage(e){
  const p = document.getElementById('imgPreview');
  const f = e.target.files[0];
  if (!f) { p.style.display='none'; return;}
  p.src = URL.createObjectURL(f); p.style.display='block';
}
</script>

<?php require 'admin_footer.php'; ?>
