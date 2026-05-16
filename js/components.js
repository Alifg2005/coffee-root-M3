//  NAVBAR (dynamic based on pageType) 
function getNavbarHTML() {
  // Shared section: logo and main nav links
  const sharedTop = `
    <a href="Main.php" class="logo">
      <img src="../img/Coffee Roots.png" alt="Coffee Roots Logo" class="logo-img" />
      <span class="logo-text">Coffee Roots</span>
    </a>

    <nav class="nav-links">
      <a href="Main.php#products">Products</a>
      <a href="Main.php#contact">Contact</a>
    </nav>
  `;

  // Right side changes based on page type
  let rightSide = "";

  if (pageType === "admin") {
    // Admin view: search, logout and financial statements
    rightSide = `
      <div class="nav-right">
        <div class="nav-search">
          <span class="search-icon">&#128269;</span>
          <input type="text" placeholder="Search products..." />
        </div>

        <a href="login.php" class="icon-btn logout-btn" aria-label="Logout" onclick="logout()">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>

        <a href="#" class="icon-btn cart-btn" aria-label="Financial Statements">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </a>
      </div>
    `;
}
  else if (pageType === "guest") {
    // Login / signup pages: auth buttons
    rightSide = `
      <div class="nav-right">
        <a href="login.php" class="btn btn-dark">Login</a>
        <a href="signup.php" class="btn btn-outline">Sign Up</a>
      </div>
    `;
  } 
  else {
    // Default customer view: search,account and cart
    rightSide = `
      <div class="nav-right">
        <div class="nav-search">
          <span class="search-icon">&#128269;</span>
          <input type="text" placeholder="Search products..." />
        </div>
        <a href="login.php" class="icon-btn" aria-label="Account">
          <span class="account-icon">&#128100;</span>
        </a>
        <a href="cart.php" class="icon-btn cart-btn" aria-label="Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
        </a>
      </div>
    `;
  }

  return `
    <header class="navbar">
      <div class="navbar-inner">
        ${sharedTop}
        ${rightSide}
      </div>
    </header>
  `;
}

//  FOOTER (same page for all ) 
const footerHTML = `
<footer class="footer">
  <div class="footer-inner">

    <div class="footer-col brand-col">
      <div class="logo footer-logo">
        <img src="../img/Coffee Roots.png" alt="logo" class="logo-img" />
        <span>Coffee Roots</span>
      </div>
      <p>Sourced with care. Roasted with passion. Delivered fresh.</p>
      <small>© 2026 Coffee Roots. All rights reserved.</small>
    </div>

    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="Main.php">Home</a>
      <a href="Main.php#products">Products</a>
      <a href="Main.php#contact">Contact Us</a>
      <a href="admin-dashboard.php">Admin</a>
    </div>

    <div class="footer-col">
      <h4>Shop</h4>
      <a href="#">Coffee Beans</a>
      <a href="#">Ground Coffee</a>
      <a href="#">Blends</a>
      <a href="#">Accessories</a>
    </div>

    <div class="footer-col">
      <h4>Contact</h4>
      <p><i class="fa-solid fa-location-dot"></i> Imam Abdulrahman Bin Faisal University, Dammam, Saudi Arabia</p>
      <p><i class="fa-solid fa-phone"></i> (+966) 55 444 777</p>
      <p><i class="fa-solid fa-envelope"></i> coffeeroots@iau.edu.sa</p>
    </div>

  </div>
</footer>
`;

//  SHARED LOGOUT FUNCTION (used in admin navbar) 
function logout() {
  // Clear session/user data
  localStorage.removeItem("user");
  sessionStorage.clear();
  // Redirect to login page
  window.location.href = "login.php";
}

//  INJECT COMPONENTS INTO PAGE 
document.addEventListener("DOMContentLoaded", () => {
  // Fallback if pageType wasn't defined in the HTML page
  if (typeof pageType === "undefined") {
    window.pageType = "customer";
  }

  const navbarPlaceholder = document.getElementById("navbar-placeholder");
  const footerPlaceholder = document.getElementById("footer-placeholder");

  if (navbarPlaceholder) navbarPlaceholder.innerHTML = getNavbarHTML();
  if (footerPlaceholder) footerPlaceholder.innerHTML = footerHTML;

  console.log("Components loaded as:", pageType);
});