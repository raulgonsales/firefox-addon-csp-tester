from selenium import webdriver
from selenium.webdriver.common.keys import Keys

from app.config import config


class FirefoxTester:

    driver = webdriver.Firefox(log_path=config.DRIVER_LOG_PATH)

    def __init__(self, addon_name, addon_link, testing_page):
        self.addon_link = addon_link
        self.addon_name = addon_name
        self.testing_page = testing_page

    def run(self):
        self.navigate_start_page()
        self.install_addon()

        self.driver.close()

    def install_addon(self):
        self.driver.install_addon(config.ADDONS_ABSOLUTE_PATH + self.addon_name + '.xpi')

    def navigate_start_page(self):
        self.driver.get(config.WEB_INTERFACE_HOST + ':' + config.WEB_INTERFACE_PORT + self.testing_page)

