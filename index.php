<?php
require_once('twitterAPI.php');
ob_clean(); 

function pa($arr){
	echo '<pre>'.print_r($arr,true).'</pre>';
}

?>
<html>
<head>
	<title>Twitter Test</title>
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<style type="text/css">
		.twitterList{
			width: 400px;
			margin: 10px auto;
		}
		
		.twitterList {
			font-size: 11px;
		}
		
			ul.tweets{
				list-style: none;
				padding: 0px;
				margin: 0px;
			}
				ul.tweets li.tweet:first-child{
					border-top: 1px solid #ccc;
					-webkit-border-top-left-radius: 10px;
					-webkit-border-top-right-radius: 10px;
					-moz-border-radius-topleft: 10px;
					-moz-border-radius-topright: 10px;
					border-top-left-radius: 10px;
					border-top-right-radius: 10px;
				}
				
				ul.tweets li.tweet:last-child{
					-webkit-border-bottom-right-radius: 10px;
					-webkit-border-bottom-left-radius: 10px;
					-moz-border-radius-bottomright: 10px;
					-moz-border-radius-bottomleft: 10px;
					border-bottom-right-radius: 10px;
					border-bottom-left-radius: 10px;
				}
			
				li.tweet{
					padding: 10px 20px;
					border: 1px solid #ccc;
					border-top: 0px;
				}
				li.tweet.odd{
					background-color: #EDECF3;
				}
					.twitterTweetTime{
						font-size: 10px;
						font-style: italic;
					}
				
					.twitterPic{
						float: left;
						margin-right: 10px;
						margin-top: 5px;
						margin-bottom: 10px;
					}
						.twitterPic .twitterScreenName{
							display: none;
						}
				
					.twitterText{
						margin-top: 10px;
						font-size: 14px;
						font-family: verdana;
					}
					
					.twitterList a{
						text-decoration: none;
						color: #330072;
					}
					
					.twitterList a:hover{
						text-decoration: underline;
					}
					
					.twitterReplyTo{
						font-size: 10px;
						padding: 5px 0px;
						text-align: right;
					}
						
						
					div.twitterControls{
						margin: 0 auto;
						width: 100%;
						text-align: center;
						padding: 10px 0px;
					}
						div.twitterControls a{
							margin: 0px 20px;
						}
		
		
					.twitterCounts{
						text-align: right;
					}
					
					.twitterCounts > div{
						display: inline-block;
						margin: 0px 3px;
					}
					
					a.twitterRetweet:hover, a.twitterReply:hover, a.twitterFavourite:hover{ text-decoration: none; }
					a.twitterRetweet i, a.twitterReply i,  a.twitterFavourite i{
						transition: .3s;
					}
					
					.twitterControls a.twitterFavourite:hover i{
						color: #ffab00;
					}
					
					.twitterControls a.twitterRetweet:hover i{
						color: #a9b946;
					}
					
					.twitterControls a.twitterReply:hover i{
						color: #ff2335;
					}
					
					a.twitterHashTag{
					
					}
					
					.twitterCounts div{
						font-size: 9px;
					}
					
					
					
					.twitPicProfListItem{ float: left; margin: 10px; }
					.clear{ clear: both; }
	</style>
	</head>
<body>
<h3>LaurierNews Time line</h3>
<?php
$method = 'GET';
$path = '/1.1/statuses/user_timeline.json'; // api call path
$query = array( // query parameters
    'screen_name' => 'LaurierNews',
    'count' => '3'
);
$twitter_data = getTwitterData($path, $query, $method);
renderTwitList( $twitter_data );
?>

<h3>My twitter feed</h3>
<?php
$path = "/1.1/statuses/home_timeline.json";
$query = array( // query parameters
    //'screen_name' => 'bren1818',
    'count' => '3'
);
$twitter_data = getTwitterData($path, $query, $method);
renderTwitList( $twitter_data );
?>

<br />
<h3>https://dev.twitter.com/docs/api/1.1/get/followers/list - my followers</h3>
<?php
$path = "/1.1/followers/list.json";
$query = array( // query parameters
    'screen_name' => 'bren1818'
    //'count' => '3'
);
$twitter_data = getTwitterData($path, $query, $method);
//pa( $twitter_data );
renderFollowersList( $twitter_data );
?>
<br />
<h3>https://dev.twitter.com/docs/api/1.1/get/friends/list - my friends</h3>
<?php
$path = "/1.1/friends/list.json";
$query = array( // query parameters
    'screen_name' => 'bren1818'
    //'count' => '3'
);
$twitter_data = getTwitterData($path, $query, $method);
//pa( $twitter_data );
renderFollowersList( $twitter_data );
?>


</body>
</html>