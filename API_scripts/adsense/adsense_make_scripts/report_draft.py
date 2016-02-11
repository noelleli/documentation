#!/usr/bin/python
 
# python libraries
import sys, datetime, json
from pytz import timezone
 
#google libraries
from oauth2client.client import AccessTokenRefreshError
 
# app files
import utils
 
def main(argv):
    get_stats()
     
def get_stats() :
     
    # Authenticate and construct service.
    service = sample_utils.initialize_service()
     
    #client_id - get this from your adsense login
    ad_client_id = 'pub-1711976718738240'
     
    #adsense timezone is usa pacific http://support.google.com/adsense/bin/answer.py?hl=en&answer=59143
    now_pacific = datetime.datetime.now(timezone('US/Pacific'))
    today_pacific = now_pacific.strftime('%Y-%m-%d')
     
    yesterday_pacific = now_pacific - datetime.timedelta(1)
    yesterday_pacific = yesterday_pacific.strftime('%Y-%m-%d')
     
    month_first = now_pacific.strftime('%Y-%m-01')
     
    # print the dates of today and yesterday, these are used to define timespans for the report
    print (today_pacific)
    print (yesterday_pacific)
     
    #print today_pacific
    try:
        all_data = {}
         
        sets = {
            'today' : [today_pacific , today_pacific] ,
            'yesterday' : [yesterday_pacific , yesterday_pacific] ,
            'this_month' : [month_first , today_pacific]
            }
         
        for k,v in sets.items() :
            # Retrieve report. result is a json object
            result = service.reports().generate(
                startDate = v[0] , 
                endDate = v[1] ,
                filter=['AD_CLIENT_ID==' + ad_client_id],
                metric=['PAGE_VIEWS', 'CLICKS', 'PAGE_VIEWS_CTR', 'COST_PER_CLICK', 'AD_REQUESTS_RPM', 'EARNINGS'],
                #dimension=['DATE'],
                #sort=['+DATE']
            ).execute()
             
            #dumping json object - you may want to dump, to see the structure and use next
            #print json.dumps(result, sort_keys=True, indent=4)
             
            # Display headers
            '''for header in result['headers']:
                print '%15s' % header['name'],
            print'''
            data = {}
            # Display results
            if 'rows' in result :
                row = result['rows'][0]
                 
                data['page_view'] = row[0]
                data['clicks'] = row[1]
                data['ctr'] = row[2]
                data['cpc'] = row[3]
                data['rpm'] = row[4]
                data['earnings'] = row[5]
                 
                '''for row in result['rows']:
                    for column in row:
                        print '%15s' % column ,
                    print'''
             
            all_data[k] = data
         
        print (str(all_data))
     
    #authorization problem
    except AccessTokenRefreshError:
        print ('The credentials have been revoked or expired, please re-run the application to re-authorize')
 
# main function
if __name__ == '__main__':
    main(sys.argv) 