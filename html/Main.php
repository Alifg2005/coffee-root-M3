<?php
session_start();

// Connect to your custom coffee_roots database
$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch all coffee products AND their images from the database
// We use a LEFT JOIN to combine the 'coffee' table and 'images' table based on id_coffee
$stmt = $pdo->query("
    SELECT c.*, i.img_path 
    FROM coffee c 
    LEFT JOIN images i ON c.id_coffee = i.id_coffee 
    GROUP BY c.id_coffee
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coffee Roots</title>
  <link rel="stylesheet" href="../css/Main-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

  <div id="navbar-placeholder"></div>

  <section class="products" id="products">
    <div class="section-header">
      <p class="section-tag">Our Menu</p>
      <h2 class="section-title">Explore Our Collection</h2>
      <p class="section-sub">Handpicked beans from the world's finest growing regions.</p>
    </div>

    <div class="filter-bar">
      <button class="filter-btn active">All</button>
      <button class="filter-btn">Coffee Beans</button>
      <button class="filter-btn">Ground Coffee</button>
      <button class="filter-btn">Blends</button>
      <button class="filter-btn">Accessories</button>
    </div>

    <div class="product-grid">

      <?php foreach($products as $product): ?>
      
      <div class="product-card" onclick="window.location.href='product-details.php?id=<?= $product['id_coffee'] ?>'">
        
        <div class="product-img">
          <img src="<?= htmlspecialchars($product['img_path'] ?? '../img/Coffee Roots.png') ?>" alt="<?= htmlspecialchars($product['coffee_name']) ?>" class="product-img-photo"></img>
          <span class="product-badge"><?= htmlspecialchars($product['category'] ?? 'Coffee Beans') ?></span>
        </div>
        
        <div class="product-info">
          <p class="product-category"><?= htmlspecialchars($product['category'] ?? 'Coffee Beans') ?></p>
          
          <h3 class="product-name"><?= htmlspecialchars($product['coffee_name']) ?></h3>
          
          <p class="product-desc"><?= htmlspecialchars($product['description'] ?? 'Delicious, freshly roasted coffee.') ?></p>
          
          <div class="product-footer">
            <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
            <a href="product-details.php?id=<?= $product['id_coffee'] ?>" class="add-btn">+ Add</a>
          </div>
        </div>

      </div>
      <?php endforeach; ?>
      </div>
  </section>

  <div id="footer-placeholder"></div>

  <script>
    const pageType = "customer";
  </script>
  <script src="../js/components.js"></script>

</body>
</html>