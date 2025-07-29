<?php
include 'db.php';

// Get hotel ID
$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;

// Redirect if no hotel ID
if ($hotel_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get hotel details
$sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$hotel = $result->fetch_assoc();

// Get hotel amenities
$sql_amenities = "SELECT a.amenity_name FROM amenities a 
                 JOIN hotel_amenities ha ON a.amenity_id = ha.amenity_id 
                 WHERE ha.hotel_id = $hotel_id";
$result_amenities = $conn->query($sql_amenities);
$hotel_amenities = [];
if ($result_amenities->num_rows > 0) {
    while($row = $result_amenities->fetch_assoc()) {
        $hotel_amenities[] = $row["amenity_name"];
    }
}

// Get rooms
$sql_rooms = "SELECT * FROM rooms WHERE hotel_id = $hotel_id ORDER BY price_per_night";
$result_rooms = $conn->query($sql_rooms);
$rooms = [];
if ($result_rooms->num_rows > 0) {
    while($row = $result_rooms->fetch_assoc()) {
        // Get room amenities
        $sql_room_amenities = "SELECT a.amenity_name FROM amenities a 
                              JOIN room_amenities ra ON a.amenity_id = ra.amenity_id 
                              WHERE ra.room_id = " . $row["room_id"];
        $result_room_amenities = $conn->query($sql_room_amenities);
        $room_amenities = [];
        if ($result_room_amenities->num_rows > 0) {
            while($amenity_row = $result_room_amenities->fetch_assoc()) {
                $room_amenities[] = $amenity_row["amenity_name"];
            }
        }
        $row["amenities"] = $room_amenities;
        $rooms[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel["hotel_name"]); ?> - Hilton Hotels</title>
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
        
        /* Hotel Hero Section */
        .hotel-hero {
            position: relative;
            height: 500px;
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            align-items: flex-end;
        }
        
        .hotel-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
        }
        
        .hotel-info {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .hotel-name {
            font-size: 36px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hotel-location {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .hotel-location::before {
            content: "📍";
            margin-right: 5px;
        }
        
        .hotel-rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .stars {
            color: #d4af37;
            margin-right: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* Hotel Details */
        .hotel-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .hotel-description {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .description-title {
            font-size: 24px;
            color: #00406c;
            margin-bottom: 20px;
        }
        
        .description-text {
            color: #555;
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
        
        .check-availability {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .check-availability:hover {
            background-color: #b8971f;
        }
        
        /* Rooms Section */
        .rooms-section {
            margin-top: 40px;
        }
        
        .rooms-title {
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
        
        .room-info {
            padding: 20px;
        }
        
        .room-type {
            font-size: 20px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .room-description {
            color: #555;
            margin-bottom: 15px;
        }
        
        .room-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .room-capacity {
            display: flex;
            align-items: center;
            color: #555;
        }
        
        .room-capacity::before {
            content: "👤";
            margin-right: 5px;
        }
        
        .room-price {
            font-weight: bold;
            color: #00406c;
        }
        
        .room-amenities {
            margin-bottom: 20px;
        }
        
        .room-amenities-title {
            font-size: 16px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .room-amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .room-amenity {
            background-color: #f5f5f5;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: #555;
        }
        
        .book-now {
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
        }
        
        .book-now:hover {
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
            .hotel-details {
                grid-template-columns: 1fr;
            }
            
            .booking-card {
                position: static;
                margin-bottom: 30px;
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
            
            .hotel-hero {
                height: 400px;
            }
            
            .hotel-name {
                font-size: 28px;
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
    
    <div class="hotel-hero" style="background-image: url('<?php echo htmlspecialchars($hotel["image_url"]); ?>');">
        <div class="hotel-info">
            <h1 class="hotel-name"><?php echo htmlspecialchars($hotel["hotel_name"]); ?></h1>
            <p class="hotel-location"><?php echo htmlspecialchars($hotel["address"]); ?></p>
            <div class="hotel-rating">
                <span class="stars">
                    <?php
                    $rating = floor($hotel["rating"]);
                    for ($i = 0; $i < $rating; $i++) {
                        echo '★';
                    }
                    if ($hotel["rating"] - $rating > 0) {
                        echo '☆';
                    }
                    ?>
                </span>
                <span><?php echo $hotel["rating"]; ?>/5</span>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="hotel-details">
            <div class="hotel-description">
                <h2 class="description-title">About <?php echo htmlspecialchars($hotel["hotel_name"]); ?></h2>
                <p class="description-text"><?php echo htmlspecialchars($hotel["description"]); ?></p>
                
                <h3 class="amenities-title">Hotel Amenities</h3>
                <div class="amenities-list">
                    <?php foreach($hotel_amenities as $amenity): ?>
                        <div class="amenity-item"><?php echo htmlspecialchars($amenity); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="booking-card">
                <h3 class="booking-title">Check Availability</h3>
                <form class="booking-form" action="results.php" method="GET">
                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($hotel["location"]); ?>">
                    
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
                            <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Guest</option>
                            <option value="2" <?php echo $guests == 2 ? 'selected' : ''; ?>>2 Guests</option>
                            <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Guests</option>
                            <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Guests</option>
                            <option value="5" <?php echo $guests == 5 ? 'selected' : ''; ?>>5+ Guests</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="check-availability">Check Availability</button>
                </form>
            </div>
        </div>
        
        <div class="rooms-section">
            <h2 class="rooms-title">Available Rooms</h2>
            <div class="rooms-grid">
                <?php foreach($rooms as $room): ?>
                <div class="room-card">
                    <img src="<?php echo htmlspecialchars($room["image_url"]); ?>" alt="<?php echo htmlspecialchars($room["room_type"]); ?>" class="room-img">
                    <div class="room-info">
                        <h3 class="room-type"><?php echo htmlspecialchars($room["room_type"]); ?></h3>
                        <p class="room-description"><?php echo htmlspecialchars($room["description"]); ?></p>
                        <div class="room-details">
                            <div class="room-capacity">Up to <?php echo $room["capacity"]; ?> guests</div>
                            <div class="room-price">$<?php echo number_format($room["price_per_night"], 2); ?> / night</div>
                        </div>
                        
                        <div class="room-amenities">
                            <h4 class="room-amenities-title">Room Amenities</h4>
                            <div class="room-amenities-list">
                                <?php foreach($room["amenities"] as $amenity): ?>
                                    <span class="room-amenity"><?php echo htmlspecialchars($amenity); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button class="book-now" onclick="bookRoom(<?php echo $room['room_id']; ?>)">Book Now</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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
            
            // Book room function
            window.bookRoom = function(roomId) {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const guests = document.getElementById('guests').value;
                
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
