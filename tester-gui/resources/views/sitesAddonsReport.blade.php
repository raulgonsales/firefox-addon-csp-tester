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
        <script type="text/javascript" src="{{asset('/js/plotly-latest.min.js')}}"></script>
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
            <div id="sites_addons_info">

            </div>
        </section>
    </body>

    <script type="text/javascript">
        let sitesInfo = @json($sites);

        let x = [];
        let y = [];

        for (let siteInfo of sitesInfo) {
            console.log();
            x.push(siteInfo.site_name);
            y.push(siteInfo.related_addons.length);
        }

        let data = [
            {
                histfunc: "sum",
                y: y,
                x: x,
                type: "histogram",
                name: "Addons count by error type"
            }
        ];

        Plotly.newPlot(document.getElementById('sites_addons_info'), data);
    </script>
</html>
