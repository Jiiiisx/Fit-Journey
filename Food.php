<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    error_log("Food.php: user_id not set in session");
    header('Location: login.php');
    exit;
} else {
    error_log("Food.php: user_id in session: " . $_SESSION['user_id']);
}

try {
    $stmt = $pdo->query("SELECT id, nama_makanan, kalori, porsi, kategori, foto_makanan FROM makanan");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $foods = [];
    error_log("Error fetching food data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fitney Calorie Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/Food.css" />
    <link rel="stylesheet" href="css/Food-enhanced.css" />
    <link rel="stylesheet" href="css/add-activity-modal.css" />
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-dumbbell"></i>
            <span>Fitney</span>
        </div>
        <nav class="nav-menu">
            <a href="dashboard-pelanggan.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="Food.php" class="nav-item active">
                <i class="fas fa-utensils"></i>
                <span>Calorie Tracker</span>
            </a>
            <a href="schedule.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule</span>
            </a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log out</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <header class="top-header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search..." />
            </div>
            <div class="user-profile">
                <div class="notification">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <img
                    src="https://randomuser.me/api/portraits/men/32.jpg"
                    alt="Profile"
                    class="profile-img"
                />
                <div class="user-info">
                    <span class="username"><?php echo $_SESSION['username']; ?></span>
                    <span class="role">Pelanggan</span>
                </div>
            </div>
        </header>

        <div class="dashboard-grid">
            <section class="calorie-summary card">
                <h2>Calorie Summary</h2>
                <div class="summary-details">
                    <p>
                        Total Consumed:
                        <span id="totalConsumedCalories">0.00</span> kcal
                    </p>
                    <p>
                        Total Burned:
                        <span id="totalBurnedCalories">0.00</span> kcal
                    </p>
                    <p>
                        Net Calories:
                        <span id="netCalories">0.00</span> kcal
                    </p>
                    <div class="progress-bar">
                        <div
                            class="progress-fill"
                            id="progressFill"
                            style="width: 0%"
                        ></div>
                    </div>
                </div>
                <button id="saveCaloriesBtn" class="btn-save-calories">
                    Save Daily Calories
                </button>
            </section>

            <section class="consumed-foods card">
                <div class="Title">
                    <h2>Consumed Foods</h2>
                    <button id="addFoodBtn" class="btn-option">Add Food</button>
                </div>

                <!-- Modal for adding new food -->
                <div id="addFoodModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
                    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 450px; border-radius: 10px; position: relative;">
                        <span id="closeFoodModalBtn" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                        <h3>Add Food</h3>
                        <form id="addFoodForm">
                            <label for="foodSelectionType">Choose Food Input Type:</label><br />
                            <select id="foodSelectionType" name="foodSelectionType" style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                <option value="existing" selected>Choose from Existing Foods</option>
                                <option value="custom">Add Custom Food</option>
                            </select>

                            <div id="existingFoodSection">
                                <label for="foodSearch">Search Food:</label><br />
                                <input type="text" id="foodSearch" placeholder="Search foods..." style="width: 100%; padding: 8px; margin-bottom: 10px;" />
                                <label for="existingFoodSelect">Select Food:</label><br />
                                <select id="existingFoodSelect" name="existingFoodSelect" style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                    <option value="">Loading foods...</option>
                                </select>
                            </div>

                            <div id="customFoodSection" style="display:none;">
                                <label for="customFoodName">Food Name:</label><br />
                                <input type="text" id="customFoodName" name="customFoodName" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                                <label for="customCalories">Calories (kcal):</label><br />
                                <input type="number" id="customCalories" name="customCalories" min="0" step="0.01" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                                <label for="customPortion">Portion:</label><br />
                                <input type="text" id="customPortion" name="customPortion" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                                <label for="customCategory">Category:</label><br />
                                <input type="text" id="customCategory" name="customCategory" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            </div>

                            <label for="foodQuantity">Quantity:</label><br />
                            <input type="number" id="foodQuantity" name="foodQuantity" min="1" value="1" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <label for="foodDate">Consumption Date:</label><br />
                            <input type="date" id="foodDate" name="foodDate" style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <button type="submit" class="btn-option" style="width: 100%;">Add Food</button>
                        </form>
                        <div id="foodFormMessage" style="margin-top: 10px; color: red;"></div>
                    </div>
                </div>

                <div
                    id="consumedFoodItemsContainer"
                    class="consumed-food-grid"
                >
                    <div
                        id="emptyFoodState"
                        style="text-align: center; padding: 20px; display: none"
                    >
                        No food consumed yet.
                    </div>
                </div>

                <!-- New table for consumed food details -->
                <div class="consumed-food-details-container">
                    <h3>Consumed Food Details</h3>
                    <table id="consumedFoodDetailsTable" class="enhanced-table">
                        <thead>
                            <tr>
                                <th>Nama Makanan</th>
                                <th>Quantity</th>
                                <th>Consumption Date</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="consumedFoodDetailsTableBody">
                            <tr>
                                <td colspan="4" style="text-align: center;">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="daily-activities card">
                <div class="Title">
                    <h2>Daily Activities</h2>
                    <button id="addActivityBtn" class="btn-option">
                        Add Activity
                    </button>
                </div>

                <!-- Modal for adding new activity -->
                <div id="addActivityModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
                    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 10px; position: relative;">
                        <span id="closeModalBtn" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                        <h3>Add New Activity</h3>
                        <form id="addActivityForm">
                            <label for="activityName">Activity Name:</label><br />
                            <input type="text" id="activityName" name="activityName" required style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <label for="durationMinutes">Duration (minutes):</label><br />
                            <input type="number" id="durationMinutes" name="durationMinutes" min="1" required style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <label for="caloriesBurned">Calories Burned:</label><br />
                            <input type="number" id="caloriesBurned" name="caloriesBurned" min="0" step="0.01" required style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <label for="activityDate">Date:</label><br />
                            <input type="date" id="activityDate" name="activityDate" required style="width: 100%; padding: 8px; margin-bottom: 10px;" /><br />
                            <button type="submit" class="btn-option" style="width: 100%;">Add Activity</button>
                        </form>
                        <div id="formMessage" style="margin-top: 10px; color: red;"></div>
                    </div>
                </div>
                <p>
                    Total Burned Calories:
                    <span id="currentBurnedCalories">0.00</span> kcal
                </p>
                <div class="burned-calories-progress-bar" style="width: 100%; background: #eee; border-radius: 10px; height: 20px; margin-top: 5px;">
                    <div id="burnedCaloriesProgressFill" style="height: 100%; width: 0%; background: #f9844a; border-radius: 10px; transition: width 0.5s ease;"></div>
                </div>

                <!-- New table for daily activities details -->
                <div class="consumed-food-details-container">
                    <h3>Daily Activities Details</h3>
                    <table id="dailyActivitiesDetailsTable" class="enhanced-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Activity Name</th>
                                <th>Duration Minutes</th>
                                <th>Calories Burned</th>
                                <th>Tanggal</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="dailyActivitiesDetailsTableBody">
                            <tr>
                                <td colspan="6" style="text-align: center;">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    
    <script>
        // Calorie Summary Update Function
        function updateCalorieSummary() {
            const today = new Date().toISOString().split('T')[0];
            
            // Fetch consumed foods
            fetch('get_consumed_foods.php')
                .then(response => response.json())
                .then(foodData => {
                    const totalConsumed = foodData.success ? foodData.total_consumed_calories : 0;
                    
                    // Fetch daily activities
                    fetch('get_daily_activities_updated.php')
                        .then(response => response.json())
                        .then(activityData => {
                            const totalBurned = activityData.success ? activityData.total_burned_calories : 0;
                            
                            // Calculate net calories
                            const netCalories = totalConsumed - totalBurned;
                            
                            // Update UI
                            document.getElementById('totalConsumedCalories').innerText = totalConsumed.toFixed(2);
                            document.getElementById('totalBurnedCalories').innerText = totalBurned.toFixed(2);
                            document.getElementById('netCalories').innerText = netCalories.toFixed(2);
                            
                            // Update progress bar
                            const targetCalories = 2000;
                            const progressPercent = Math.min((Math.abs(netCalories) / targetCalories) * 100, 100);
                            document.getElementById('progressFill').style.width = progressPercent + '%';
                            
                            // Update consumed foods table
                            const foodTableBody = document.getElementById('consumedFoodDetailsTableBody');
                            if (foodData.success && foodData.consumed_foods.length > 0) {
                                foodTableBody.innerHTML = '';
                                foodData.consumed_foods.forEach(food => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${food.nama_makanan}</td>
                                        <td>${food.quantity}</td>
                                        <td>${food.consumption_date}</td>
                                        <td>${food.created_at}</td>
                                    `;
                                    foodTableBody.appendChild(row);
                                });
                            } else {
                                foodTableBody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No consumed food data available.</td></tr>';
                            }
                            
                            // Update daily activities table
                            const dailyTableBody = document.getElementById('dailyActivitiesDetailsTableBody');
                            if (activityData.success && activityData.daily_activities.length > 0) {
                                dailyTableBody.innerHTML = '';
                                activityData.daily_activities.forEach(activity => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${activity.id}</td>
                                        <td>${activity.activity_name}</td>
                                        <td>${activity.duration_minutes}</td>
                                        <td>${activity.calories_burned}</td>
                                        <td>${activity.tanggal}</td>
                                        <td>${activity.created_at}</td>
                                    `;
                                    dailyTableBody.appendChild(row);
                                });
                                document.getElementById('currentBurnedCalories').innerText = totalBurned.toFixed(2);
                            } else {
                                dailyTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No daily activities data available.</td></tr>';
                                document.getElementById('currentBurnedCalories').innerText = '0.00';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching daily activities data:', error);
                        });
                })
                .catch(error => {
                    console.error('Error fetching consumed foods data:', error);
                });
        }
        
        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            updateCalorieSummary();
            
            // Update when new food or activity is added
            document.addEventListener('foodAdded', updateCalorieSummary);
            document.addEventListener('activityAdded', updateCalorieSummary);
        });
    </script>
    
    <script src="js/add-activity-handler.js"></script>
    <script src="js/add-food-handler.js"></script>
</body>
</html>
