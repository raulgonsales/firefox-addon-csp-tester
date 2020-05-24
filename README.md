## Requirements
`php 7.2`

`Docker`

`Composer`

## Installation
There are two variants how to run the application: 
1. Run clear application with empty tables in the database. Only `sites` table has list of 
web sites to provide the `manifest.json analysis`. 
                
        ./run_clear_app.sh

2. Run the application with all tests provided. Full statistic is present.

        ./run_final_app.sh

## Licence
This project uses library CanvasJS to draw statistic graphs.

CanvasJS is a paid product and it requires purchasing license - https://canvasjs.com/license/
