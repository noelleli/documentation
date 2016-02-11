## Basic:
- Account, profile, views all need a bit cleaning up.
- User management also needs clean-up. 

    __NOTE:__ do not remove "*@developer.gserviceaccount.com". It is linked to google reporting api credentials and key.
    
- __Custom Dimensions__: only makezine and makershed have custom dimensions enabled. Makezine currently has only one active dimension.  Makershed custom dimensions are for remarketing campaigns. New dimension can be created by adding new label, or by changing the inactive dimension name/settings. Each profile has limited amount (~ 25) of custom dimensions. Please do recycle. 
- Optimizely also uses with Custom Dimensions to tag A vs B campaign. see [here](https://help.optimizely.com/hc/en-us/articles/200039995-Integrating-Optimizely-with-Google-Universal-Analytics).
- __Calculated Metrics__ works in view level __not profile level__. Only makezine has calculated metrics enabled, and we used up 5 of them). See below for detail.
- __Daily report__ can be created in "Dashboards". Each dashboard can have __max 12 widgets__. See daily/weekl flash for detail.
- __MM Email Campaign Tracking:__ [PLEASE READ THIS](https://developers.google.com/analytics/devguides/collection/protocol/v1/email). This profile was originally created to track email opens and clicks down to each and every email subscriber level. It tracks when the link is clicked, which link, in which email from which campaign. It is designed to combine with WhatCounts APIs for dashboarding/data analysis. Feel free to delete this profile. If so, please make sure to __notify__ email team not to put in the tracking in each email to save them some time. 

## Daily/Weekly Flash:
- In GA, email report is created by creating new Dashboard (or modify existing one).  
- __Modify existing email distribution list or scheduling__ doesn't go though dashboard. It goes through "Scheduled Emails" : View profile -> Admin -> Personal Tools & Assests -> Schedule Emails.
- Each dashboard can have __max 12 widgets__. 
- Dashboard widget only provides basic standard configurations. User filtering to get a bit of customization.

![GA_graph1](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph1.png)

![GA_graph2](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph2.png)

![GA_graph3](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph3.png)

- When initiate a new email report, make sure the date range is selected as one of the non-custom range, e.g. Yesterday, Last Week, or Last Month. If a custom range is selected the report will always sent with data of that same date range. 

## Calculated Metrics:
- __Pros:__ getting computed results from existing metrics.
- __Cons:__ can't apply filtering on existin metrics before computing.
- __Work-arounds__: use goals to filter metrics then pull goal metrics for computing. 
__EXAMPLE:__ (magazine) gift subscription conversion rate = gift subscription confirmation pageviews / gift subscription (starting page) pageviews. Both subscription pageviews and confirmation pageviews are pageviews __filtered__ by certain URLs/URL patterns. see the following goal setting:

![GA_graph4](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph4.png)

![GA_graph5](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph5.png)


The use these goal metrics in Calculated Metric configuration Formula to calcualte conversion rate. The complete formula is `{{GIFT Subscription Confirm (Goal 6 Completions)}} / {{GIFT Subscription (funnel 1) (Goal 4 Completions)}}`. 

![GA_graph6](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/GA_graph6.png)




