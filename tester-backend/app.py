# flask_web/app.py
from flask import Flask
from flask import make_response
from flask import request
import json

from lib import tests

app = Flask(__name__)


@app.route('/test/on-start-test', methods=["POST"])
def test_addon():
    """
    API endpoint for running on-start-test
    """

    addon_file = request.form['file']
    addon_id = request.form['id']
    test_type = request.form['test_type']
    domain = request.form['domain']

    # start test
    tests.start_on_start_test(addon_file, addon_id, test_type, domain)

    resp = make_response('true')
    resp.headers['Access-Control-Allow-Origin'] = '*'
    return resp


@app.route('/test/content-scripts-analysis', methods=["POST"])
def analyze_addons_content_scripts():
    """
    API endpoint for running manifest.json analysis
    """
    addon_file = request.form['file']
    sites_matching = request.form['sites_matching']

    response = tests.analyze(addon_file, json.loads(sites_matching))

    resp = make_response(response)
    resp.headers['Access-Control-Allow-Origin'] = '*'
    return resp


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
