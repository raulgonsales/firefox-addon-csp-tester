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
        <script type="text/javascript" src="{{mix('/js/app.js')}}"></script>
        <script type="text/javascript" src="{{asset('/js/plotly-latest.min.js')}}"></script>
        <title>Laravel</title>
    </head>
    <body id="body">
        <header>
            <div class="control">
                <div class="buttons">
                    <button class="btn btn-info" id="select_all_addons">Select all</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Deselect all
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item addons-deselect-button" data-type="current">Current page</button>
                            <button class="dropdown-item addons-deselect-button" data-type="all">Through all pages</button>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filter
                        </button>
                        <div class="dropdown-menu">
                            <a href="http://localhost:998?errorType=on-start-test" class="dropdown-item test-selected" data-error-type="on-start-test">on-start-test</a>
                            <a href="http://localhost:998?errorType=no-errors" class="dropdown-item test-selected" data-error-type="no-errors">no-errors</a>
                            <a href="http://localhost:998" class="dropdown-item test-selected">all</a>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Test selected
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item test-selected test-selected-on-start-test" data-error-type="on-start-test">on-start-test</button>
                            <button class="dropdown-item test-selected test-selected-content-scripts-analyzing" data-error-type="content-scripts-analyzing">content-scripts-analyzing</button>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Full test
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item full-test" data-error-type="on-start-test">on-start-test</button>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Generate report
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item report-all" data-error-type="on-start-test">For all</button>
                            <button class="dropdown-item report-selected" data-error-type="on-start-test">For selected</button>
                        </div>
                    </div>

                    <button class="btn btn-info hidden" id="show_all_report">Show report for all</button>

                    <a class="btn btn-success" href="/sites-addons-report">Show report for all</a>
                </div>
                <div class="searchbox">
                    {{--                    TODO bootstrap select--}}
                    <div class="active-cyan-3 active-cyan-4 mb-4">
                        <input class="form-control" type="text" placeholder="Search addon" aria-label="Search">
                    </div>
                </div>
            </div>
        </header>
        <section>
            <div class="addons-wrapper">
                @foreach($addons as $addon)
                    <div class="addon-wrapper" id="addon_item_{{$addon['id']}}">
                        <input type="checkbox" class="check-addon" name="kek" id="addon_checkbox_{{$addon['id']}}">
                        <script type="text/javascript">
                            // script for autoselect addon which was selected before reload of the page
                            var addonId = {!! $addon['id'] !!};
                            if (window.sessionStorage.getItem('selectedAddons') !== null) { //if no addons were selected
                                let selectedAddons = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
                                if (selectedAddons.addons.hasOwnProperty(addonId)) {
                                    document.getElementById('addon_checkbox_' + addonId).checked = true;
                                }
                            }
                        </script>
                        <div class="addon">
                            @if($addon['firefox_recommend'])
                                <span class="badge badge-pill badge-warning">Recommended by Firefox</span>
                            @endif
                            <div class="addon-content">
                                <div class="addon-icon">
                                    <img src="{{$addon['img_name']}}" alt="">
                                </div>
                                <div class="addon-info">
                                    <p class="name" data-toggle="tooltip" data-placement="top" title="{{$addon['name']}}">{{$addon['name']}}</p>
                                    <p class="users">Users: {{$addon['users_count']}}</p>
                                    <div class="addon-controls">
                                        <button type="button"
                                                class="btn btn-primary test-addon-button"
                                                data-name="{{$addon['name']}}"
                                                data-file="{{$addon['file_name']}}"
                                                data-link="{{$firefoxLink . $addon['link']}}"
                                                data-id="{{$addon['id']}}">
                                            Test addon
                                        </button>
                                        <a class="btn btn-primary" target="_blank" href="{{$firefoxLink . $addon['link']}}" role="button">Go to web page</a>
                                    </div>
                                </div>
                            </div>
                            <div class="error-type">
                                <p>
                                    CSP reports count:
                                    <span class="error-type-content">
                                        @if(count($addon->cspReports) > 0)
                                            {{count($addon->cspReports)}}
                                        @else
                                            no errors
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $addons->links() }}
        </section>
        <div id="tester" style="width:600px;height:250px;"></div>
        @include('reportAllModal')
        @include('sitesListModal')
    </body>
</html>
