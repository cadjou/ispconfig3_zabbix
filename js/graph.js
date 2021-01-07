function loadChart(conf) {
    setTimeout(function () {
        var ctx = document.getElementById('myChart').getContext('2d');
        myChart = new Chart(ctx, conf);
    }, 1000);
}