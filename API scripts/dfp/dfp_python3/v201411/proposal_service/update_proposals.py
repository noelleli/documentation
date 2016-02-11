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

"""This code example updates the notes of a single proposal specified by ID.

To determine which proposals exist, run get_all_proposals.py."""

__author__ = 'Nicholas Chen'

# Import appropriate modules from the client library.
from googleads import dfp

PROPOSAL_ID = 'INSERT_PROPOSAL_ID_HERE'


def main(client, proposal_id):
  # Initialize appropriate service.
  proposal_service = client.GetService('ProposalService', version='v201411')

  # Create statement object to select a single proposal by an ID.
  values = [{
      'key': 'proposalId',
      'value': {
          'xsi_type': 'NumberValue',
          'value': proposal_id
      }
  }]
  query = 'WHERE id = :proposalId'

  statement = dfp.FilterStatement(query, values, 1)

  # Get proposals by statement.
  response = proposal_service.getProposalsByStatement(statement.ToStatement())

  if 'results' in response:
    # Update each local proposal object by changing its notes.
    updated_proposals = []
    for proposal in response['results']:
      proposal['notes'] = 'Proposal needs further review before approval.'
      updated_proposals.append(proposal)

    # Update proposals remotely.
    proposals = proposal_service.updateProposals(updated_proposals)

    # Display results.
    if proposals:
      for proposal in proposals:
        print(('Proposal with id \'%s\', name \'%s\', and '
               'notes \'%s\' was updated.'
               % (proposal['id'], proposal['name'], proposal['notes'])))
    else:
      print('No proposals were updated.')
  else:
    print('No proposals found to update.')

if __name__ == '__main__':
  # Initialize client object.
  dfp_client = dfp.DfpClient.LoadFromStorage()
  main(dfp_client, PROPOSAL_ID)
