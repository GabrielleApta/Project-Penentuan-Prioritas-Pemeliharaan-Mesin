document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById("myPieChart");
    if (!ctx) return;

    const myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Blue", "Red", "Yellow", "Green"],
            datasets: [{
                data: [12.21, 15.58, 11.25, 8.32],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Arial'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ": " + context.parsed.toFixed(2) + "%";
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Contoh Pie Chart'
                }
            }
        }
    });
});
