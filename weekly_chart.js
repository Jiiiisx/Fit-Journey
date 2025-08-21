// Weekly Calorie Chart Implementation
document.addEventListener('DOMContentLoaded', function() {
    // Function to get weekly calorie data
    function fetchWeeklyCalorieData() {
        fetch('get_weekly_calories.php')
            .then(response => response.json())
            .then(data => {
                renderWeeklyCalorieChart(data);
            })
            .catch(error => {
                console.error('Error fetching weekly calorie data:', error);
                // Fallback to dummy data if fetch fails
                const dummyData = {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    calories: [2000, 2200, 1950, 2100, 2300, 2050, 2150]
                };
                renderWeeklyCalorieChart(dummyData);
            });
    }

    // Function to render the weekly calorie chart
    function renderWeeklyCalorieChart(data) {
        const ctx = document.getElementById('weeklyCalorieChart');
        if (!ctx) return;

        const chart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Daily Calories',
                    data: data.calories,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(34, 197, 94, 0.8)',  // Hijau
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(118, 75, 162, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(34, 197, 94, 1)',   // Hijau
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' calories';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' cal';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // Initialize the chart
    fetchWeeklyCalorieData();
});
