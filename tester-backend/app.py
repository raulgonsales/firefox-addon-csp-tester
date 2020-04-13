# flask_web/app.py

from flask import Flask
from flask import make_response
from flask import request
from flask import jsonify
import logging
import json

from lib import tests
from lib import content_script_analyzer

app = Flask(__name__)

logger = logging.getLogger('tester_backend')
logger.setLevel(logging.DEBUG)
fh_debug = logging.FileHandler('logs/debug.log')
fh_debug.setLevel(logging.DEBUG)
logger.addHandler(fh_debug)


@app.route('/test/on-start-test', methods=["POST"])
def test_addon():
    if request.method != 'POST':
        logger.error('ERROR: Unsupported call method!')

    addon_name = request.form['name']
    addon_link = request.form['link']
    addon_file = request.form['file']
    addon_id = request.form['id']

    # logger.debug('DEBUG REQUEST: Initial error for addon:\n\tname - ' + addon_name + '\n\tfile - ' + addon_file + '\n\tlink - ' + addon_link)

    tests.start_on_start_test(addon_file, addon_id)

    resp = make_response('true')
    resp.headers['Access-Control-Allow-Origin'] = '*'
    return resp


@app.route('/test/content-scripts-analyzing', methods=["POST"])
def analyze_addons_content_scripts():
    if request.method != 'POST':
        logger.error('ERROR: Unsupported call method!')

    addon_name = request.form['name']
    addon_link = request.form['link']
    addon_file = request.form['file']
    sites_matching = request.form['sites_matching']

    # logger.debug('DEBUG REQUEST: Content script analyzing for addon:\n\tname - ' + addon_name + '\n\tfile - ' + addon_file + '\n\tlink - ' + addon_link)

    try:
        response = content_script_analyzer.analyze(addon_file, json.loads(sites_matching))
    except Exception as err:
        response = 'Error on backend: ' + str(err)
        logger.debug(response)

    resp = make_response(response)
    resp.headers['Access-Control-Allow-Origin'] = '*'
    return resp


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
