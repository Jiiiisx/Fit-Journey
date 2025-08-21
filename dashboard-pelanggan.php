<?php
session_start();
require_once 'config.php';

// Initialize variables
$total_calories = 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Fetch total calories for the logged-in user
error_log("dashboard-pelanggan.php - user_id from session: " . $user_id);
if ($user_id > 0) {
    $date = date('Y-m-d');
$sql = "SELECT calorie FROM daily_calories WHERE user_id = ? AND tanggal = ?";
        $stmt = $pdo->prepare($sql);
    if ($stmt) {
        $stmt->execute([$user_id, $date]);
        $total_calories = $stmt->fetchColumn();
    }
}

// Calculate Day Streak
$day_streak = 0;
if ($user_id > 0) {
    $sql_streak = "SELECT tanggal FROM daily_calories WHERE user_id = ? ORDER BY tanggal DESC";
    $stmt_streak = $pdo->prepare($sql_streak);
    if ($stmt_streak) {
        $stmt_streak->execute([$user_id]);
        $dates = $stmt_streak->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($dates)) {
            $current_date = new DateTime();
            $streak_count = 0;
            
            // Check if today's data exists
            $today_has_data = false;
            foreach ($dates as $d) {
                if ($d == $current_date->format('Y-m-d')) {
                    $today_has_data = true;
                    break;
                }
            }

            if ($today_has_data) {
                $streak_count = 1;
                $previous_date = clone $current_date;
                $previous_date->modify('-1 day');

                foreach ($dates as $d) {
                    if ($d == $previous_date->format('Y-m-d')) {
                        $streak_count++;
                        $previous_date->modify('-1 day');
                    } else if ($d < $previous_date->format('Y-m-d')) {
                        // Date is too old, break the loop
                        break;
                    }
                }
            }
            $day_streak = $streak_count;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitney Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/Dashboard-pelanggan.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="weekly_chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-dumbbell"></i>
            <span>Fitney</span>
        </div>
        <nav class="nav-menu">
            <a href="dashboard-pelanggan.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="Food.php" class="nav-item">
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
                <input type="text" placeholder="Search...">
            </div>
            <div class="user-profile">
                <div class="notification">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="profile-img">
                <div class="user-info">
                    <span class="username"><?php echo $_SESSION['username']; ?></span>
                    <span class="role"><?php echo ucfirst($_SESSION['role']); ?></span>
                </div>
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_calories); ?></h3>
                        <p>Calories Today</p>
                        <span class="trend <?php echo $total_calories > 0 ? 'up' : 'neutral'; ?>">
                            <?php echo $total_calories > 0 ? '+12% <i class="fas fa-arrow-up"></i>' : '<i class="fas fa-minus"></i>'; ?>
                        </span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $day_streak; ?></h3>
                        <p>Day Streak</p>
                        <span class="trend up">+1 <i class="fas fa-arrow-up"></i></span>
                    </div>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-container">
                    <h3>Weekly Calorie</h3>
                    <canvas id="weeklyCalorieChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Calorie Intake vs Burn</h3>
                    <p>Intake: <span id="calorieIntake">0</span> cal | Burn: <span id="calorieBurn">0</span> cal</p>
                    <canvas id="calorieChart"></canvas>
                </div>
            </div>

            <div class="recent-activities">
                <h3>Recent Activities</h3>
                <div class="activity-list" id="recentActivitiesList">
                    <!-- Activities will be loaded here dynamically -->
                </div>
                <div class="empty-state" id="emptyActivitiesState" style="display: none;">
                    <i class="fas fa-running"></i>
                    <p>No activities recorded yet.</p>
                    <small>Start tracking your activities in the Calorie Tracker page!</small>
                </div>
            </div>

            <div class="upcoming-schedule">
                <h3>Today's Schedule</h3>
                <div class="schedule-cards" id="dailyScheduleListDashboard">
                    <!-- Schedules will be loaded here dynamically -->
                </div>
                <div class="empty-state" id="emptyScheduleStateDashboard" style="display: none;">
                    <i class="fas fa-calendar-check"></i>
                    <p>No schedule entries for today.</p>
                    <small>Add your daily plans in the Schedule page!</small>
                </div>
            </div>
</div>
    </div>

    <div style="position: fixed; bottom: 10px; right: 10px; background: #eee; padding: 10px; border: 1px solid #ccc; font-size: 14px;">
        Session User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set'; ?>
    </div>

    <script src="js/weekly_chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calorie Chart
            const calorieCtx = document.getElementById('calorieChart').getContext('2d');
            let calorieChart = new Chart(calorieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Consumed', 'Burned'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#ff6b6b', '#4ecdc4'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Fetch calorie data
            fetch('get_calorie_intake_vs_burn.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calorieChart.data.datasets[0].data = [data.consumed, data.burned];
                        calorieChart.update();
                        document.getElementById('calorieIntake').textContent = data.consumed.toFixed(0);
                        document.getElementById('calorieBurn').textContent = data.burned.toFixed(0);
                    }
                });

            // Fetch and display recent activities
            function fetchRecentActivities() {
                fetch('get_daily_activities.php')
                    .then(response => response.json())
                    .then(data => {
                        const activityList = document.getElementById('recentActivitiesList');
                        const emptyState = document.getElementById('emptyActivitiesState');
                        activityList.innerHTML = ''; // Clear existing activities

                        if (data.success && data.activities.length > 0) {
                            emptyState.style.display = 'none';
                            data.activities.forEach(activity => {
                                const activityItem = document.createElement('div');
                                activityItem.className = 'activity-item';
                                activityItem.innerHTML = `
                                    <div class="activity-icon">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h4>${activity.activity_name}</h4>
                                        <p>${activity.duration_minutes} min • ${activity.calories_burned} cal</p>
                                        <span class="time">Today</span>
                                    </div>
                                    <div class="activity-status completed">
                                        <i class="fas fa-check"></i>
                                    </div>
                                `;
                                activityList.appendChild(activityItem);
                            });
                        } else {
                            emptyState.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching recent activities:', error);
                        document.getElementById('emptyActivitiesState').style.display = 'block';
                    });
            }

            // Call the function to fetch activities on page load
            fetchRecentActivities();

            // Fetch and display daily schedule
            function fetchDailyScheduleDashboard() {
                fetch('get_daily_schedule.php')
                    .then(response => response.json())
                    .then(data => {
                        const scheduleList = document.getElementById('dailyScheduleListDashboard');
                        const emptyState = document.getElementById('emptyScheduleStateDashboard');
                        scheduleList.innerHTML = ''; // Clear existing schedules

                        if (data.success && data.schedules.length > 0) {
                            emptyState.style.display = 'none';
                            data.schedules.forEach(schedule => {
                                const scheduleItem = document.createElement('div');
                                scheduleItem.className = 'schedule-card';
                                scheduleItem.innerHTML = `
                                    <div class="schedule-time">${schedule.start_time.substring(0, 5)}</div>
                                    <div class="schedule-details">
                                        <h4>${schedule.schedule_name}</h4>
                                        <p>${schedule.description || 'No description'}</p>
                                    </div>
                                    <div class="schedule-status upcoming">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                `;
                                scheduleList.appendChild(scheduleItem);
                            });
                        } else {
                            emptyState.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching daily schedule:', error);
                        document.getElementById('emptyScheduleStateDashboard').style.display = 'block';
                    });
            }

            // Call the function to fetch daily schedule on page load
            fetchDailyScheduleDashboard();
        });
    </script>
</body>
</html>
