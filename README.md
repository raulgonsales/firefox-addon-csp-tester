## Requirements
`php 7.2`

`Docker`

`Composer`

## Installation
There are two variants how to run the application: 
1. Run clear application with empty tables in the database. Only `sites` table has list of 
web sites to provide the `manifest.json` analysis. 
                
        ./run_clear_app.sh

2. Run the application with all tests provided. Full statistic is present.

        ./run_final_app.sh

## Usage

!!! IMPORTANT: all commands have to be run inside of `app` docker container. !!!

    docker-compose exec app bash

Extracts all information about extensions from the `addons.mozilla.org`. Enabled parameter `--download-files` triggers
downloading archived source code of an extension in `.XPI` format. The application stores each archive into AWS S3 bucket.

    php artisan downloader:addons-info --start-page=3 --final-page=5 --download-files
    
The command below goes through the `addons` database table and only downloads archived source code and uploads it to AWS S3. 
    
    php artisan downloader:addons-files

## Licence
This project uses library CanvasJS to draw statistic graphs.

CanvasJS is a paid product and it requires purchasing license - https://canvasjs.com/license/
