<?php
require_once( 'keys.php');
/*
$method = 'GET';
$path = '/1.1/statuses/user_timeline.json'; // api call path

$query = array( // query parameters
    'screen_name' => 'LaurierNews',
    'count' => '10'
);
*/

function add_quotes($str) { return '"'.$str.'"'; }

function getTwitterData($path, $query, $method){


$ConsumerKey = ConsumerKey;
$ConsumerSecret = ConsumerSecret; //not shared
$AccessToken = AccessToken;
$AccessTokenSecret = AccessTokenSecret; 	//not shared

$host = 'api.twitter.com';


$oauth = array(
    'oauth_consumer_key' => $ConsumerKey,
    'oauth_token' => $AccessToken,
    'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
    'oauth_timestamp' => time(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_version' => '1.0'
);

$oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
$query = array_map("rawurlencode", $query);

$arr = array_merge($oauth, $query); // combine the values THEN sort

asort($arr); // secondary sort (value)
ksort($arr); // primary sort (key)

// http_build_query automatically encodes, but our parameters
// are already encoded, and must be by this point, so we undo
// the encoding step
$querystring = urldecode(http_build_query($arr, '', '&'));

$url = "https://$host$path";

// mash everything together for the text to hash
$base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

// same with the key
$key = rawurlencode($ConsumerSecret)."&".rawurlencode($AccessTokenSecret);

// generate the hash
$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

// this time we're using a normal GET query, and we're only encoding the query params
// (without the oauth params)
$url .= "?".http_build_query($query);
$url=str_replace("&amp;","&",$url); //fix stuff

$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
ksort($oauth); // probably not necessary, but twitter's demo does it

// also not necessary, but twitter's demo does this too

$oauth = array_map("add_quotes", $oauth);

// this is the full value of the Authorization line
$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

// if you're doing post, you need to skip the GET building above
// and instead supply query parameters to CURLOPT_POSTFIELDS
$options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
                  //CURLOPT_POSTFIELDS => $postfields,
                  CURLOPT_HEADER => false,
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_SSL_VERIFYPEER => false);

// do our business
$feed = curl_init();
curl_setopt_array($feed, $options);
$json = curl_exec($feed);
curl_close($feed);

return json_decode($json);
}



function fixText( $str ){
	$str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
	
	$words = explode(" ",$str );
	
	for($w = 0; $w < sizeof($words); $w++){
		if( strpos($words[$w], '@') ===0){
			$words[$w] = '<a class="twitterUser" target="_blank" href="https://twitter.com/'.substr($words[$w],1).'"><span>@</span>'.substr($words[$w],1).'</a>';
		}
		
		if( strpos($words[$w], '#') ===0){
			$words[$w] = '<a class="twitterHashTag" target="_blank" href="https://twitter.com/hashtag/'.substr($words[$w],1).'?src=hash"><span>#</span>'.substr($words[$w],1).'</a>';
		}
		
		if( strpos($words[$w], 'http://') ===0 || strpos($words[$w], 'https://') ===0 ){
			$words[$w] = '<a class="twitterLink" target="_blank" href="'.$words[$w].'">'.$words[$w].'</a>';
		}
	
	}
	
	//fix amp
	
	$str = implode(" ",$words );
	
	$str = str_replace("&amp;","&",$str);
	
	return $str;
}


function seconds2human($ss) {
		$s = $ss%60;
		$m = floor(($ss%3600)/60);
		$h = floor(($ss%86400)/3600);
		$d = floor(($ss%2592000)/86400);
		
		$str = "";
		if( $d > 0 ){
			$str.= "$d days, ";
		}
		
		if( $h > 0 ){
			$str.= "$h hours, ";
		}
		
		if( $m > 0 ){
			$str.= "$m minutes, ";
		}
		
		if( $s >= 0 ){
			$str.= "$s seconds ";
		}
		
		$str.= "ago...";
		return $str;
}

function fixTime( $t ){
	$now = time();
	$diff = $now - strtotime($t);
	return seconds2human($diff);
}

function renderTwitList($twitter_data){

	if( is_array($twitter_data) ){
		echo "<div class='twitterList'><ul class='tweets'>";
		$count = 0;
		foreach($twitter_data as $td ){
			echo "<li class='tweet ".(++$count%2 ? "odd" : "even")."'>";
			echo '<div class="twitterTweetTime">About <span class="tweetTime">'.fixTime($td->created_at).'</span></div>';
			
			
			echo '<div class="twitterPic"><a target="_blank" href="'.$td->user->url.'"><img class="twitterImage" title="'.$td->user->name.' -  '.$td->user->description.' alt="'.$td->user->name.' - '.$td->user->description.'" src="'.$td->user->profile_image_url.'" /><span class="twitterScreenName">'.$td->user->screen_name.'</span></a></div>';
			echo '<div class="twitterText">'.fixText($td->text).'</div>';
			
			
			
			if( $td->in_reply_to_screen_name != "" ){
				echo '<div class="twitterReplyTo">In <a target="_blank" href="http://twitter.com/'.$td->in_reply_to_screen_name.'/status/'.$td->in_reply_to_status_id_str.'">reply</a> to: <a href="https://twitter.com/'.$td->in_reply_to_screen_name.'">@'.$td->in_reply_to_screen_name.'</a></div>';
			}
			
			echo '<div class="twitterControls">';
				echo '<a class="twitterReply" href="https://twitter.com/intent/tweet?in_reply_to='.$td->id_str.'">'; ?><i class="fa fa-reply"></i> Reply</a>
				<?php echo '<a  class="twitterRetweet" href="https://twitter.com/intent/retweet?tweet_id='.$td->id_str.'">'; ?><i class="fa fa-share"></i> Retweet</a>
				<?php echo '<a  class="twitterFavourite" href="https://twitter.com/intent/favorite?tweet_id='.$td->id_str.'">'; ?><i class="fa fa-star"></i> Favorite</a>
			<?php echo '</div>';
			
			echo '<div class="twitterCounts">';
				echo '<div class="twitterFavCount">Favourite Count: '.$td->favorite_count.'</div>';
				echo '<div class="twitterRetweetCount">ReTweet Count: '.$td->retweet_count.'</div>';
			echo '</div>';
			
			echo "</li>";
		}
		echo "</ul></div>";
	}

}

function renderFollowersList($twitter_data){
	if( is_array($twitter_data->users) ){
		foreach($twitter_data->users as $tu ){
			echo '<div class="twitPicProfListItem"><img src="'.$tu->profile_image_url.'"/><br />'.$tu->name.'('.$tu->screen_name.')'.'</div>';
		}
		echo '<div class="clear"></div>';
	}
}

?>