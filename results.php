<?php
include 'db.php';

// Get search parameters
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;
$amenities = isset($_GET['amenities']) ? $_GET['amenities'] : [];

// Get all amenities for filter
$sql_amenities = "SELECT * FROM amenities ORDER BY amenity_name";
$result_amenities = $conn->query($sql_amenities);
$all_amenities = [];
if ($result_amenities->num_rows > 0) {
    while($row = $result_amenities->fetch_assoc()) {
        $all_amenities[] = $row;
    }
}

// Build query for hotels
$sql = "SELECT DISTINCT h.* FROM hotels h";

// Add amenities filter if selected
if (!empty($amenities)) {
    $sql .= " JOIN hotel_amenities ha ON h.hotel_id = ha.hotel_id 
              WHERE ha.amenity_id IN (" . implode(',', array_map('intval', $amenities)) . ")";
    
    if (!empty($destination)) {
        $sql .= " AND h.location = '" . $conn->real_escape_string($destination) . "'";
    }
} else if (!empty($destination)) {
    $sql .= " WHERE h.location = '" . $conn->real_escape_string($destination) . "'";
}

$sql .= " ORDER BY h.rating DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Hilton Hotels</title>
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
        
        /* Search Results Container */
        .results-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        
        /* Filter Sidebar */
        .filters {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            align-self: start;
            position: sticky;
            top: 20px;
        }
        
        .filter-title {
            font-size: 20px;
            margin-bottom: 20px;
            color: #00406c;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
        }
        
        .filter-option input {
            margin-right: 10px;
        }
        
        .filter-option label {
            font-size: 14px;
            color: #555;
        }
        
        .apply-filters {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        .apply-filters:hover {
            background-color: #b8971f;
        }
        
        /* Results List */
        .results-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .search-summary {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .search-summary h2 {
            font-size: 24px;
            color: #00406c;
            margin-bottom: 10px;
        }
        
        .search-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .search-detail {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #555;
        }
        
        .search-detail strong {
            margin-right: 5px;
            color: #333;
        }
        
        .hotel-result {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 300px 1fr;
        }
        
        .hotel-img-container {
            height: 100%;
        }
        
        .hotel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .hotel-details {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .hotel-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .hotel-name {
            font-size: 22px;
            color: #00406c;
        }
        
        .hotel-rating {
            display: flex;
            align-items: center;
        }
        
        .stars {
            color: #d4af37;
            margin-right: 5px;
        }
        
        .hotel-location {
            color: #666;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .hotel-location::before {
            content: "📍";
            margin-right: 5px;
        }
        
        .hotel-description {
            margin-bottom: 20px;
            color: #555;
        }
        
        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .amenity {
            background-color: #f5f5f5;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: #555;
        }
        
        .hotel-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .price-container {
            text-align: right;
        }
        
        .price-label {
            font-size: 12px;
            color: #666;
        }
        
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #00406c;
        }
        
        .view-rooms {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .view-rooms:hover {
            background-color: #b8971f;
        }
        
        .no-results {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .no-results h2 {
            font-size: 24px;
            color: #00406c;
            margin-bottom: 15px;
        }
        
        .no-results p {
            color: #555;
            margin-bottom: 20px;
        }
        
        .back-btn {
            background-color: #00406c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-btn:hover {
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
            .results-container {
                grid-template-columns: 1fr;
            }
            
            .filters {
                position: static;
                margin-bottom: 30px;
            }
            
            .hotel-result {
                grid-template-columns: 1fr;
            }
            
            .hotel-img-container {
                height: 200px;
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
            
            .hotel-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .hotel-footer {
                flex-direction: column;
                gap: 15px;
            }
            
            .price-container {
                text-align: left;
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
    
    <div class="results-container">
        <div class="filters">
            <h2 class="filter-title">Filter Results</h2>
            <form action="results.php" method="GET">
                <?php if (!empty($destination)): ?>
                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                <?php endif; ?>
                <?php if (!empty($check_in)): ?>
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                <?php endif; ?>
                <?php if (!empty($check_out)): ?>
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                <?php endif; ?>
                <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                
                <div class="filter-group">
                    <h3>Amenities</h3>
                    <div class="filter-options">
                        <?php foreach($all_amenities as $amenity): ?>
                            <div class="filter-option">
                                <input type="checkbox" id="amenity-<?php echo $amenity['amenity_id']; ?>" name="amenities[]" value="<?php echo $amenity['amenity_id']; ?>" <?php echo in_array($amenity['amenity_id'], $amenities) ? 'checked' : ''; ?>>
                                <label for="amenity-<?php echo $amenity['amenity_id']; ?>"><?php echo htmlspecialchars($amenity['amenity_name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="apply-filters">Apply Filters</button>
            </form>
        </div>
        
        <div class="results-list">
            <div class="search-summary">
                <h2>Search Results</h2>
                <div class="search-details">
                    <?php if (!empty($destination)): ?>
                        <div class="search-detail">
                            <strong>Destination:</strong> <?php echo htmlspecialchars($destination); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($check_in) && !empty($check_out)): ?>
                        <div class="search-detail">
                            <strong>Dates:</strong> <?php echo date('M d, Y', strtotime($check_in)); ?> - <?php echo date('M d, Y', strtotime($check_out)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="search-detail">
                        <strong>Guests:</strong> <?php echo $guests; ?>
                    </div>
                </div>
            </div>
            
            <?php
            if ($result->num_rows > 0) {
                while($hotel = $result->fetch_assoc()) {
                    // Get minimum room price for this hotel
                    $sql_price = "SELECT MIN(price_per_night) as min_price FROM rooms WHERE hotel_id = " . $hotel["hotel_id"];
                    $result_price = $conn->query($sql_price);
                    $price_row = $result_price->fetch_assoc();
                    $min_price = $price_row["min_price"];
                    
                    // Get hotel amenities
                    $sql_hotel_amenities = "SELECT a.amenity_name FROM amenities a 
                                           JOIN hotel_amenities ha ON a.amenity_id = ha.amenity_id 
                                           WHERE ha.hotel_id = " . $hotel["hotel_id"] . " 
                                           LIMIT 5";
                    $result_hotel_amenities = $conn->query($sql_hotel_amenities);
                    $hotel_amenities = [];
                    if ($result_hotel_amenities->num_rows > 0) {
                        while($amenity_row = $result_hotel_amenities->fetch_assoc()) {
                            $hotel_amenities[] = $amenity_row["amenity_name"];
                        }
                    }
                    
                    // Calculate stars display
                    $stars = '';
                    $rating = floor($hotel["rating"]);
                    for ($i = 0; $i < $rating; $i++) {
                        $stars .= '★';
                    }
                    if ($hotel["rating"] - $rating > 0) {
                        $stars .= '☆';
                    }
            ?>
            <div class="hotel-result">
                <div class="hotel-img-container">
                    <img src="<?php echo htmlspecialchars($hotel["image_url"]); ?>" alt="<?php echo htmlspecialchars($hotel["hotel_name"]); ?>" class="hotel-img">
                </div>
                <div class="hotel-details">
                    <div class="hotel-header">
                        <h3 class="hotel-name"><?php echo htmlspecialchars($hotel["hotel_name"]); ?></h3>
                        <div class="hotel-rating">
                            <span class="stars"><?php echo $stars; ?></span>
                            <span><?php echo $hotel["rating"]; ?>/5</span>
                        </div>
                    </div>
                    <p class="hotel-location"><?php echo htmlspecialchars($hotel["address"]); ?></p>
                    <p class="hotel-description"><?php echo htmlspecialchars($hotel["description"]); ?></p>
                    <div class="hotel-amenities">
                        <?php foreach($hotel_amenities as $amenity): ?>
                            <span class="amenity"><?php echo htmlspecialchars($amenity); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="hotel-footer">
                        <a href="hotel.php?id=<?php echo $hotel["hotel_id"]; ?><?php echo !empty($check_in) ? '&check_in=' . urlencode($check_in) : ''; ?><?php echo !empty($check_out) ? '&check_out=' . urlencode($check_out) : ''; ?><?php echo isset($guests) ? '&guests=' . urlencode($guests) : ''; ?>" class="view-rooms">View Rooms</a>
                        <div class="price-container">
                            <div class="price-label">Starting from</div>
                            <div class="price">$<?php echo number_format($min_price, 2); ?></div>
                            <div class="price-label">per night</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
            ?>
            <div class="no-results">
                <h2>No Hotels Found</h2>
                <p>We couldn't find any hotels matching your search criteria. Please try different filters or search for another destination.</p>
                <a href="index.php" class="back-btn">Back to Search</a>
            </div>
            <?php
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
        // JavaScript for filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const applyButton = document.querySelector('.apply-filters');
            
            // Enable apply button when a filter changes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    applyButton.textContent = 'Apply Filters';
                    applyButton.style.backgroundColor = '#d4af37';
                });
            });
        });
    </script>
</body>
</html>
