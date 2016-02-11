## Folders explained:
- adsense: includes adsense api python library. /adsense_make_scripts has scritps related to maker media. 
- dfp: includes dfp python3 library. 
- ga: Google Analytics api php library and scripts. GA8515Boards.php is written for running digikey click report. It loops through each link in `boards/boardpages.csv` for each of its total clicks, and dumps out a file. 
- gl: Intacct 2.1 version api scripts
- gl_projects: Intacct 3.0 version api scripts. Only 3.0 verison api calls can get project level reports.
- marketing: Avanlink script and fb fan page stats scripts
- googleleads: google adwords api php library. /mm_adwords_scripts has maker media related scripts.
- shed: makershed test script - it was never used because RJ has connector. And this script is missing database name for insetion.


## Quick notes on api and api scripts:
- The config.php file stays offline, because it has all kpi keys.
- All Google product api access need to go thorugh [OAuth2](https://developers.google.com/identity/protocols/OAuth2). [Google AdWords api](https://developers.google.com/adwords/api/docs/signingup) needs to submit a written project plan, then go through OAuth2. AdWords also only provide SOAP api.
- Facebook api tokens are linked to noelle@makermedia.com. [Obtaining facebook tokens](https://developers.facebook.com/docs/facebook-login/access-tokens#extending) is a very complicated and very confussing process. Plus they usually give out short live tokens (2 months). [Read this](http://stackoverflow.com/questions/17197970/facebook-permanent-page-access-token) to get long live token. __noelle.@.makermedia.com [facebook](https://www.facebook.com/profile.php?id=100008728475706) profile is a fake profile.__ As long as this fb profile is an admin of a fan page, you will be able to get fan page api access with the long live token. Use it to save yourself time trying to get new facebook api tokens.
- Getting a new dfp is not easy either. Since our dfp account is under another user's account, we need to have that user (Rainy) to give whatever new requesting account both admin and developer permission.
- AvantLink and Maker Shed api keys do not link with any email (99% sure).
- Intacct api key doesn't link with any profile. However, it does require user login and password to get access. Intacct requires changing password every 3 months so make sure to update the config file. And the user used in api calls also needs to have devleoper permissions.
