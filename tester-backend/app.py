# flask_web/app.py

from flask import Flask
from flask import make_response
from flask import request
import logging

from lib import tests

app = Flask(__name__)

logger = logging.getLogger('tester_backend')
logger.setLevel(logging.DEBUG)
fh_debug = logging.FileHandler('logs/debug.log')
fh_debug.setLevel(logging.DEBUG)
logger.addHandler(fh_debug)


@app.route('/test/initial-error', methods=["POST"])
def test_addon():
    if request.method != 'POST':
        logger.error('ERROR: Unsupported call method!')

    addon_name = request.form['name']
    addon_link = request.form['link']
    addon_file = request.form['file']

    logger.debug('DEBUG REQUEST: Initial error for addon:\n\tname - ' + addon_name + '\n\tfile - ' + addon_file + '\n\tlink - ' + addon_link)

    tests.initial_error_test(addon_file)

    resp = make_response('true')
    resp.headers['Access-Control-Allow-Origin'] = '*'
    return resp


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
