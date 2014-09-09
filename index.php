<html>
<title>InstanceRadio</title>
<head>
<link rel="stylesheet" type="text/css" href="s/css/semantic.min.css">
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>
<script src="http://www.youtube.com/player_api"></script>
<script type="text/javascript">
	<?php
	include 'config.php';
	$all_array = array();
	
	$PerfectElectroMusic = getVideosFromPlaylistV3($key, "UUtCcPJl-cIG-mRiIyg-PKsQ");
	$LDM = getVideosFromPlaylistV3($key, "UUDl6xIISC4tm38lzmcHvDiQ");
	$xKito = getVideosFromPlaylistV3($key, "UUMOgdURr7d8pOVlc-alkfRg");
	$MrSuicideSheep = getVideosFromPlaylistV3($key, "UU5nc_ZtjKW1htCVZVRxlQAQ");
	$NoCopyrightSounds = getVideosFromPlaylistV3($key, "UU_aEa8K-EOJ3D6gOs7HcyNg");
	$MixHound = getVideosFromPlaylistV3($key, "UU_jxnWLGJ2eQK4en3UblKEw");
	$AirwaveMusicTV = getVideosFromPlaylistV3($key, "UUwIgPuUJXuf2nY-nKsEvLOg");
	$Proximity = getVideosFromPlaylistV3($key, "UU3ifTl5zKiCAhHIBQYcaTeg");
	$WhySoDank = getVideosFromPlaylistV3($key, "UUjE1fSNLI_RzC8-ZjkqHH9Q");
	$Liquicity = getVideosFromPlaylistV3($key, "UUSXm6c-n6lsjtyjvdD0bFVw");
	$Berzox = getVideosFromPlaylistV3($key, "UUyePQ8y0eJQ5E-EuiaE29Xg");
	$Monstafluff = getVideosFromPlaylistV3($key, "UUNqFDjYTexJDET3rPDrmJKg");
	$MajesticCastle = getVideosFromPlaylistV3($key, "UUXIyz409s7bNWVcM-vjfdVA");
	$GalaxyMusic = getVideosFromPlaylistV3($key, "UUIKF1msqN7lW9gplsifOPkQ");
	$Fluidfied = getVideosFromPlaylistV3($key, "UUTPjZ7UC8NgcZI8UKzb3rLw");

	$all_array = array_merge($PerfectElectroMusic, $LDM, $xKito, $MrSuicideSheep, $NoCopyrightSounds, $MixHound, $AirwaveMusicTV, $Proximity, $WhySoDank, $Liquicity, $Berzox, $Monstafluff, $MajesticCastle, $GalaxyMusic, $Fluidfied);
	shuffle($all_array);
	
	function getSSLPageContents($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, "instancedev.com");

		curl_setopt( $c, CURLOPT_TIMEOUT, 1 );

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	// uses google api v3
	function getVideosFromPlaylistV3($key, $str){
		$contents = getSSLPageContents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=".$str."&key=".$key);
		$lastvideoPos = 0;
		$lasttitlePos =0;
		$videos = array();

		$i = 0;
		
		while (($lastvideoPos = strpos($contents, '"videoId": "', $lastvideoPos)) !== false) {
			$pos = $lastvideoPos + strlen('"videoId": "');
			$lasttitlePos = strpos($contents, '"title": "', $lasttitlePos) + strlen('"title": "');
			$video = substr($contents, $pos,  strpos($contents, '"', $pos + 1) - $pos);
			$title = substr($contents, $lasttitlePos,  strpos($contents, '"', $lasttitlePos + 1) - $lasttitlePos);
			$videos[$i] = array($video, $title);
			$i++;
			$lastvideoPos = $pos;
		}
		
		return $videos;
	}
	
	// google api v2
	function getVideosFromPlaylist($str){
		$contents = file_get_contents("http://gdata.youtube.com/feeds/api/playlists/".$str."max-results=50");
		$lastvideoPos = strpos($contents, "<link rel='alternate' type='text/html'") + 1;
		$lasttitlePos = strpos($contents, "<title type='text'>") + 1;
		$videos = array();

		$i = 0;
		
		while (($lastvideoPos = strpos($contents, "<link rel='alternate' type='text/html' href='http://www.youtube.com/watch?v=", $lastvideoPos)) !== false) {
			$pos = $lastvideoPos + strlen("<link rel='alternate' type='text/html' href='http://www.youtube.com/watch?v=");
			$lasttitlePos = strpos($contents, "<title type='text'>", $lasttitlePos) + strlen("<title type='text'>");
			$video = substr($contents, $pos,  strpos($contents, "&", $pos + 1) - $pos);
			$title = substr($contents, $lasttitlePos,  strpos($contents, "</", $lasttitlePos) - $lasttitlePos);
			$videos[$i] = array($video, $title);
			//array_push($videos, $video);
			$i++;
			$lastvideoPos = $pos;
		}	
		
		return $videos;
	}
	
	?>

	var ids = <?php echo json_encode($all_array);?>;
	var currentid = 0;
	
	var player;
	var interval;
	
	$(document).ready(function(){
		$("#pause").click(function(){
			player.pauseVideo();
		});
		$("#play").click(function(){
			player.playVideo();
		});
		$("#next").click(function(){
			playNext();
		});
	});

	function playNext(){
		clearInterval(interval);
		//$('#yt').attr('src', "https://www.youtube.com/embed/" + ids[currentid] + "?autoplay=1&controls=1&rel=0&showinfo=0&autohide=1&iv_load_policy=3");
		player.loadVideoById(ids[currentid][0]);
		document.title = ids[currentid][1];
		interval = setInterval(calcProgress, 500); //check status
		currentid++;
	}
	
	function calcProgress(){
		var t = player.getCurrentTime();
		var d = player.getDuration();
		$('.bar').css("width", Math.round(t / d * 100) + "%");
	}

	function onYouTubePlayerAPIReady() {
		player = new YT.Player('yt', {
			height: window.innerHeight,
			width: window.innerWidth,
			videoId: 'JKL6ZhObBQs',
			playerVars: {
				autoplay: 1,
				controls: 0,
				rel: 0,
				showinfo: 0,
				autohide: 1,
				iv_load_policy: 3
			},
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange,
				'onError': onError
			}
		});
	}

	function onPlayerReady(event) {
		playNext();
	}

	function onPlayerStateChange(event) {      
		if(event.data === 0) {          
			playNext();
		}
	}
	
	function onError(event){
		playNext();
	}
	
	
</script>
</head>
<body>

<center>
<div class="ui basic icon buttons">
  <div id="pause" class="ui button">
    <i class="pause icon"></i>
  </div>
  <div id="play" class="ui button">
    <i class="play icon"></i>
  </div>
  <div id="next" class="ui button">
    <i class="step forward icon"></i>
  </div>
</div>
<div class="ui basic progress" style='max-width: 200px; background-color: rgba(0, 0, 0, 0.2)!important'>
  <div class="bar"></div>
</div>
</center>
<div id='yt' style='position: absolute; top: 0px; left: -1px; z-index: -1'>
</div>


</body>
</html>
