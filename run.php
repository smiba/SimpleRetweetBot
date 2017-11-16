<?php

// Quick 'n simple PHP Twitter Retweet bot script by Smiba
// Run every x seconds in cron, the faster the more often it will check for retweets (Remember the rate limit, don't run it too often!)
// https://github.com/smiba/SimpleRetweetBot

error_reporting(E_ERROR); //Error only, you don't want silly warnings (for example, empty array due no tweets) to come up
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
);

$searchstring = str_replace(" ", "+", "smiba rocks"); //Retweet every tweet containing "Smiba" or "Rocks"

$LastID = file_get_contents('lastid'); //Get the last tweet we've retweeted

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = "?q=%22$searchstring%22+-RT&since_id=$LastID&result_type=recent&count=1"; //q, Search query - since_id, limits search to only new tweets - result_type, all tweets newst first - count, only one tweet
#echo $getfield;
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$jsonoutput = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$jsonoutput = json_decode($jsonoutput, true);

/**  GET TWEET ID  **/

$tweetid = $jsonoutput['statuses'][0]['id'];

if (empty($tweetid)) { die(); }

file_put_contents('lastid', $tweetid); //Write down the last tweet we saved so we do not interact with it on every run

if ($jsonoutput['statuses'][0]['user']['name'] == "BannedUser123") { die(); } //Quickly written examply of blocking a user from being retweeted

/** Retweet ! **/

$url = "https://api.twitter.com/1.1/statuses/retweet/$tweetid.json";
$requestMethod = 'POST';
$postfields = array();

$twitter = new TwitterAPIExchange($settings);
$twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();
