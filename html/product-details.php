<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Get the ID from the URL (e.g., ?id=5)
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Fetch that specific product from the DB
$stmt = $pdo->prepare("
    SELECT c.*, i.img_path 
    FROM coffee c 
    LEFT JOIN images i ON c.id_coffee = i.id_coffee 
    WHERE c.id_coffee = ? 
    LIMIT 1
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If someone types an invalid ID, stop loading
if (!$product) {
    die("<h2>Product not found in database.</h2>");
}

$message = "";

// 3. HANDLE ADD TO CART (Rubric requirement)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $requested_qty = (int)$_POST['quantity'];
    
    // RUBRIC: Check quantity against stock
    if ($requested_qty > $product['stock']) {
        $message = "<script>alert('Sorry, we only have {$product['stock']} left in stock.');</script>";
    } else {
        // Initialize cart session if empty
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add to session cart
        $_SESSION['cart'][$product_id] = [
            'id_coffee' => $product['id_coffee'],
            'name' => $product['coffee_name'],
            'price' => $product['price'],
            'quantity' => $requested_qty,
            'image' => $product['img_path']
        ];
        $message = "<script>alert('Added to cart successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($product['coffee_name']) ?> | Coffee Roots</title>
  <link rel="stylesheet" href="../css/admin-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
  <style>
      .qty-input { padding: 8px; width: 70px; margin-right: 15px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;}
  </style>
</head>
<body>
  
  <?= $message ?> <div id="navbar-placeholder"></div>

  <main class="product-details-page">
    <div class="product-details-container">

      <div class="product-image-card">
        <img src="<?= htmlspecialchars($product['img_path'] ?? '../img/Coffee Roots.png') ?>" alt="<?= htmlspecialchars($product['coffee_name']) ?>" class="product-details-image" />
      </div>

      <div class="product-info-card">
        <span class="product-badge"><?= htmlspecialchars($product['category'] ?? 'Coffee Beans') ?></span>
        
        <h1><?= htmlspecialchars($product['coffee_name']) ?></h1>
        <div class="product-price">$<?= number_format($product['price'], 2) ?></div>

        <p class="product-description">
          <?= htmlspecialchars($product['description'] ?? 'A premium selection from Coffee Roots.') ?>
        </p>

        <div class="product-meta">
          <div class="meta-item">
            <span class="meta-label">Calories</span>
            <strong><?= htmlspecialchars($product['kcal']) ?> kcal</strong>
          </div>
          <div class="meta-item">
            <span class="meta-label">Stock</span>
            <strong><?= htmlspecialchars($product['stock']) ?> Available</strong>
          </div>
        </div>

        <form method="POST" action="" class="product-actions" style="display: flex; align-items: center; margin-top: 20px;">
          <label for="qty" style="margin-right: 10px; font-weight: bold;">Qty:</label>
          <input type="number" id="qty" name="quantity" class="qty-input" value="1" min="1" max="<?= htmlspecialchars($product['stock']) ?>" required>
          
          <button type="submit" name="add_to_cart" class="buy-btn" style="border:none; cursor:pointer;">
            <i class="fa-solid fa-cart-plus"></i> Add To Cart
          </button>
          <a href="main.php#products" class="back-btn" style="margin-left: 15px;">Back</a>
        </form>

        <button onclick="openHelpWindow()" style="margin-top: 20px; background: transparent; border: 1px solid #2c1a0e; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
          Need Help?
        </button>

      </div>
    </div>
  </main>

  <div id="footer-placeholder"></div>

  <script>
    const pageType = "customer";
    
    // JS Function for the rubric "Help Window"
    function openHelpWindow() {
        alert("Help Window:\nIf you need assistance choosing a coffee, please review the Roast type or email us at coffeeroots@gmail.com.");
    }
  </script>
  <script src="../js/components.js"></script>

</body>
</html>