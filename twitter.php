<?php

/* 

This is a demo for how to pull tweets from twitter's api in php.
This is the bare minimum code you need to get the job done.

This demo is the opposite of robust.  
I imagine this fails under many edge cases, although I actually haven't found them yet.
This is stripped down, barebones version of this twitter app only authentication oauth demo:
https://github.com/jonhurlock/Twitter-Application-Only-Authentication-OAuth-PHP

A more robust twitter php oauth library that works well is:
https://github.com/abraham/twitteroauth

*/ 

// put your api keys & twitter search query here
// for api keys, go to https://apps.twitter.com/app/new
$apiKey = 'yourApiKeyHere';
$apiSecret = 'yourApiSecretKeyHere';
// for twitter search query syntax see "Query Operators" on https://dev.twitter.com/rest/public/search
$twitterQuery = 'from:poolsharkmark';


// step 1: get twitter oauth token
$oauthToken = '';
$curlHeader = array( "Authorization: Basic " . base64_encode(urlencode($apiKey) . ':' . urlencode($apiSecret)) ); 
$oauthCurl = curl_init();
curl_setopt($oauthCurl, CURLOPT_URL,'https://api.twitter.com/oauth2/token');
curl_setopt($oauthCurl, CURLOPT_HTTPHEADER, $curlHeader);
curl_setopt($oauthCurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($oauthCurl, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
$oauthObject = curl_exec ($oauthCurl);
curl_close($oauthCurl); 
foreach( explode("\n", $oauthObject) as $line) {
	$oauthToken = json_decode($line)->{'access_token'};
}

// step 2: get tweets (in an array)
$tweetsArray = array();
$twitterHeader = array( "Authorization: Bearer " . $oauthToken );
$tweetsCurl = curl_init();
curl_setopt($tweetsCurl, CURLOPT_URL,'https://api.twitter.com/1.1/search/tweets.json'. '?q=' . urlencode(trim($twitterQuery)) );
curl_setopt($tweetsCurl, CURLOPT_HTTPHEADER, $twitterHeader); 
curl_setopt($tweetsCurl, CURLOPT_RETURNTRANSFER, true); 
$tweetsObject = curl_exec ($tweetsCurl); 
curl_close($tweetsCurl); 
foreach( json_decode($tweetsObject)->{'statuses'} as $tweetObject) {
	$rawTweetText = $tweetObject->{'text'};
	$rawTweetDate = $tweetObject->{'created_at'};
	// $tweet = $rawTweetText . ' ' . $rawTweetDate; // to use unformatted tweet, uncomment this line & comment below line
	$tweet = formatTweet($rawTweetText,$rawTweetDate); // to use formatted tweet, uncomment this line & comment above line
	array_push($tweetsArray, $tweet);
}

// optional tweet formatting function
// you can change how you want to format your tweet.
// here i add some spans with bootstrap classes,
// make urls & usernames into links, and then tweak the date format
function formatTweet($rawTweetText,$rawTweetDate) { 
	$tweetString = '<p class="col-sm-10 text">' . $rawTweetText . '</p>';
	$tweetString = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a target="_blank" href="$1">$1</a>', $tweetString);
	$tweetString = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $tweetString);
	$tweetString .= ' <p class="date col-sm-2">' . date("n/j/y g:ia", strtotime($rawTweetDate)) . '</p>'; 
	return $tweetString;
}

?>