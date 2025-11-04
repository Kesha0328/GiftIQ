<?php
require 'admin_header.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    echo "<div class='notice success'>Product deleted successfully.</div>";
}

$q = $conn->real_escape_string($_GET['q'] ?? '');
$where = $q ? "WHERE name LIKE '%$q%' OR description LIKE '%$q%'" : "";
$res = $conn->query("SELECT * FROM products $where ORDER BY id DESC");
?>

<style>
.card {
  padding: 20px;
  margin: 24px 0;
  border-radius: 14px;
  background: var(--card);
  box-shadow: var(--shadow);
}

.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 18px;
}

.header-bar h3 {
  margin: 0;
  color: var(--accent-2);
  font-size: 1.3rem;
}

.search-row {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 16px;
  background: var(--glass);
  padding: 12px 14px;
  border-radius: 10px;
}

.search-row input[type="text"] {
  flex: 1;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #fff;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 0.95rem;
  outline: none;
  transition: all 0.3s ease;
}

.search-row input[type="text"]:focus {
  border-color: var(--accent);
  box-shadow: 0 0 10px rgba(233, 184, 154, 0.3);
  background: rgba(255,255,255,0.08);
}

.search-row input::placeholder {
  color: #aaa;
  font-style: italic;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 14px;
}

.table th {
  text-align: left;
  padding: 12px 10px;
  background: rgba(255,255,255,0.03);
  color: var(--accent-2);
  font-weight: 600;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.table td {
  padding: 12px 10px;
  border-bottom: 1px dashed rgba(255,255,255,0.05);
  color: #e6e6e6;
  vertical-align: middle;
  transition: all 0.2s ease;
}

.table tr:hover td {
  background: rgba(197, 139, 106, 0.04);
}

.img-thumb {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.04);
  box-shadow: 0 3px 12px rgba(0,0,0,0.3);
}

.btn {
  display: inline-block;
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  border: 0;
  font-size: 0.9rem;
  transition: all .25s ease;
}

.btn {
  background: linear-gradient(90deg,var(--accent),var(--accent-2));
  color: #111;
  box-shadow: 0 4px 12px rgba(197,139,106,0.25);
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(233,184,154,0.35);
}

.btn.ghost {
  background: transparent;
  color: var(--accent-2);
  border: 1px solid rgba(233,184,154,0.3);
}
.btn.ghost:hover {
  background: rgba(233,184,154,0.1);
  box-shadow: 0 0 10px rgba(233,184,154,0.2);
}

.btn.danger {
  background: linear-gradient(90deg,#b33b3b,#d24c4c);
  color: #fff;
}
.btn.danger:hover {
  box-shadow: 0 0 10px rgba(210,76,76,0.4);
}

.notice.success {
  background: rgba(46,164,79,0.1);
  color: #d2f2d2;
  padding: 10px 14px;
  border-radius: 8px;
  margin-bottom: 12px;
}
</style>

<div class="card">
  <div class="header-bar">
    <h3>🎁 Manage Products</h3>
    <a class="btn" href="add_product.php">+ Add New</a>
  </div>

  <div class="search-row">
    <form method="get" style="display:flex; gap:10px; width:100%;">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="🔍 Search products...">
      <button class="btn ghost" type="submit">Search</button>
      <a class="btn ghost" href="manage_products.php">Reset</a>
    </form>
  </div>

  <table class="table">
    <thead>
      <tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php while($p=$res->fetch_assoc()): ?>
      <tr>
        <td>#<?= $p['id'] ?></td>
        <td>
          <?php if(!empty($p['image'])): ?>
            <img src="/GiftIQ-main/uploads/<?= htmlspecialchars($p['image']) ?>" class="img-thumb">
          <?php else: ?>
            <span style="color:var(--muted)">—</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td>₹<?= number_format($p['price'],2) ?></td>
        <td>
          <a class="btn ghost" href="edit_product.php?id=<?= $p['id'] ?>">Edit</a>
          <a class="btn danger" href="manage_products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete product?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require 'admin_footer.php'; ?>
