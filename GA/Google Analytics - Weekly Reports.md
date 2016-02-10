## Quick and easy:
- Have [this page](https://developers.google.com/analytics/devguides/reporting/core/dimsmets) open before.
- If you go through [this entire page](https://developers.google.com/analytics/solutions/google-analytics-spreadsheet-add-on), it will be much easier to understand the weekly reports. 

__If reading these 2 pages takes too much time, you can still get weekly reports by following the instructions below.__

## Weekly basic stats
1. File: [makezine weekly](https://docs.google.com/a/makermedia.com/spreadsheets/d/1dCEGTst0NPtst_9jyjKjY6aymnB1fh1VQWKgDkeZfKI/edit?usp=sharing).
2. __Important!__ For all report files with Google Analytics add-on plug-in, the configuration sheet is always named "Report Configuration" (dah!). Do not change the sheet name, the layout format, or column A. These are the parameters the plug-in looks at for api calls .
3. For column B-E, H-L, change the end data to the Saturday of the week you want to run. Don't change start date rows, which will modify the structure of other sheets. Google Analytics defines a week as Sunday to Saturday. Make sure to select Saturday as end date to get whole week's metrics.
[!w_ga1](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga1.png)
4. For column F-G, modify both start date and end date to one week apart.
[!w_ga2](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga2.png)
5. Go to Add-ons -> Google Analytics -> Run Reports. The reports will run but it will take 10 - 20 sec if the report configurations are complex.  
[!w_ga3](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga3.png)
[!w_ga4](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga4.png)
6. When the report is done running. Go to spreadsheet "ByWeek-Egmt". Copy the last row and paste to a new next row, it will copy the formula and fetch the new weekly metrics just pulled.
[!w_ga5](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga5.png)
7. Traffic by Type: Go to sheet "Makezine.com-Types", copy row 3 values (column B-I) and (right click/control+click ) paste value to the new week row. Using directly paste will end up with same numbers for every week.
[!w_ga6](https://github.com/noelleli/documentation/blob/master/GA/ga_graphs/W_ga6.png)
8. See "Blogs Pageviews", "Homepage", "Tag", "Project Pageviews" for categroy pageviews by week.

## Other useful and quick-to-run stats
1. [SiteSearch Stats and cross site referrals](https://docs.google.com/a/makermedia.com/spreadsheets/d/1CLD5rxFmec6JuO8T11Ux0yPVFrQbAkXsAYP3MQwnUr0/edit?usp=sharing): update the end date to get the latest week. Go to Add-ons -> Google Analytics -> Run Reports to run the reports. Sheet "cross-site" and "SiteSearch" are already set to get the relevant metrics and formula, all there is to do is copying last row and pasting to new row(s) to update new weekly metrics.
2. [Referral Weekly](https://docs.google.com/a/makermedia.com/spreadsheets/d/104eZdYM7C_h3J0oPQBGlkjRltronfRM2Gmq4LJa13G0/edit?usp=sharing): update end date in "Report Confiuration". Run report and go to "Total" sheet, update new row from the last row using copy and paste. 
3. [Makezine Monthly](https://docs.google.com/a/makermedia.com/spreadsheets/d/1p_z-Q9HLv2JSxpmXCz80oolWqDcI6vDN72gP2di9idU/edit?usp=sharing): same process as the weekly file. (update column B-E end date, but update both start and end dates for column F-G.)


