<?php
include 'db.php';

// Get room ID
$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;

// Redirect if no room ID
if ($room_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get room details
$sql = "SELECT r.*, h.hotel_name, h.location, h.address, h.hotel_id 
        FROM rooms r 
        JOIN hotels h ON r.hotel_id = h.hotel_id 
        WHERE r.room_id = $room_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$room = $result->fetch_assoc();

// Get room amenities
$sql_amenities = "SELECT a.amenity_name FROM amenities a 
                 JOIN room_amenities ra ON a.amenity_id = ra.amenity_id 
                 WHERE ra.room_id = $room_id";
$result_amenities = $conn->query($sql_amenities);
$room_amenities = [];
if ($result_amenities->num_rows > 0) {
    while($row = $result_amenities->fetch_assoc()) {
        $room_amenities[] = $row["amenity_name"];
    }
}

// Calculate total price if dates are selected
$total_price = 0;
$nights = 0;
if (!empty($check_in) && !empty($check_out)) {
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_out_date->diff($check_in_date)->days;
    $total_price = $room["price_per_night"] * $nights;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room["room_type"]); ?> - <?php echo htmlspecialchars($room["hotel_name"]); ?></title>
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
        
        /* Breadcrumbs */
        .breadcrumbs {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            color: #666;
            font-size: 14px;
        }
        
        .breadcrumbs a {
            color: #00406c;
            text-decoration: none;
        }
        
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        
        .breadcrumbs span {
            margin: 0 10px;
        }
        
        /* Room Details Container */
        .room-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        /* Room Gallery */
        .room-gallery {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        /* Room Info */
        .room-info {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .room-type {
            font-size: 28px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .hotel-link {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .hotel-link:hover {
            text-decoration: underline;
        }
        
        .room-description {
            color: #555;
            margin-bottom: 30px;
        }
        
        .room-features {
            margin-bottom: 30px;
        }
        
        .features-title {
            font-size: 20px;
            color: #00406c;
            margin-bottom: 15px;
        }
        
        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            color: #555;
        }
        
        .feature-item::before {
            content: "✓";
            color: #d4af37;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .room-amenities {
            margin-bottom: 30px;
        }
        
        .amenities-title {
            font-size: 20px;
            color: #00406c;
            margin-bottom: 15px;
        }
        
        .amenities-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            color: #555;
        }
        
        .amenity-item::before {
            content: "✓";
            color: #d4af37;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .cancellation-policy {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .policy-title {
            font-size: 18px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .policy-text {
            color: #555;
            font-size: 14px;
        }
        
        /* Booking Card */
        .booking-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }
        
        .booking-title {
            font-size: 20px;
            color: #00406c;
            margin-bottom: 20px;
        }
        
        .price-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .price-label {
            font-size: 16px;
            color: #333;
        }
        
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #00406c;
        }
        
        .booking-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
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
        
        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .total-price {
            font-size: 24px;
            font-weight: bold;
            color: #d4af37;
        }
        
        .book-now {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .book-now:hover {
            background-color: #b8971f;
        }
        
        /* Similar Rooms */
        .similar-rooms {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }
        
        .similar-title {
            font-size: 28px;
            color: #00406c;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .room-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .room-card:hover {
            transform: translateY(-10px);
        }
        
        .room-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        
        .room-card-info {
            padding: 20px;
        }
        
        .room-card-type {
            font-size: 20px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .room-card-description {
            color: #555;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .room-card-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .room-card-capacity {
            display: flex;
            align-items: center;
            color: #555;
        }
        
        .room-card-capacity::before {
            content: "👤";
            margin-right: 5px;
        }
        
        .room-card-price {
            font-weight: bold;
            color: #00406c;
        }
        
        .view-details {
            background-color: #00406c;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .view-details:hover {
            background-color: #002e4d;
        }
        
        /* Footer */
        footer {
            background-color: #00406c;
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
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
        @media (max-width: 992px) {
            .room-container {
                grid-template-columns: 1fr;
            }
            
            .booking-card {
                position: static;
            }
            
            .rooms-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 20px;
                justify-content: center;
            }
            
            .main-image {
                height: 300px;
            }
            
            .rooms-grid {
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
    
    <div class="breadcrumbs">
        <a href="index.php">Home</a> <span>></span> 
        <a href="hotel.php?id=<?php echo $room["hotel_id"]; ?>"><?php echo htmlspecialchars($room["hotel_name"]); ?></a> <span>></span> 
        <span><?php echo htmlspecialchars($room["room_type"]); ?></span>
    </div>
    
    <div class="room-container">
        <div class="room-details">
            <div class="room-gallery">
                <img src="<?php echo htmlspecialchars($room["image_url"]); ?>" alt="<?php echo htmlspecialchars($room["room_type"]); ?>" class="main-image">
            </div>
            
            <div class="room-info">
                <h1 class="room-type"><?php echo htmlspecialchars($room["room_type"]); ?></h1>
                <a href="hotel.php?id=<?php echo $room["hotel_id"]; ?>" class="hotel-link"><?php echo htmlspecialchars($room["hotel_name"]); ?></a>
                
                <p class="room-description"><?php echo htmlspecialchars($room["description"]); ?></p>
                
                <div class="room-features">
                    <h2 class="features-title">Room Features</h2>
                    <div class="features-list">
                        <div class="feature-item">Up to <?php echo $room["capacity"]; ?> guests</div>
                        <div class="feature-item">King-sized bed</div>
                        <div class="feature-item">40" HDTV</div>
                        <div class="feature-item">Work desk</div>
                        <div class="feature-item">Blackout curtains</div>
                        <div class="feature-item">Climate control</div>
                    </div>
                </div>
                
                <div class="room-amenities">
                    <h2 class="amenities-title">Room Amenities</h2>
                    <div class="amenities-list">
                        <?php foreach($room_amenities as $amenity): ?>
                            <div class="amenity-item"><?php echo htmlspecialchars($amenity); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="cancellation-policy">
                    <h3 class="policy-title">Cancellation Policy</h3>
                    <p class="policy-text">Free cancellation up to 24 hours before check-in. Cancellations made less than 24 hours before check-in are subject to a one-night charge. No-shows will be charged the full amount of the reservation.</p>
                </div>
            </div>
        </div>
        
        <div class="booking-card">
            <h2 class="booking-title">Book This Room</h2>
            
            <div class="price-container">
                <div class="price-label">Price per night</div>
                <div class="price">$<?php echo number_format($room["price_per_night"], 2); ?></div>
            </div>
            
            <form class="booking-form" action="#" method="POST" id="booking-form">
                <div class="form-group">
                    <label for="check-in">Check-in Date</label>
                    <input type="date" id="check-in" name="check_in" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo !empty($check_in) ? htmlspecialchars($check_in) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="check-out">Check-out Date</label>
                    <input type="date" id="check-out" name="check_out" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" value="<?php echo !empty($check_out) ? htmlspecialchars($check_out) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="guests">Guests</label>
                    <select id="guests" name="guests" required>
                        <?php for($i = 1; $i <= $room["capacity"]; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $guests == $i ? 'selected' : ''; ?>><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <?php if($nights > 0): ?>
                <div class="total-container">
                    <div class="total-label"><?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?> total</div>
                    <div class="total-price">$<?php echo number_format($total_price, 2); ?></div>
                </div>
                <?php endif; ?>
                
                <button type="button" class="book-now" onclick="bookRoom()">Book Now</button>
            </form>
        </div>
    </div>
    
    <div class="similar-rooms">
        <h2 class="similar-title">Similar Rooms You May Like</h2>
        <div class="rooms-grid">
            <?php
            // Get similar rooms in the same hotel
            $sql_similar = "SELECT * FROM rooms 
                           WHERE hotel_id = " . $room["hotel_id"] . " 
                           AND room_id != " . $room_id . " 
                           LIMIT 3";
            $result_similar = $conn->query($sql_similar);
            
            if ($result_similar->num_rows > 0) {
                while($similar_room = $result_similar->fetch_assoc()) {
            ?>
            <div class="room-card">
                <img src="<?php echo htmlspecialchars($similar_room["image_url"]); ?>" alt="<?php echo htmlspecialchars($similar_room["room_type"]); ?>" class="room-img">
                <div class="room-card-info">
                    <h3 class="room-card-type"><?php echo htmlspecialchars($similar_room["room_type"]); ?></h3>
                    <p class="room-card-description"><?php echo htmlspecialchars($similar_room["description"]); ?></p>
                    <div class="room-card-details">
                        <div class="room-card-capacity">Up to <?php echo $similar_room["capacity"]; ?> guests</div>
                        <div class="room-card-price">$<?php echo number_format($similar_room["price_per_night"], 2); ?> / night</div>
                    </div>
                    <a href="room.php?id=<?php echo $similar_room["room_id"]; ?><?php echo !empty($check_in) ? '&check_in=' . urlencode($check_in) : ''; ?><?php echo !empty($check_out) ? '&check_out=' . urlencode($check_out) : ''; ?><?php echo isset($guests) ? '&guests=' . urlencode($guests) : ''; ?>" class="view-details">View Details</a>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
    
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
        // JavaScript for date validation and booking
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
                
                // Refresh page with new dates
                updateDates();
            });
            
            checkOutInput.addEventListener('change', function() {
                updateDates();
            });
            
            document.getElementById('guests').addEventListener('change', function() {
                updateDates();
            });
            
            function updateDates() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const guests = document.getElementById('guests').value;
                
                if (checkIn && checkOut) {
                    window.location.href = 'room.php?id=<?php echo $room_id; ?>&check_in=' + checkIn + '&check_out=' + checkOut + '&guests=' + guests;
                }
            }
            
            // Book room function
            window.bookRoom = function() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                
                if (!checkIn || !checkOut) {
                    alert('Please select check-in and check-out dates');
                    return;
                }
                
                // In a real application, this would redirect to a booking page
                // For this demo, we'll just show an alert
                alert('Thank you for booking with Hilton Hotels! Your reservation has been confirmed.');
            };
        });
    </script>
</body>
</html>
