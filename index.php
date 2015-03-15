<html>
<title>InstanceRadio</title>
<head>
<link rel="stylesheet" type="text/css" href="s/css/semantic.min.css">
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>
<script src="https://www.youtube.com/player_api"></script>
<link rel="stylesheet" type="text/css" href="index.css">
<script type="text/javascript">
	<?php
		include 'config.php';
		$all_array = array();

		$maxResults = 50;

		if(isset($_GET['latestonly']) || isset($_GET['latest'])){
			$maxResults = 10;
		}
		$lowercase_params = array_change_key_case($_GET, CASE_LOWER);
		if(isset($lowercase_params['maxresults'])){
			$maxResults = $lowercase_params['maxresults'];
		}
	?>

	var ids = [];
	var currentid = 0;
	
	var player;
	var interval;
	var playing = false;
	var debug = false; // TODO CHANGE BACK TO false
	
	$(document).ready(function(){
		$("#pause").click(function(){
			if(playing){
				playing = false;
				player.pauseVideo();
				$(this).html('<i class="play icon"></i>');
			}else{
				playing = true;
				player.playVideo();
				$(this).html('<i class="pause icon"></i>');
			}
		});
		$("#next").click(function(){
			playNext();
		});
		
		// Load all latest videos of the following channels
		$.when(getVideosFromPlaylistV3("UUDl6xIISC4tm38lzmcHvDiQ"), // LDM
			getVideosFromPlaylistV3("UUtCcPJl-cIG-mRiIyg-PKsQ"), // PerfectElectroMusic
			getVideosFromPlaylistV3("UUMOgdURr7d8pOVlc-alkfRg"), // xKito
			getVideosFromPlaylistV3("UUE_4AzEG60_GRBg4im9tKHg"), // xKito 2nd!
			getVideosFromPlaylistV3("UU5nc_ZtjKW1htCVZVRxlQAQ"), // MrSuicideSheep
			getVideosFromPlaylistV3("UU_aEa8K-EOJ3D6gOs7HcyNg"), // NoCopyrightSounds
			getVideosFromPlaylistV3("UU_jxnWLGJ2eQK4en3UblKEw"), // MixHound
			getVideosFromPlaylistV3("UUwIgPuUJXuf2nY-nKsEvLOg"), // AirwaveMusicTV
			getVideosFromPlaylistV3("UU3ifTl5zKiCAhHIBQYcaTeg"), // Proximity
			getVideosFromPlaylistV3("UUjE1fSNLI_RzC8-ZjkqHH9Q"), // WhySoDank
			getVideosFromPlaylistV3("UUSXm6c-n6lsjtyjvdD0bFVw"), // Liquicity
			getVideosFromPlaylistV3("UUyePQ8y0eJQ5E-EuiaE29Xg"), // Berzox
			getVideosFromPlaylistV3("UUNqFDjYTexJDET3rPDrmJKg"), // Monstafluff
			getVideosFromPlaylistV3("UUXIyz409s7bNWVcM-vjfdVA"), // MajesticCastle
			getVideosFromPlaylistV3("UUIKF1msqN7lW9gplsifOPkQ"), // GalaxyMusic
			getVideosFromPlaylistV3("UUTPjZ7UC8NgcZI8UKzb3rLw"), // Fluidfied
			getVideosFromPlaylistV3("PL47GfNryB12uTUdaeDxlbBhGZQLVKJvoT"), // Arctic Empire
			getVideosFromPlaylistV3("UUoHJ5m7J27_u96gksCkHnlg"), // eoenetwork)
			getVideosFromPlaylistV3("UU7tD6Ifrwbiy-BoaAHEinmQ"), // Diversity Promotions
			getVideosFromPlaylistV3("UU0XKvSq8CcMBSQTKXZXnEiQ"), // MA Dubstep
			getVideosFromPlaylistV3("UUd3TI79UTgYvVEq5lTnJ4uQ")) // MrRevillz
			.then(allAjaxCallsDone);
	});
	
	function allAjaxCallsDone(){
		var playlist = $(".playlist");
		for(var i in ids){
			playlist.html(playlist.html() + "<div class='playlistitem' id='" + i + "'>" + ids[i].title + "</div>");
		}
		$(".playlistitem").bind("click", function(){
			var id = $(this).attr("id");
			currentid = id > -1 ? id : 0;
			playNext();
		});
		playNext();
	}
		
	function getVideosFromPlaylistV3(str){
		var call = $.ajax({
			url: "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails,snippet&maxResults=<?php echo($maxResults);?>&playlistId=" + str + "&key=<?php echo($key);?>",
		})
		.done(function(data) {
			for (var key in data.items) {
				var itemobj = data.items[key];
				var videoTitle = itemobj["snippet"]["title"];
				var videoId = itemobj["contentDetails"]["videoId"];
				console.log(videoTitle + " " + videoId);
				var node = {title: videoTitle, id: videoId};
				ids.push(node);
			}
			shuffle(ids);
			$("title").html(ids.length);
		});
		return call;
	}
	
	function shuffle(o){
		for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	};

	function playNext(){
		playing = true;
		clearInterval(interval);
		player.loadVideoById(ids[currentid].id);
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
		//playNext();
	}

	function onPlayerStateChange(event) {
		if(debug){
			return;
		}
		if(event.data === 0) {          
			playNext();
		}
		if(player.getVideoData() != null){
			$("title").html(player.getVideoData().title);
		}
	}
	
	function onError(event){
		if(!debug){
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
	<div id="next" class="ui button">
		<i class="step forward icon"></i>
	</div>
</div>
<div class="ui basic progress" style='max-width: 200px; background-color: rgba(0, 0, 0, 0.25)!important'>
	<div class="bar"></div>
</div>
</center>
<div class="playlist">
</div>
<div id='yt' style='position: absolute; top: 0px; left: -1px; z-index: -1'></div>


</body>
</html>
