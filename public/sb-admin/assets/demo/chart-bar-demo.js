document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById("myBarChart");
    if (!ctx) return; // hindari error jika elemen tidak ada

    const myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["January", "February", "March", "April", "May", "June"],
            datasets: [{
                label: "Revenue",
                backgroundColor: "rgba(2,117,216,1)",
                borderColor: "rgba(2,117,216,1)",
                data: [4215, 5312, 6251, 7841, 9821, 14984],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    },
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                },
                y: {
                    min: 0,
                    max: 15000,
                    ticks: {
                        maxTicksLimit: 5,
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    grid: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Pendapatan (Rp)'
                    }
                }
            }
        }
    });
});
