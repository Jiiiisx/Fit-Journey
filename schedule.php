<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitney Schedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/Dashboard-pelanggan.css"> <!-- Reusing some styles -->
    <link rel="stylesheet" href="css/Schedule.css">
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
            <a href="Food.php" class="nav-item">
                <i class="fas fa-utensils"></i>
                <span>Calorie Tracker</span>
            </a>
            <a href="schedule.php" class="nav-item active">
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
            <div class="schedule-section">
                <h3>Today's Schedule</h3>
                <div class="schedule-cards" id="dailyScheduleList">
                    <!-- Schedules will be loaded here dynamically -->
                </div>
                <div class="empty-state" id="emptyScheduleState" style="display: none;">
                    <i class="fas fa-calendar-check"></i>
                    <p>No schedule entries for today.</p>
                    <small>Add your daily plans here!</small>
                </div>
            </div>

            <div class="add-schedule-section">
                <h3>Add New Schedule</h3>
                <form id="addScheduleForm" class="schedule-form">
                    <div class="form-group">
                        <label for="scheduleName">Schedule Name</label>
                        <input type="text" id="scheduleName" placeholder="e.g., Morning Workout" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="startTime">Start Time</label>
                            <input type="time" id="startTime" required>
                        </div>
                        <div class="form-group">
                            <label for="endTime">End Time</label>
                            <input type="time" id="endTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea id="description" placeholder="e.g., Focus on cardio and abs"></textarea>
                    </div>
                    <button type="submit" class="btn-add-schedule">
                        <i class="fas fa-plus"></i> Add Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fetchDailySchedule() {
                fetch('get_daily_schedule.php')
                    .then(response => response.json())
                    .then(data => {
                        const scheduleList = document.getElementById('dailyScheduleList');
                        const emptyState = document.getElementById('emptyScheduleState');
                        scheduleList.innerHTML = ''; // Clear existing schedules

                        if (data.success && data.schedules.length > 0) {
                            emptyState.style.display = 'none';
                            data.schedules.forEach(schedule => {
                                const scheduleItem = document.createElement('div');
                                scheduleItem.className = 'schedule-card';
                                scheduleItem.innerHTML = `
                                    <div class="schedule-time">${schedule.start_time.substring(0, 5)} - ${schedule.end_time.substring(0, 5)}</div>
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
                        document.getElementById('emptyScheduleState').style.display = 'block';
                    });
            }

            document.getElementById('addScheduleForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const scheduleName = document.getElementById('scheduleName').value.trim();
                const startTime = document.getElementById('startTime').value;
                const endTime = document.getElementById('endTime').value;
                const description = document.getElementById('description').value.trim();

                if (!scheduleName || !startTime || !endTime) {
                    alert('Please fill in all required fields.');
                    return;
                }

                fetch('save_daily_schedule.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        schedule_name: scheduleName,
                        start_time: startTime,
                        end_time: endTime,
                        description: description
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Jadwal berhasil disimpan!');
                        this.reset();
                        fetchDailySchedule(); // Reload schedules after saving
                    } else {
                        alert('Gagal menyimpan jadwal: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan jadwal.');
                });
            });

            fetchDailySchedule(); // Initial fetch on page load
        });
    </script>
</body>
</html>