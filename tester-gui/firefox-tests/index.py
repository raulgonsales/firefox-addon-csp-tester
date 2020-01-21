import sys

from app.helpers.ArgParser import ArgParser


def get_tester(browser, arguments):
    if browser == 'firefox':
        from app.models.testers.FirefoxTester import FirefoxTester
        return FirefoxTester(arguments.addon_name, arguments.addon_link, arguments.testing_page)


if __name__ == '__main__':
    arguments = ArgParser.parse(sys.argv[1:])

    tester = get_tester('firefox', arguments)

    tester.run()
