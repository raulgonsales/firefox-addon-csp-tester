from flask import jsonify
import json
import os
import shutil
import zipfile


def analyze(addon_file, sites_matching):
    unzip_addon(addon_file)

    with open('/usr/src/app/resources/addons/unziped/' + addon_file + '/manifest.json', 'r') as openfile:
        json_object = json.load(openfile)

    unzipped_addon_path = '/usr/src/app/resources/addons/unziped/' + addon_file

    sites_info = {}

    for site_id in sites_matching:
        matching_url = sites_matching[site_id]
        sites_info[site_id] = False

        if 'content_scripts' not in json_object:
            continue

        content_scripts = []
        for content_script_item in json_object['content_scripts']:
            if matching_url not in content_script_item['matches']:
                continue
            if 'js' not in content_script_item:
                continue

            content_scripts += content_script_item['js']

        if len(content_scripts) == 0:
            continue

        content_scripts = list(set(content_scripts))

        scripts_info = {}
        scripts_with_signs_count = 0
        for script in content_scripts:
            scripts_info[script] = process_script(unzipped_addon_path + '/' + script)
            if scripts_info[script] is not False:
                scripts_with_signs_count += 1

        sites_info[site_id] = {
                "use_content_scripts": True,
                "content_scripts_count": len(content_scripts),
                "content_scripts_count_with_signs": scripts_with_signs_count,
                "scripts_info": scripts_info
            }

    return jsonify(sites_info)


def process_script(script_path):
    script_injecting_signs = [
        "injectScript",
        "insertScript",
        "appendScript",
        "insertBefore(script",
        "insertBefore(<script",
        "appendChild(script",
        "appendChild(<script",
        "document.createElement('script')"
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
            'found_script_injection_signs': found_signs
        }
    else:
        return False


def unzip_addon(addon_file):
    unziped_dirpath = '/usr/src/app/resources/addons/unziped/' + addon_file
    addon_filepath = '/usr/src/app/resources/addons/' + addon_file

    with zipfile.ZipFile(addon_filepath, 'r') as zip_ref:
        if os.path.isdir(unziped_dirpath):
            shutil.rmtree(unziped_dirpath)

        os.mkdir(unziped_dirpath)
        zip_ref.extractall(unziped_dirpath)
