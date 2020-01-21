from argparse import ArgumentParser


class ArgParser:
    @staticmethod
    def parse(args):
        parser = ArgumentParser(description='Provide arguments to run extension tester.')
        parser.add_argument('-ua', '--user-agent', type=str, help='User agent name (firefox, chrome)', required=True)
        parser.add_argument('--addon-name', type=str, help='Name of addon', required=True)
        parser.add_argument('--addon-link', type=str, help='Link to addon in the store', required=True)
        parser.add_argument('--testing-page', type=str, help='Link to testing page', default='/')

        return parser.parse_args(args)
