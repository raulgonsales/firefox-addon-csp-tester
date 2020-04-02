from selenium import webdriver
from pyvirtualdisplay import Display


def initial_error_test(addon_file, addon_id):
    display = Display(visible=0, size=(800, 600))
    display.start()

    browser = webdriver.Firefox()
    browser.install_addon('/usr/src/app/resources/addons/' + addon_file)
    browser.get('http://172.19.0.3/test?addon_id=' + str(addon_id) + '&test_type=initial-error')
    browser.quit()
    display.stop()