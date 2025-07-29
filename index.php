<?php
include 'db.php';

// Get all locations for dropdown
$sql = "SELECT DISTINCT location FROM hotels ORDER BY location";
$result = $conn->query($sql);
$locations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $locations[] = $row["location"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Find Your Perfect Stay</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header Styles */
        header {
            background-color: #00406c;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
        }
        
        .logo span {
            color: #d4af37;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: #d4af37;
        }
        
        /* Hero Section */
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://www.hilton.com/im/en/NYCMHHH/3254503/nycmhhh-exterior-16.jpg');
            background-size: cover;
            background-position: center;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        /* Search Form */
        .search-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 1000px;
            margin: -100px auto 50px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group select,
        .form-group input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .search-btn {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            grid-column: 1 / -1;
        }
        
        .search-btn:hover {
            background-color: #b8971f;
        }
        
        /* Featured Hotels Section */
        .featured-hotels {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            color: #00406c;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .hotel-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .hotel-card:hover {
            transform: translateY(-10px);
        }
        
        .hotel-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        
        .hotel-info {
            padding: 20px;
        }
        
        .hotel-name {
            font-size: 20px;
            margin-bottom: 10px;
            color: #00406c;
        }
        
        .hotel-location {
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .hotel-location::before {
            content: "📍";
            margin-right: 5px;
        }
        
        .hotel-rating {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .stars {
            color: #d4af37;
            margin-right: 5px;
        }
        
        .hotel-description {
            margin-bottom: 20px;
            color: #555;
        }
        
        .view-btn {
            display: inline-block;
            background-color: #00406c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .view-btn:hover {
            background-color: #002e4d;
        }
        
        /* Why Choose Us Section */
        .why-choose-us {
            background-color: #f9f9f9;
            padding: 80px 0;
        }
        
        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .feature {
            text-align: center;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .feature-icon {
            font-size: 40px;
            margin-bottom: 20px;
            color: #d4af37;
        }
        
        .feature-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: #00406c;
        }
        
        .feature-description {
            color: #666;
        }
        
        /* Footer */
        footer {
            background-color: #00406c;
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .footer-logo span {
            color: #d4af37;
        }
        
        .footer-links h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #d4af37;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links ul li {
            margin-bottom: 10px;
        }
        
        .footer-links ul li a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links ul li a:hover {
            color: #d4af37;
        }
        
        .footer-bottom {
            max-width: 1200px;
            margin: 40px auto 0;
            padding: 20px 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 14px;
            color: #ddd;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 20px;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .hero p {
                font-size: 18px;
            }
            
            .search-container {
                margin-top: -50px;
                padding: 20px;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">Hilton <span>Hotels</span></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Offers</a></li>
                    <li><a href="#">Hilton Honors</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Experience Luxury & Comfort</h1>
            <p>Find your perfect Hilton stay with exclusive rates and offers worldwide.</p>
        </div>
    </section>
    
    <div class="search-container">
        <form class="search-form" action="results.php" method="GET">
            <div class="form-group">
                <label for="destination">Destination</label>
                <select id="destination" name="destination" required>
                    <option value="">Select Destination</option>
                    <?php foreach($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="check-in">Check-in Date</label>
                <input type="date" id="check-in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="check-out">Check-out Date</label>
                <input type="date" id="check-out" name="check_out" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            <div class="form-group">
                <label for="guests">Guests</label>
                <select id="guests" name="guests" required>
                    <option value="1">1 Guest</option>
                    <option value="2" selected>2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4 Guests</option>
                    <option value="5">5+ Guests</option>
                </select>
            </div>
            <button type="submit" class="search-btn">Search Hotels</button>
        </form>
    </div>
    
    <section class="featured-hotels">
        <h2 class="section-title">Featured Hilton Hotels</h2>
        <div class="hotels-grid">
            <?php
            // Get featured hotels
            $sql = "SELECT * FROM hotels LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $stars = '';
                    $rating = floor($row["rating"]);
                    for ($i = 0; $i < $rating; $i++) {
                        $stars .= '★';
                    }
                    if ($row["rating"] - $rating > 0) {
                        $stars .= '☆';
                    }
            ?>
            <div class="hotel-card">
                <img src="<?php echo htmlspecialchars($row["image_url"]); ?>" alt="<?php echo htmlspecialchars($row["hotel_name"]); ?>" class="hotel-img">
                <div class="hotel-info">
                    <h3 class="hotel-name"><?php echo htmlspecialchars($row["hotel_name"]); ?></h3>
                    <p class="hotel-location"><?php echo htmlspecialchars($row["location"]); ?></p>
                    <div class="hotel-rating">
                        <span class="stars"><?php echo $stars; ?></span>
                        <span><?php echo $row["rating"]; ?>/5</span>
                    </div>
                    <p class="hotel-description"><?php echo htmlspecialchars(substr($row["description"], 0, 100)) . '...'; ?></p>
                    <a href="hotel.php?id=<?php echo $row["hotel_id"]; ?>" class="view-btn">View Hotel</a>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>
    </section>
    
    <section class="why-choose-us">
        <div class="features-container">
            <h2 class="section-title">Why Choose Hilton Hotels</h2>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">🌟</div>
                    <h3 class="feature-title">Best Rate Guarantee</h3>
                    <p class="feature-description">Find a lower rate? We'll match it and give you an additional 25% discount.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <h3 class="feature-title">Hilton Honors Rewards</h3>
                    <p class="feature-description">Earn points for free nights, experiences, and more with every stay.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Digital Key</h3>
                    <p class="feature-description">Skip the front desk and unlock your room with your smartphone.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🛏️</div>
                    <h3 class="feature-title">Comfortable Stays</h3>
                    <p class="feature-description">Enjoy premium bedding, amenities, and services for a perfect stay.</p>
                </div>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="footer-container">
            <div class="footer-about">
                <div class="footer-logo">Hilton <span>Hotels</span></div>
                <p>Experience the best in hospitality with Hilton Hotels worldwide. From luxury accommodations to budget-friendly options, we have something for everyone.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Special Offers</a></li>
                    <li><a href="#">Hilton Honors</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3>Contact</h3>
                <ul>
                    <li>Email: info@hilton.com</li>
                    <li>Phone: +1-800-HILTONS</li>
                    <li>Address: 7930 Jones Branch Drive, McLean, Virginia, USA</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Hilton Hotels & Resorts. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // JavaScript for date validation
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check-in');
            const checkOutInput = document.getElementById('check-out');
            
            // Set minimum dates
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Update check-out min date when check-in changes
            checkInInput.addEventListener('change', function() {
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                
                const nextDayFormatted = nextDay.toISOString().split('T')[0];
                checkOutInput.min = nextDayFormatted;
                
                // If current check-out date is before new min date, update it
                if (new Date(checkOutInput.value) <= checkInDate) {
                    checkOutInput.value = nextDayFormatted;
                }
            });
        });
    </script>
</body>
</html>
