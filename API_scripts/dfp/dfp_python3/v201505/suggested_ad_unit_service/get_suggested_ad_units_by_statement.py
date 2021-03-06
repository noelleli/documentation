#!/usr/bin/python
#
# Copyright 2015 Google Inc. All Rights Reserved.
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

"""This code example gets suggested ad units that have more than 50 requests.

This feature is only available to DFP premium solution networks.
"""

__author__ = ('Nicholas Chen',
              'Joseph DiLallo')

# Import appropriate modules from the client library.
from googleads import dfp


def main(client):
  # Initialize appropriate service.
  suggested_ad_unit_service = client.GetService(
      'SuggestedAdUnitService', version='v201505')

  values = [{
      'key': 'numRequests',
      'value': {
          'xsi_type': 'NumberValue',
          'value': '50'
      }
  }]

  query = 'WHERE numRequests > :numRequests'

  # Create a filter statement.
  statement = dfp.FilterStatement(query, values)

  # Get suggested ad units by statement.
  while True:
    response = suggested_ad_unit_service.getSuggestedAdUnitsByStatement(
        statement.ToStatement())
    if 'results' in response:
      # Display results.
      for suggested_ad_unit in response['results']:
        print(('Ad unit with id \'%s\' and number of requests \'%s\' was found.'
               % (suggested_ad_unit['id'], suggested_ad_unit['numRequests'])))
      statement.offset += dfp.SUGGESTED_PAGE_LIMIT
    else:
      break

  print('\nNumber of results found: %s' % response['totalResultSetSize'])

if __name__ == '__main__':
  # Initialize client object.
  dfp_client = dfp.DfpClient.LoadFromStorage()
  main(dfp_client)
