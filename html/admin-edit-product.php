<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

// Get ID from URL
$id_coffee = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// HANDLE UPDATE LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    
    $name = $_POST['coffee_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $kcal = empty($_POST['kcal']) ? 0 : $_POST['kcal'];
    
    // Update Coffee Table
    $stmt = $pdo->prepare("UPDATE coffee SET coffee_name=?, category=?, description=?, price=?, stock=?, kcal=? WHERE id_coffee=?");
    $stmt->execute([$name, $category, $description, $price, $stock, $kcal, $id_coffee]);
    
    // Optional: Handle new image upload if they selected one
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_file = "../img/" . basename($_FILES["product_image"]["name"]);
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $img_stmt = $pdo->prepare("UPDATE images SET img_path=? WHERE id_coffee=?");
            $img_stmt->execute([$target_file, $id_coffee]);
        }
    }
    
    $message = "<script>alert('Product updated successfully!'); window.location.href='admin-dashboard.php';</script>";
}

// FETCH CURRENT PRODUCT DATA TO FILL THE FORM
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Product | Coffee Roots</title>
  <link rel="stylesheet" href="../css/admin-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
</head>
<body>
  <?= $message ?>
  <div id="navbar-placeholder"></div>

  <main class="add-product-page">
    <div class="add-product-box">
      <h1>Edit Product</h1>
      <p class="form-subtitle">Update the product details below</p>

      <form class="add-product-form" action="" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" name="coffee_name" value="<?= htmlspecialchars($product['coffee_name']) ?>" required />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Category</label>
            <select name="category" required>
              <option value="Coffee Beans" <?= $product['category'] == 'Coffee Beans' ? 'selected' : '' ?>>Coffee Beans</option>
              <option value="Ground Coffee" <?= $product['category'] == 'Ground Coffee' ? 'selected' : '' ?>>Ground Coffee</option>
              <option value="Accessories" <?= $product['category'] == 'Accessories' ? 'selected' : '' ?>>Accessories</option>
            </select>
          </div>

          <div class="form-group">
            <label>Calories (kcal)</label>
            <input type="number" name="kcal" value="<?= htmlspecialchars($product['kcal']) ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required />
          </div>

          <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required />
          </div>
        </div>

        <div class="form-group">
          <label>Product Image</label>
          <div class="current-image">
            <img src="<?= htmlspecialchars($product['img_path'] ?? '../img/Coffee Roots.png') ?>" alt="Current product image" class="preview-img" style="width:100px; border-radius:10px; margin-bottom:10px;" />
            <p class="current-image-label">Current image</p>
          </div>
          <input type="file" name="product_image" accept="image/*" />
        </div>

        <div class="form-actions">
          <a href="admin-dashboard.php" class="cancel-btn">Cancel</a>
          <button type="submit" name="edit_product" class="save-btn">Save Changes</button>
        </div>

      </form>
    </div>
  </main>

  <div id="footer-placeholder"></div>
  <script>const pageType = "admin";</script>
  <script src="../js/components.js"></script>
</body>
</html>