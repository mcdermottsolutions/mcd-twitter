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

// step 1: put your api keys & twitter search query here
// for api keys, go to https://apps.twitter.com/app/new
$apiKey = 'yourApiKeyHere';
$apiSecret = 'yourApiSecretKeyHere';
// for twitter search query syntax see "Query Operators" on https://dev.twitter.com/rest/public/search
$twitterQuery = 'from:poolsharkmark';
$tweetsArray = array();

// fallback option 1 - error notice - use this for unexpected short term config or api issues
$fallbackTweet = '<p class="col-sm-10 text">Twitter feed temporarily not available</p>';
// fallback option 2 - fake tweet fallback - maybe use this for long term api issues like twitter drastically changes their api and it will take you a long time to adapt your code (grab a real tweet from your timeline & paste it in here)
// $fallbackTweet = '<p class="col-sm-10 text">Ooh facebook has fancy fonts in their console logs. <a target="_blank" href="http://t.co/ZIILxRBHcM">http://t.co/ZIILxRBHcM</a></p> <p class="date col-sm-2">6/3/15 5:45am</p>'; 


$tweetsArray = getTweets($apiKey, $apiSecret,$twitterQuery,$fallbackTweet);


function getTweets($key,$secret,$query,$fallback) {

	$tempTweetsArray = array();

	// step 2: get twitter oauth token
	$oauthToken = '';
	$curlHeader = array( "Authorization: Basic " . base64_encode(urlencode($key) . ':' . urlencode($secret)) ); 
	$oauthCurl = curl_init();
	curl_setopt($oauthCurl, CURLOPT_URL,'https://api.twitter.com/oauth2/token');
	curl_setopt($oauthCurl, CURLOPT_HTTPHEADER, $curlHeader);
	curl_setopt($oauthCurl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($oauthCurl, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
	$oauthObject = curl_exec($oauthCurl);
	if (curl_errno($oauthCurl)) {
	    // $tempTweetsArray[0] = 'oAuth curl call failed:' . curl_error($oauthCurl); // for debugging
	    $tempTweetsArray[0] = $fallback; // for production
	    return $tempTweetsArray;
	} elseif (json_decode($oauthObject)->errors) {
		// $tempTweetsArray[0] = 'Twitter oAuth error: ' . json_decode($oauthObject)->errors{0}->message; // for debugging
		$tempTweetsArray[0] = $fallback; // for production
		return $tempTweetsArray;
	} else {
		foreach( explode("\n", $oauthObject) as $line) {
			$oauthToken = json_decode($line)->{'access_token'};
		}
	}
	curl_close($oauthCurl); 

	// step 3: get tweets (in an array)
	$twitterHeader = array( "Authorization: Bearer " . $oauthToken );
	$tweetsCurl = curl_init();
	curl_setopt($tweetsCurl, CURLOPT_URL,'https://api.twitter.com/1.1/search/tweets.json'. '?q=' . urlencode(trim($query)) );
	curl_setopt($tweetsCurl, CURLOPT_HTTPHEADER, $twitterHeader); 
	curl_setopt($tweetsCurl, CURLOPT_RETURNTRANSFER, true); 
	$tweetsObject = curl_exec ($tweetsCurl); 	
	if (curl_errno($tweetsCurl)) {
	    // $tempTweetsArray[0] = 'Twitter curl call failed:' . curl_error($tweetsCurl); // for debugging
	    $tempTweetsArray[0] = $fallback; // for production
	    return $tempTweetsArray;
	} elseif (json_decode($tweetsObject)->errors) {
		// $tempTweetsArray[0] = 'Twitter API error: ' . json_decode($tweetsObject)->errors{0}->message; // for debugging
		$tempTweetsArray[0] = $fallback; // for production
		return $tempTweetsArray;
	} else {
		foreach( explode("\n", $oauthObject) as $line) {
			$oauthToken = json_decode($line)->{'access_token'};
		}
	}
	foreach( json_decode($tweetsObject)->{'statuses'} as $tweetObject) {
		$rawTweetText = $tweetObject->{'text'};
		$rawTweetDate = $tweetObject->{'created_at'};
		// $tweet = $rawTweetText . ' ' . $rawTweetDate; // to use unformatted tweet, uncomment this line & comment below line
		$tweet = formatTweet($rawTweetText,$rawTweetDate); // to use formatted tweet, uncomment this line & comment above line
		array_push($tempTweetsArray, $tweet);
	}
	curl_close($tweetsCurl); 

	return $tempTweetsArray;

}

// optional step 4 - tweet formatting function
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