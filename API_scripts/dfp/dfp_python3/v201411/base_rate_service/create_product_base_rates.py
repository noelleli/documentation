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

"""This example creates a product base rate.

To determine which base rates exist, run get_all_base_rates.py.

Tags: BaseRateService.createBaseRates
"""

__author__ = 'Nicholas Chen'

# Import appropriate modules from the client library.
from googleads import dfp

PRODUCT_ID = 'INSERT_PRODUCT_ID_HERE'
RATE_CARD_ID = 'INSERT_RATE_CARD_ID_HERE'


def main(client, product_id, rate_card_id):
  # Initialize appropriate service.
  base_rate_service = client.GetService(
      'BaseRateService', version='v201411')

  # Create a product base rate.
  product_base_rate = {
      'xsi_type': 'ProductBaseRate',
      'rateCardId': rate_card_id,
      'productId': product_id,
      # Set the rate to be $2.
      'rate': {
          'currencyCode': 'USD',
          'microAmount': 2000000
      }
  }

  # Create base rates on the server.
  base_rates = base_rate_service.createBaseRates(
      [product_base_rate])

  if base_rates:
    for base_rate in base_rates:
      print(('A product base rate with ID \'%s\' and rate \'%.2f\' %s was '
             'created.' % (base_rate['id'],
                           base_rate['rate']['microAmount'],
                           base_rate['rate']['currencyCode'])))


if __name__ == '__main__':
  # Initialize client object.
  dfp_client = dfp.DfpClient.LoadFromStorage()
  main(dfp_client, PRODUCT_ID, RATE_CARD_ID)
