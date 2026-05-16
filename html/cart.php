<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=coffee_roots;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- CART MODIFICATION LOGIC (Rubric: Delete, modify, empty) ---

// 1. Empty Cart
if (isset($_POST['empty_cart'])) {
    $_SESSION['cart'] = [];
    $message = "<script>alert('Cart Emptied!');</script>";
}

// 2. Remove Specific Item
if (isset($_POST['remove_item'])) {
    $id_to_remove = $_POST['coffee_id'];
    unset($_SESSION['cart'][$id_to_remove]);
}

// 3. Modify Quantity
if (isset($_POST['update_qty'])) {
    $id_to_update = $_POST['coffee_id'];
    $new_qty = (int)$_POST['quantity'];
    
    // Rubric: Check against stock
    $stmt = $pdo->prepare("SELECT stock FROM coffee WHERE id_coffee = ?");
    $stmt->execute([$id_to_update]);
    $stock_available = $stmt->fetchColumn();

    if ($new_qty > $stock_available) {
        $message = "<script>alert('Only $stock_available left in stock!');</script>";
    } elseif ($new_qty <= 0) {
        unset($_SESSION['cart'][$id_to_update]); // Remove if qty is 0
    } else {
        $_SESSION['cart'][$id_to_update]['quantity'] = $new_qty;
    }
}

// --- CHECKOUT LOGIC (Rubric: Buy Product, Empty Cart, Update DB, Cookie) ---
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $message = "<script>alert('Your cart is empty!');</script>";
    } else {
        try {
            $pdo->beginTransaction(); // Start DB transaction

            // Loop through cart and update database stock
            foreach ($_SESSION['cart'] as $id => $item) {
                $qty = $item['quantity'];
                // Update database to detect quantity (Rubric requirement)
                $stmt = $pdo->prepare("UPDATE coffee SET stock = stock - ? WHERE id_coffee = ? AND stock >= ?");
                $stmt->execute([$qty, $id, $qty]);
                
                if ($stmt->rowCount() == 0) {
                    throw new Exception("Not enough stock for " . $item['name']);
                }
            }

            $pdo->commit(); // Save changes to DB

            // Set Cookie for Past Purchases (Rubric requirement)
            setcookie("last_purchase_date", date("Y-m-d H:i:s"), time() + (86400 * 30), "/"); // 30 days

            // Empty Cart
            $_SESSION['cart'] = [];
            $message = "<script>alert('Purchase Successful! Stock updated.'); window.location.href='main.php';</script>";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "<script>alert('Checkout failed: " . $e->getMessage() . "');</script>";
        }
    }
}

// Calculate Totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Cart - Coffee Roots</title>
  <link rel="stylesheet" href="../css/cart-style.css" />
  <link rel="stylesheet" href="../css/Super-style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

  <?= $message ?>
  <div id="navbar-placeholder"></div>

  <main class="cart-section">
    <div class="cart-header">
      <h1 class="cart-title">Your Cart</h1>
      <div class="cart-header-actions">
        <a href="main.php#products" class="continue-shopping">&larr; Continue Shopping</a>
        <form method="POST" style="display:inline;">
            <button type="submit" name="empty_cart" class="clear-cart-btn">
            <i class="fa-solid fa-trash"></i> Clear All
            </button>
        </form>
      </div>
    </div>

    <div class="cart-container">
      <div class="cart-items" id="cart-items">
        
        <?php if(empty($_SESSION['cart'])): ?>
            <p>Your cart is currently empty.</p>
        <?php else: ?>
            <?php foreach($_SESSION['cart'] as $id => $item): ?>
            <div class="cart-item">
              <img src="<?= htmlspecialchars($item['image']) ?>" alt="Coffee" class="cart-item-img" />
              <div class="cart-item-info">
                <h3 class="cart-item-name"><?= htmlspecialchars($item['name']) ?></h3>
              </div>
              
              <div class="cart-item-qty">
                <form method="POST" style="display:flex; align-items:center;">
                    <input type="hidden" name="coffee_id" value="<?= $id ?>">
                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width: 50px; padding: 5px;">
                    <button type="submit" name="update_qty" style="margin-left:5px; padding: 5px; background: #6f5a3e; color: white; border: none; border-radius: 3px; cursor: pointer;">Update</button>
                </form>
              </div>

              <div class="cart-item-price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
              
              <form method="POST">
                  <input type="hidden" name="coffee_id" value="<?= $id ?>">
                  <button type="submit" name="remove_item" class="cart-item-remove" aria-label="Remove item"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

      </div>

      <div class="cart-summary">
        <h3 class="summary-title">Order Summary</h3>
        <div class="summary-row">
          <span>Subtotal</span>
          <span>$<?= number_format($subtotal, 2) ?></span>
        </div>
        <hr class="summary-divider" />
        <div class="summary-row summary-total">
          <span>Total</span>
          <span>$<?= number_format($subtotal, 2) ?></span>
        </div>
        
        <form method="POST">
            <div class="payment-methods">
              <h4 class="payment-title">Payment Method</h4>
              <label class="payment-option"><input type="radio" name="payment" value="card" checked /> Credit Card</label>
              <label class="payment-option"><input type="radio" name="payment" value="cash" /> Cash on Delivery</label>
            </div>
            <button type="submit" name="checkout" class="btn btn-dark checkout-btn">Proceed to Checkout</button>
        </form>
      </div>
    </div>
  </main>

  <div id="footer-placeholder"></div>
  <script>const pageType = "customer";</script>
  <script src="../js/components.js"></script>
</body>
</html>