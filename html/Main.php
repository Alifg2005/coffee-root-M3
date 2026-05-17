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

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="hero-content">
      <p class="hero-label">Single Origin &amp; Specialty Blends</p>
      <h1 class="hero-title">From the Root<br/>to Your Cup</h1>
      <p class="hero-sub">We source the finest beans from small farms around the world, roasted with care and delivered fresh to your door.</p>
      <div class="hero-actions">
        <a href="#products" class="btn btn-dark">Shop Now</a>
        <a href="#about" class="btn btn-outline">Our Story</a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-card">
        <div class="hero-img-placeholder">
          <span class="logo2">
            <img src="../img/ethiopian yirgacheffe.jpg" alt="Coffee Roots Logo" class="logo-img3" />
          </span>
        </div>
        <div class="hero-card-text">
          <span class="card-tag">Featured Blend</span>
          <p class="card-name">Ethiopian Yirgacheffe</p>
          <p class="card-sub">Light Roast · Floral &amp; Citrus Notes</p>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS STRIP -->
  <section class="stats-strip">
    <div class="stat">
      <span class="stat-num">10+</span>
      <span class="stat-label">Origins</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <span class="stat-num">5+</span>
      <span class="stat-label">Happy Customers</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <span class="stat-num">100%</span>
      <span class="stat-label">Ethically Sourced</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <span class="stat-num">Fresh</span>
      <span class="stat-label">Roasted Weekly</span>
    </div>
  </section>

  <!-- PRODUCTS SECTION -->
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

  <!-- ABOUT SECTION -->
  <section class="about" id="about">
    <div class="about-text">
      <p class="section-tag">Our Story</p>
      <h2 class="section-title">Rooted in Craft,<br/>Driven by Passion</h2>
      <p>Coffee Roots was born from a simple belief: exceptional coffee starts long before the roast. We partner directly with small-scale farmers across Ethiopia, Colombia, Guatemala, and beyond, building relationships that are fair, transparent, and lasting.</p>
      <p>Every bag we sell represents months of careful cultivation, careful selection, and careful roasting. We do not cut corners because your cup deserves the best.</p>
      <a href="#contact" class="btn btn-dark">Get in Touch</a>
    </div>
    <div class="about-visual">
      <div class="about-img-box">
        <img src="../img/coffee farm.png" alt="Espresso Maker" class="big-icon"></img>
        <p class="about-img-caption">Farm-to-Cup Since 2015</p>
      </div>
      <div class="about-badges">
        <div class="about-badge">
          <span>&#10003;</span> Directly Traded
        </div>
        <div class="about-badge">
          <span>&#10003;</span> Small Batch Roasted
        </div>
        <div class="about-badge">
          <span>&#10003;</span> Carbon Neutral Shipping
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT SECTION -->
  <section class="contact" id="contact">
    <div class="section-header">
      <p class="section-tag">Get In Touch</p>
      <h2 class="section-title">We'd Love to Hear From You</h2>
    </div>

    <div class="contact-grid">

      <div class="contact-info">
        <div class="contact-item">
          <span class="contact-icon">📍</span>
          <div>
            <strong>Visit Us</strong>
            <p>Imam Abdulrahman Bin Faisal University</p>
          </div>
        </div>
        <div class="contact-item">
          <span class="contact-icon">📞</span>
          <div>
            <strong>Call Us</strong>
            <p> (+966) 55 444 777</p>
          </div>
        </div>
        <div class="contact-item">
          <span class="contact-icon">✉</span>
          <div>
            <strong>Email Us</strong>
            <p>coffeeroots@gmail.com</p>
          </div>
        </div>
      </div>

      <form class="contact-form">
        <input type="text" placeholder="Your Name" />
        <input type="email" placeholder="Email Address" />
        <textarea placeholder="Your message..." rows="4"></textarea>
        <button type="submit" class="btn btn-dark">Send Message</button>
      </form>

      <div class="map-container">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3575.466548174542!2d50.18840277636502!3d26.39579177695922!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49e81b6aa50001%3A0xd64d4b295cb68d83!2sImam%20Abdulrahman%20Bin%20Faisal%20University!5e0!3m2!1sen!2ssa!4v1700000000000!5m2!1sen!2ssa"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>

    </div>
  </section>

  <div id="footer-placeholder"></div>

  <script>
    const pageType = "customer";
  </script>
  <script src="../js/components.js"></script>

</body>
</html>