<?php
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - SJ Computer</title>
    <script src="https://kit.fontawesome.com/cca3f4e97d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">     
</head>
<body>

<div class="container">
    <div class="navbar">
        <div class="logo">
            <img src="images/Logo.png" alt="Logo">
            <span class="business-name">SJ Computer</span>
        </div>

        <nav>
            <ul id="MenuItems">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart <?php if (isLoggedIn()): ?>(<?php echo getCartItemCount($_SESSION['user_id']); ?>)<?php endif; ?></a></li>
                <li><a href="services.php">Services</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signin.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<div class="smallContainer">
    <h1>Repair Services</h1>
    <div class="services-grid">
        <div class="service-card">
            <div class="pic">
                <img src="images/upgrade.png" alt="OS Installation" width="250px">
            </div>
            
            <div class="text">
                <h2>OS Installation</h2>
                <p>Install or reinstall operating systems</p>
                <h4><strong>₱500.00</strong></h4>
                <a href="#" class="book-btn" data-service="OS Installation">Book Now</a>
            </div>
        </div>

        <div class="service-card">
            <div class="pic">
                <img src="images/upgrade.png" alt="Laptop Diagnostic" width="250px">
            </div>
            
            <div class="text">
                <h2>Laptop Diagnostic</h2>
                <p>Full system diagnostic to identify issues</p>
                <h4><strong>₱200.00</strong></h4>
                <a href="#" class="book-btn" data-service="Laptop Diagnostic">Book Now</a>
            </div>
        </div>
    </div>

    <div class="services-grid">
        <div class="service-card">
            <div class="pic">
                <img src="images/upgrade.png" alt="Virus Removal" width="250px">
            </div>
            
            <div class="text">
                <h2>Virus Removal</h2>
                <p>Remove malware, spyware, and viruses for a safer system</p>
                <h4><strong>₱350.00</strong></h4>
                <a href="#" class="book-btn" data-service="Virus Removal">Book Now</a>
            </div>
        </div>

        <div class="service-card">
            <div class="pic">
                <img src="images/upgrade.png" alt="System Cleanup" width="250px">
            </div>
            
            <div class="text">
                <h2>Full System Cleanup</h2>
                <p>Optimize performance by cleaning files and fixing system clutter</p>
                <h4><strong>₱300.00</strong></h4>
                <a href="#" class="book-btn" data-service="Full System Cleanup">Book Now</a>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="customerSupport">
                <h3>Customer Support</h3>
                <ul>
                    <li>FAQs</li>
                    <li>Return Policy</li>
                    <li>Warranty</li>
                </ul>
            </div>
            <div class="sjlogo">
                <img src="images/Logo.png" alt="">
                <h3>SJ Computer</h3>
                <h4>Location</h4>
                <p>Manlapaz Bldg. F. Pimentel Ave. Daet, Camarines Norte</p>
            </div>
            <div class="socMed">
                <h3>Follow Us</h3>
                <ul>
                    <li>Facebook</li>
                    <li>Instagram</li>
                </ul>
                <h4>Contact Us</h4>
                <p>0987654321</p>
            </div>
        </div>

        <div class="copyright">
            <hr>
            <p>&copy; 2025 SJ Computer. All rights reserved.</p>
        </div>
    </div>
</div>

<!-- Booking Form Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Book a Service</h2>
        <form id="bookingForm" method="post" action="processBooking.php">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <label for="serviceName">Service</label>
            <input type="text" id="serviceName" name="serviceName" readonly>

            <?php if (isLoggedIn()): ?>
                <input type="hidden" id="customerName" name="customerName" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">
                <input type="hidden" id="customerEmail" name="customerEmail" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
            <?php else: ?>
                <label for="customerName">Your Name</label>
                <input type="text" id="customerName" name="customerName" required>

                <label for="customerEmail">Email</label>
                <input type="email" id="customerEmail" name="customerEmail" required>
            <?php endif; ?>

            <label for="contactNumber">Contact Number</label>
            <input type="text" id="contactNumber" name="contactNumber" required>

            <label for="preferredDate">Preferred Date</label>
            <input type="date" id="preferredDate" name="preferredDate" required min="<?php echo date('Y-m-d'); ?>">

            <label for="problemDescription">Problem Description</label>
            <textarea id="problemDescription" name="problemDescription" rows="4"></textarea>

            <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="error-message" style="color: red; margin: 10px 0;">
                    <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
                </div>
            <?php endif; ?>

            <button type="submit">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("bookingModal");
    const closeBtn = document.querySelector(".close-btn");
    const bookingForm = document.getElementById("bookingForm");
    const serviceNameInput = document.getElementById("serviceName");

    document.querySelectorAll(".book-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const service = this.getAttribute('data-service');
            serviceNameInput.value = service;
            modal.style.display = "block";
        });
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Set minimum date for date picker
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("preferredDate").min = today;
});
</script>

</body>
</html>