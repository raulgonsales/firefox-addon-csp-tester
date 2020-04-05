from selenium import webdriver
from pyvirtualdisplay import Display
import boto3
from dotenv import load_dotenv
import os

load_dotenv()

s3_client = boto3.client(
    's3',
    aws_access_key_id=os.getenv('AWS_ACCESS_KEY'),
    aws_secret_access_key=os.getenv('AWS_SECRET_KEY')
)
bucket_name = 'firefox-addons-tester'
s3_addons_folder = 'addons-files/'

local_addons_folder = '/usr/src/app/resources/addons/'


def initial_error_test(addon_file, addon_id):
    download_addon_file(addon_file)

    display = Display(visible=0, size=(800, 600))
    display.start()

    browser = webdriver.Firefox()
    browser.install_addon('/usr/src/app/resources/addons/' + addon_file)
    browser.get('http://172.22.0.3/test?addon_id=' + str(addon_id) + '&test_type=initial-error')
    browser.quit()
    display.stop()

    delete_addon_file(addon_file)


def download_addon_file(file_name):
    if os.path.exists(local_addons_folder + file_name):
        return

    s3_client.download_file(bucket_name, s3_addons_folder + file_name, local_addons_folder + file_name)


def delete_addon_file(file_name):
    if os.path.exists(local_addons_folder + file_name):
        os.remove(local_addons_folder + file_name)
