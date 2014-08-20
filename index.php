<html>
<title>InstanceRadio</title>
<head>
<link rel="stylesheet" type="text/css" href="s/css/semantic.min.css">
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>
<script src="http://www.youtube.com/player_api"></script>
<script type="text/javascript">
	<?php
	$all_array = array();
	
	$PerfectElectroMusic = getVideosFromPlaylist("UUtCcPJl-cIG-mRiIyg-PKsQ?max-results=50");
	$LDM = getVideosFromPlaylist("UUDl6xIISC4tm38lzmcHvDiQ?max-results=50");
	$xKito = getVideosFromPlaylist("UUMOgdURr7d8pOVlc-alkfRg?max-results=50");
	$MrSuicideSheep = getVideosFromPlaylist("UU5nc_ZtjKW1htCVZVRxlQAQ?max-results=50");
	
	$all_array = array_merge($PerfectElectroMusic, $LDM, $xKito, $MrSuicideSheep);
	shuffle($all_array);
	
	function getVideosFromPlaylist($str){
		$contents = file_get_contents("http://gdata.youtube.com/feeds/api/playlists/".$str);
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
				'onStateChange': onPlayerStateChange
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
