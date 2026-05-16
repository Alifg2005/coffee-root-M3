<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id_coffee = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// HANDLE DELETE LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    // Delete from coffee table. 
    // (Because your schema has ON DELETE CASCADE, this automatically deletes the image link too!)
    $stmt = $pdo->prepare("DELETE FROM coffee WHERE id_coffee = ?");
    $stmt->execute([$id_coffee]);
    
    echo "<script>alert('Product Deleted!'); window.location.href='admin-dashboard.php';</script>";
    exit();
}

// FETCH DATA FOR PREVIEW
$stmt = $pdo->prepare("SELECT c.*, i.img_path FROM coffee c LEFT JOIN images i ON c.id_coffee = i.id_coffee WHERE c.id_coffee = ?");
$stmt->execute([$id_coffee]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Product | Coffee Roots</title>
  <link rel="stylesheet" href="../css/admin-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

  <div id="navbar-placeholder"></div>

  <main class="delete-page">
    <div class="delete-box">

      <h1>Delete Product</h1>
      <p class="warning-text">Are you sure you want to delete this product?</p>

      <div class="product-preview">
        <img src="<?= htmlspecialchars($product['img_path'] ?? '../img/Coffee Roots.png') ?>" alt="product" style="width:100px; border-radius:10px;">

        <div class="product-details" style="margin-left: 20px;">
          <h3><?= htmlspecialchars($product['coffee_name']) ?></h3>

          <div class="info-row">
            <span>Category:</span>
            <strong><?= htmlspecialchars($product['category']) ?></strong>
          </div>

          <div class="info-row">
            <span>Price:</span>
            <strong>$<?= number_format($product['price'], 2) ?></strong>
          </div>

          <div class="info-row">
            <span>Stock:</span>
            <strong><?= htmlspecialchars($product['stock']) ?></strong>
          </div>
        </div>
      </div>

      <div class="delete-actions" style="margin-top: 30px;">
        <a href="admin-dashboard.php" class="cancel-btn">Cancel</a>

        <form method="POST" action="" style="display:inline;">
            <button type="submit" name="confirm_delete" class="confirm-btn" style="border:none; cursor:pointer;">
              Confirm Delete
            </button>
        </form>
      </div>

    </div>
  </main>

  <div id="footer-placeholder"></div>
  <script>const pageType = "admin";</script>
  <script src="../js/components.js"></script>

</body>
</html>