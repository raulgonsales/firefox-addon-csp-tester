<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <link href="https://fonts.googleapis.com/css?family=Inconsolata&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <script type="text/javascript" src="{{asset('/js/jquery.canvasjs.min.js')}}"></script>
        <title>Laravel</title>
    </head>
    <body id="body">
        <header>
            <div class="control">
                <div class="buttons">
                    <a class="btn btn-success" href="/">Home</a>
                </div>
            </div>
        </header>
        <section>
            <div class="stat-control-panel">
                <label for="only_recommended">Only recommended by Firefox</label>
                <input type="checkbox"
                       name="graph_params"
                       id="only_recommended"
                       data-name="firefox_recommend" @if(app('request')->input('firefox_recommend'))checked @endif>
            </div>
            <div id="sitesChartContainer"></div>
        </section>
    </body>

    <script>
        $('input[name=graph_params]').on('change', function () {
            let nameOfClickedCheckbox = $(this).data('name');
            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);

            if (this.checked) {
                params.set(nameOfClickedCheckbox, '1')
            } else {
                if (params.get(nameOfClickedCheckbox)) {
                    params.delete(nameOfClickedCheckbox);
                }
            }

            window.location.href = location.protocol + '//' + location.host + location.pathname + '?' + params.toString();
        });

        function compareDataPointYAscend(dataPoint1, dataPoint2) {
            return dataPoint1.y - dataPoint2.y;
        }

        window.onload = function() {
            var chart = new CanvasJS.Chart("sitesChartContainer", {
                animationEnabled: true,
                height: 400,
                title:{
                    text: "Count of addons causing CSP errors when a user enters the web page containing the CSP header",
                    fontSize: 20
                },
                axisY: {
                    title: "Extensions count",
                    titleFontSize: 17,
                    labelFontSize: 13,
                    tickLength: 10
                },
                axisX: {
                    labelFontSize: 15,
                    interval: 1
                },
                options: {
                    maintainAspectRatio: false,
                },
                data: [{
                    type: "column",
                    indexLabel: "{y}",
                    indexLabelPlacement: "inside",
                    indexLabelFontWeight: "bolder",
                    indexLabelFontColor: "white",
                    dataPoints: <?php echo json_encode($graphDataPoints, JSON_NUMERIC_CHECK); ?>,
                    click: function(e){
                        let url = new URL(window.location.href);
                        let params = new URLSearchParams(url.search);

                        params.set('site_id', e.dataPoint.site_id);

                        window.location.href = location.protocol + '//' + location.host + location.pathname + '?' + params.toString();
                    }
                }]
            });
            chart.options.data[0].dataPoints.sort(compareDataPointYAscend);
            chart.render();
        }
    </script>
</html>
