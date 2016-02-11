### Basic stuff
- Each website has its own account and container tag. (one account can have more than one container, with separate container tag.)
- To find the tag code, go to admin -> container -> install google tag manager.
-  account user permission and container permission are different. container user manager sets whether a use can pushlish tag. 
-  Tags, data layer variables, triggers need to be published to push the code to live site. 
-  Tag manager does version control for you. do use if something gets screwed up. 
-  __VERY IMPORTANT__: because of our website dev history, we don't use tag manager to track pageviews and other events where standard GA tag tracks. We are only using tag manager for custom events/tracking. There is a built-in tag in tag manager which acts as replacement for GA tag - DO NOT activate! (unless all embeded GA tags are removed from the site.)

### How it works
- __Events__: event listener listening to clicks `gtm.click`, link clicks `gtm.linkClick`, time intervals (between events) `gtm.timer`, form submits `gtm.formSubmit` and other custom events, set by *custom html tag. 
- when event happens, tag manager broadcasts values associate that specific event to __data layer variables__.

![graph1](https://github.com/noelleli/documentation/blob/master/GTM/graphs/graph1.png)

- __Data layer variables__:  tag manager comes with a set of built-in variables. we can also create custom variables using **custom script.
- __Triggers__ : a firing rule combining event and data layer variables. Since event listeners log all events, we need to use rule to condition event that  matters by specifying variable values. 


![graph2](https://github.com/noelleli/documentation/blob/master/GTM/graphs/graph2.png)
 
![graph3](https://github.com/noelleli/documentation/blob/master/GTM/graphs/graph3.png)

This rule doesn't have specific variable values because the event is a custom event set by a custom html tag where the script already does the job of filtering.
- __Tracking tags__: once the trigger (rule) is set, we need to set tracking tag to push data over to Google Analytics, Google AdWords, or other data receiving properties.
- Tags __always__ need a trigger (firing rule) to fire. If a tag needs to be fire 100% of the time, set the trigger as `gtm.load` or All Pages. Same rule, you can remove trigger in the tag setting to disable a tag without deleting it.
- __Different type of tags__ (that we are using)
  - Universal Analytics: pushes data to GA when fire. 
  - AdWords Remarketing and Conversion: pushes data to AdWords for Remarketing lists (and covnersion lists).
  - Custom HTML: 1) creates custom events, see example 1; 2) working around the entire event listener -> tigger rules -> tags work flow, directly fires and pushes data to web tracking properties. #2 needs to work closely with the data receiving property APIs, see example 2.

*Custom html tag example 1:
```javascript
<script type="text/javascript">
var prodArray = [];
var prodSum = document.getElementsByClassName("product__info__name");

for (i=0; i < prodSum.length/2; i++){
    var prodName = prodSum[i].innerText;
    prodArray.push(prodName);
}

var prodPrice = document.getElementsByClassName("total-line__price");
var prodTotal = parseFloat(prodPrice[0].innerText.replace(/\$|\,/g, ""));

var google_tag_params = {
	ecomm_prodid: prodArray,
	ecomm_pagetype: 'checkout-1',
	ecomm_totalvalue: prodTotal
};
  
dataLayer.push({
	'event': 'shop_checkout_1_rmkt',
	'google_tag_params': google_tag_params
});
ga('set', 'dimension1', 'checkout-1');
ga('set', 'dimension6', prodArray);
ga('set', 'dimension2', prodTotal);  

</script>
```

*Custom html tag example 2:
```javascript
<script>
jQuery(document).ready(function($){
    $('.div-gpt-ad-664089004995786621-1').iframeTracker({
        blurCallback: function(){
            ga('send','event','Ad Clicks','Top Banner',location.pathname);
        }
    });
	$('.div-gpt-ad-664089004995786621-4').iframeTracker({
	blurCallback: function(){
		ga('send','event','Ad Clicks','Bottom Banner',location.pathname);
	}
    });
});

</script>
```
 
 **Custom javascript setting variable:
```javascript
// Return "true" if there is at least one Youtube video on the page
function () {
    for (var e = document.getElementsByTagName('iframe'), x = e.length; x--;)
        if (/youtube.com\/embed/.test(e[x].src)) return true;
    return false;
}
```
This data lay variable (however you name it) will have value = true when a page has youtube video embedded. 

### Other resources:
- [some tutorial videos from Google](https://analyticsacademy.withgoogle.com/course05)
- [some tutorial texts from Google](https://support.google.com/tagmanager/answer/6106716?hl=en&ref_topic=6333310)
