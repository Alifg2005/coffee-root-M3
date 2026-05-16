<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

// HANDLE ADD PRODUCT LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    
    $name = $_POST['coffee_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $kcal = empty($_POST['kcal']) ? 0 : $_POST['kcal']; // Default to 0 if empty
    
    try {
        $pdo->beginTransaction(); // Start safe transaction
        
        // 1. Insert into coffee table
        $stmt = $pdo->prepare("INSERT INTO coffee (coffee_name, category, description, price, stock, kcal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $description, $price, $stock, $kcal]);
        
        // Get the ID of the coffee we just added
        $new_coffee_id = $pdo->lastInsertId();
        
        // 2. Handle Image Upload
        if (isset($_FILES['product_image'])) {
            if ($_FILES['product_image']['error'] == 0) {
                $target_dir = "../img/";
                
                // Check if the img folder actually exists
                if (!is_dir($target_dir)) {
                    throw new Exception("The folder '../img/' does not exist! Please create it.");
                }

                $image_name = basename($_FILES["product_image"]["name"]);
                $target_file = $target_dir . $image_name;
                
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    // Insert into images table
                    $img_stmt = $pdo->prepare("INSERT INTO images (img_name, img_path, id_coffee) VALUES (?, ?, ?)");
                    $img_stmt->execute([$image_name, $target_file, $new_coffee_id]);
                } else {
                    throw new Exception("Failed to move the uploaded file. Check folder permissions.");
                }
            } else {
                throw new Exception("PHP File Upload Error Code: " . $_FILES['product_image']['error']);
            }
        }
        
        $pdo->commit();
        $message = "<script>alert('Product added successfully!'); window.location.href='admin-dashboard.php';</script>";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<script>alert('Failed to add product: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Product | Coffee Roots</title>
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
      <h1>Add Product</h1>
      <p class="form-subtitle">Fill in the product details below</p>

      <form class="add-product-form" action="" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" name="coffee_name" placeholder="Enter product name" required />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4" placeholder="Enter product description"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Category</label>
            <select name="category" required>
              <option value="">Select category</option>
              <option value="Coffee Beans">Coffee Beans</option>
              <option value="Ground Coffee">Ground Coffee</option>
              <option value="Blends">Blends</option>
              <option value="Accessories">Accessories</option>
              <option value="Brewing Equipment">Brewing Equipment</option>
            </select>
          </div>

          <div class="form-group">
            <label>Calories (kcal)</label>
            <input type="number" name="kcal" placeholder="e.g. 5" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" step="0.01" placeholder="Enter price" required />
          </div>

          <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" placeholder="Enter stock quantity" required />
          </div>
        </div>

        <div class="form-group">
          <label>Product Image</label>
          <input type="file" name="product_image" accept="image/*" required />
        </div>

        <div class="form-actions">
          <a href="admin-dashboard.php" class="cancel-btn">Cancel</a>
          <button type="submit" name="add_product" class="save-btn">Add Product</button>
        </div>
      </form>
    </div>
  </main>

  <div id="footer-placeholder"></div>
  <script>const pageType = "admin";</script>
  <script src="../js/components.js"></script>

</body>
</html>