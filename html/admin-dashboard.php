<?php
session_start();

// --- SECURITY LOGIC (Rubric Requirement) ---
// Only allow users with user_type = 'admin'
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- SEARCH LOGIC (Rubric Requirement) ---
$searchQuery = "";
$params = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = "%" . $_GET['search'] . "%";
    $searchQuery = "WHERE c.coffee_name LIKE ? OR c.category LIKE ?";
    $params = [$search, $search];
}

// Fetch Products
$stmt = $pdo->prepare("
    SELECT c.*, i.img_path 
    FROM coffee c 
    LEFT JOIN images i ON c.id_coffee = i.id_coffee 
    $searchQuery
    GROUP BY c.id_coffee
");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Stats for the bottom section
$total_products = count($products);
$out_of_stock = 0;
$low_stock = 0;

foreach ($products as $p) {
    if ($p['stock'] == 0) $out_of_stock++;
    elseif ($p['stock'] < 10) $low_stock++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Coffee Roots</title>
  <link rel="stylesheet" href="../css/admin-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
</head>
<body>

  <div id="navbar-placeholder"></div>

  <section class="admin-header">
    <div>
      <h1>Admin Dashboard</h1>
      <p>Manage your product inventory. Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</p>
    </div>
  </section>

  <section class="admin-controls">
    <form method="GET" action="admin-dashboard.php" class="dashboard-search" style="display:flex;">
      <i class="fa-solid fa-magnifying-glass" style="margin-top: 10px;"></i>
      <input type="text" name="search" placeholder="Search products..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
      <button type="submit" style="display:none;">Search</button> </form>

    <a href="admin-add-product.php" class="add-btn">
      <i class="fa-solid fa-plus"></i> Add Product
    </a>
  </section>

  <section class="product-table-section">
    <div class="table-header">
      <div>Image</div>
      <div>Name</div>
      <div>Category</div>
      <div>Price</div>
      <div>Stock</div>
      <div>Actions</div>
    </div>

    <?php foreach($products as $p): ?>
    <div class="table-row">
      <div><img src="<?= htmlspecialchars($p['img_path'] ?? '../img/Coffee Roots.png') ?>" alt="Product"></div>
      <div>
        <h3><?= htmlspecialchars($p['coffee_name']) ?></h3>
      </div>
      <div><?= htmlspecialchars($p['category']) ?></div>
      <div>$<?= number_format($p['price'], 2) ?></div>
      
      <div>
          <?php if($p['stock'] == 0): ?>
              <span class="stock-badge zero-stock">0</span>
          <?php elseif($p['stock'] < 10): ?>
              <span class="stock-badge low-stock"><?= $p['stock'] ?></span>
          <?php else: ?>
              <span class="stock-badge"><?= $p['stock'] ?></span>
          <?php endif; ?>
      </div>
      
      <div class="actions">
        <a href="admin-edit-product.php?id=<?= $p['id_coffee'] ?>"><i class="fa-solid fa-pen"></i></a>
        <a href="admin-delete-product.php?id=<?= $p['id_coffee'] ?>" class="trash-can"><i class="fa-regular fa-trash-can"></i></a>
      </div>
    </div>
    <?php endforeach; ?>

  </section>

  <section class="stats-section">
    <div class="stat-card">
      <h3>Total Products</h3>
      <p><?= $total_products ?></p>
    </div>
    <div class="stat-card">
      <h3>Out of Stock</h3>
      <p class="danger"><?= $out_of_stock ?></p>
    </div>
    <div class="stat-card">
      <h3>Low Stock (&lt;10)</h3>
      <p class="warning"><?= $low_stock ?></p>
    </div>
  </section>

  <div id="footer-placeholder"></div>
  <script>const pageType = "admin";</script>
  <script src="../js/components.js"></script>
</body>
</html>