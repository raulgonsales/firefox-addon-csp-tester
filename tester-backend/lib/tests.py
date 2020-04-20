from selenium import webdriver
import boto3
from dotenv import load_dotenv
from selenium.webdriver.firefox.options import Options
from flask import jsonify
import json
import os
import shutil
import zipfile
import codecs
import fnmatch, re

load_dotenv()

s3_client = boto3.client(
    's3',
    aws_access_key_id=os.getenv('AWS_ACCESS_KEY'),
    aws_secret_access_key=os.getenv('AWS_SECRET_KEY')
)
bucket_name = 'firefox-addons-tester'
s3_addons_folder = 'addons-files/'

local_addons_folder = '/usr/src/app/resources/addons/'


def start_on_start_test(addon_file, addon_id):
    download_addon_file(addon_file)

    options = Options()
    options.headless = True
    browser = webdriver.Firefox(options=options)
    browser.install_addon('/usr/src/app/resources/addons/' + addon_file)
    browser.get('http://172.22.0.2/test?addon_id=' + str(addon_id) + '&test_type=on-start-test')
    browser.quit()

    delete_addon_file(addon_file)


def analyze(addon_file, sites_matching):
    download_addon_file(addon_file)
    unzip_addon(addon_file)

    with codecs.open('/usr/src/app/resources/addons/unziped/' + addon_file + '/manifest.json', 'r', 'utf-8-sig') as openfile:
        json_object = json.load(openfile)

    addon_unzipped_dirpath = '/usr/src/app/resources/addons/unziped/' + addon_file

    sites_info = {}

    for site_id in sites_matching:
        matching_url = sites_matching[site_id]
        sites_info[site_id] = False

        if 'content_scripts' not in json_object:
            continue

        content_scripts_batches = []
        run_at = None
        for content_script_item in json_object['content_scripts']:
            if not find_matching_url(matching_url, content_script_item['matches']):
                continue
            if 'js' not in content_script_item:
                continue

            if 'run_at' in content_script_item:
                run_at = content_script_item['run_at']

            content_scripts_batches.append({
                'content_scripts': list(set(content_script_item['js'])),
                'run_at': run_at
            })

        if len(content_scripts_batches) == 0:
            continue

        scripts_info = {}
        scripts_with_signs_count = 0
        content_scripts_count = 0
        for batch in content_scripts_batches:
            content_scripts_count += len(batch['content_scripts'])

            for script in batch['content_scripts']:
                if script in scripts_info:
                    continue

                scripts_info[script] = process_script(addon_unzipped_dirpath + '/' + script, batch['run_at'])
                if scripts_info[script] is not False:
                    scripts_with_signs_count += 1

        sites_info[site_id] = {
            "use_content_scripts": True,
            "run_at": run_at,
            "content_scripts_count": content_scripts_count,
            "content_scripts_count_with_signs": scripts_with_signs_count,
            "scripts_info": scripts_info
        }

    delete_addon_unzipped_files(addon_file)
    delete_addon_file(addon_file)

    return jsonify(sites_info)


def find_matching_url(find_url, urls):
    print(find_url)
    for url in urls:
        if url == '<all_urls>':
            print(True)
            return True

        print(url)
        regex = fnmatch.translate(url)

        if bool(re.compile(regex).match(find_url)):
            print(True)
            return True

    print(False)
    return False


def process_script(script_path, run_at):
    script_injecting_signs = [
        "injectScript(",
        "insertScript(",
        "appendScript(",
        "insertBefore(script",
        "insertBefore(scrpt",
        "insertBefore(<script",
        "appendChild(script",
        "appendChild(scrpt",
        "appendChild(<script",
        "appendChild(<scrpt",
        ".createElement('script')",
        ".createElement(\"script\")",
        ".createElement(script)",
        ".createElement(scrpt)"
    ]

    with open(script_path, 'r') as file:
        line_number = 1
        line = file.readline()
        found_signs = []
        while line:
            for sign in script_injecting_signs:
                if str(line).find(sign) != -1:
                    sign_info = {
                        'sign': sign,
                        'line': line_number
                    }
                    found_signs.append(sign_info)

            line = file.readline()
            line_number += 1

    if len(found_signs) > 0:
        return {
            'found_script_injection_signs': found_signs,
            'run_at': run_at
        }
    else:
        return False


def download_addon_file(file_name):
    if os.path.exists(local_addons_folder + file_name):
        return

    s3_client.download_file(bucket_name, s3_addons_folder + file_name, local_addons_folder + file_name)


def delete_addon_file(file_name):
    if os.path.exists(local_addons_folder + file_name):
        os.remove(local_addons_folder + file_name)


def delete_addon_unzipped_files(file_name):
    if os.path.exists(local_addons_folder + 'unziped/' + file_name):
        shutil.rmtree(local_addons_folder + 'unziped/' + file_name)


def unzip_addon(addon_file):
    unzipped_dirpath = '/usr/src/app/resources/addons/unziped/'

    if not os.path.isdir(unzipped_dirpath):
        os.mkdir(unzipped_dirpath)

    addon_unzipped_dirpath = '/usr/src/app/resources/addons/unziped/' + addon_file
    addon_filepath = '/usr/src/app/resources/addons/' + addon_file

    with zipfile.ZipFile(addon_filepath, 'r') as zip_ref:
        if os.path.isdir(addon_unzipped_dirpath):
            shutil.rmtree(addon_unzipped_dirpath)

        os.mkdir(addon_unzipped_dirpath)
        zip_ref.extractall(addon_unzipped_dirpath)
