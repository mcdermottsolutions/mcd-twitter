This is a demo for how to pull tweets from twitter's api in php.
This is the bare minimum code you need to get the job done. 

This demo is the opposite of robust. I imagine this fails under many edge cases.
This is stripped down, barebones version of this twitter app only authentication oauth demo:
https://github.com/jonhurlock/Twitter-Application-Only-Authentication-OAuth-PHP

A more robust twitter php oauth library that works well is:
https://github.com/abraham/twitteroauth

steps to get my demo working:

1) go to https://apps.twitter.com/app/new, make a new app, and get your api keys

2) add your api key & secret api key on line 20 & 21 of twitter.php

3) if you want to chang the twitter feed pulled, see "Query Operators" on 
   https://dev.twitter.com/rest/public/search and change the query on line 23 of twitter.php

4) upload to your server.  (i haven't been able to get this to work from localhost yet)

5) go to the live index.php and you should see the twitter feed