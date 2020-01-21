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
        <title>Laravel</title>
    </head>
    <body id="body">
        <header>
            Test
        </header>
        <section>
            <div class="control">
                <div class="buttons">
                    <button type="button" class="btn btn-primary">Test selected</button>
                    <button type="button" class="btn btn-primary">Full test</button>
                </div>
                <div class="searchbox">
{{--                    TODO bootstrap select--}}
                    <div class="active-cyan-3 active-cyan-4 mb-4">
                        <input class="form-control" type="text" placeholder="Search addon" aria-label="Search">
                    </div>
                </div>
            </div>
            <div class="addons-wrapper">
                @foreach($addons as $addon)
                    <div class="addon-wrapper">
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
                                                data-link="{{$firefoxLink . $addon['link']}}">
                                            Test addon
                                        </button>
                                        <a class="btn btn-primary" target="_blank" href="{{$firefoxLink . $addon['link']}}" role="button">Go to web page</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </body>
</html>
