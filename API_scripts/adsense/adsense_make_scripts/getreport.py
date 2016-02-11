#!/usr/bin/python
#
# Copyright 2014 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""Retrieves a saved report or generates a new one.
To get ad clients, run get_all_ad_clients.py.
Tags: reports.generate
"""

__author__ = 'jalc@google.com (Jose Alcerreca)'

import argparse
import sys
import json


#from adsense_util import get_account_id
from adsense_util_data_collator import DataCollator
from apiclient import sample_tools
from oauth2client import client


# Declare command-line flags.
argparser = argparse.ArgumentParser(add_help=False)
argparser.add_argument(
    '--report_id',
    help='The ID of the saved report to generate')

def main(argv):
# Authenticate and construct service.
  service, flags = sample_tools.init(
      argv, 'adsense', 'v1.4', __doc__, __file__, parents=[argparser],
      scope='https://www.googleapis.com/auth/adsense.readonly')

  # Process flags and read their values.
  saved_report_id = flags.report_id

  try:
    # Let the user pick account if more than one.
    account_id = 'pub-1711976718738240'

    # Retrieve report.
    if saved_report_id:
      result = service.accounts().reports().saved().generate(
          accountId=account_id, savedReportId=saved_report_id).execute()
    else:
      result = service.accounts().reports().generate(
          accountId=account_id, startDate='2015-09-01', endDate='2015-09-13',
          metric=['PAGE_VIEWS', 'INDIVIDUAL_AD_IMPRESSIONS', 'CLICKS', 'EARNINGS'],
          dimension=['DATE', 'OWNED_SITES'],
          sort=['+DATE']).execute()
    datarows = DataCollator([result]).collate_data()
    with open ('data.json', 'w') as outfile:
            json.dump(datarows, outfile)
  

  except client.AccessTokenRefreshError:
    print ('The credentials have been revoked or expired, please re-run the '
           'application to re-authorize')

if __name__ == '__main__':
  main(sys.argv)
