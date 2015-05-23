<html>
<title>InstanceRadio</title>
<head>
<link rel="stylesheet" type="text/css" href="s/s.min.css">
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="jquery.cookie-1.4.1.min.js"></script>
<script type="text/javascript" src="s/s.min.js"></script>
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
	var lastid = 0;
	var currentid = 0;

	var player;
	var interval;
	var playing = false;
	var debug = false; // TODO CHANGE BACK TO false
 	var channellistvisible = false;

	var channels = [
		["LDM", "UUDl6xIISC4tm38lzmcHvDiQ"],
		["PerfectElectroMusic", "UUtCcPJl-cIG-mRiIyg-PKsQ"],
		["xKito", "UUMOgdURr7d8pOVlc-alkfRg"],
		["xKito2", "UUE_4AzEG60_GRBg4im9tKHg"],
		["MrSuicideSheep", "UU5nc_ZtjKW1htCVZVRxlQAQ"],
		["NoCopyrightSounds", "UU_aEa8K-EOJ3D6gOs7HcyNg"],
		["MixHound", "UU_jxnWLGJ2eQK4en3UblKEw"],
		["AirwaveMusicTV", "UUwIgPuUJXuf2nY-nKsEvLOg"],
		["Proximity", "UU3ifTl5zKiCAhHIBQYcaTeg"],
		["WhySoDank", "UUjE1fSNLI_RzC8-ZjkqHH9Q"],
		["Liquicity", "UUSXm6c-n6lsjtyjvdD0bFVw"],
		["Berzox", "UUyePQ8y0eJQ5E-EuiaE29Xg"],
		["Monstafluff", "UUNqFDjYTexJDET3rPDrmJKg"],
		["MajesticCastle", "UUXIyz409s7bNWVcM-vjfdVA"],
		["GalaxyMusic", "UUIKF1msqN7lW9gplsifOPkQ"],
		["Fluidfied", "UUTPjZ7UC8NgcZI8UKzb3rLw"],
		["ArcticEmpire", "PL47GfNryB12uTUdaeDxlbBhGZQLVKJvoT"],
		["eoenetwork", "UUoHJ5m7J27_u96gksCkHnlg"],
		["DiversityPromotions", "UU7tD6Ifrwbiy-BoaAHEinmQ"],
		["MAMusic", "UU0XKvSq8CcMBSQTKXZXnEiQ"],
		["MrRevillz", "UUd3TI79UTgYvVEq5lTnJ4uQ"],
		["VarietyMusic", "UUkFKSmbFIVQ1xY6j9vJlCcA"],
		["Clown", "UUT4e_djPUZOkOLTZzTtnxUQ"],
		["Niiiiiiiiiiii", "UUmsh_oOrl1hby7P1ZUx5Yfw"],
		["MrMoMMusic", "UUJBpeNOjvbn9rRte3w_Kklg"]
	];

	$(document).ready(function(){
		$(".channellist").hide(0);
		$('.ui.checkbox').checkbox();
		
		if (document.cookie.indexOf("visited") >= 0) {
			
		} else {
			$.cookie('visited', 'true', { expires: 365 });
		}
		
		var deferreds = [];
		var channellist = $(".channellist");
		for (var i = 0; i < channels.length; i++) {
			var disabled = true;
			if($.cookie(channels[i][0]) === undefined){ // channel not disabled, add to deferreds arrays
				deferreds.push(getVideosFromPlaylistV3(channels[i][1]));
				disabled = false;
			}
			channellist.html(channellist.html() + "<div class='channellistitem' id='" + channels[i][0] + "'><div class='ui toggle checkbox'><input type='checkbox' " + (disabled ? "" : "checked='checked'") + " id='" + channels[i][0] + "'><label id='" + channels[i][0] + "'>" + channels[i][0] + "</label></div></div>");
		}
		
		$('.ui.checkbox').checkbox('setting', 'onChange', function() {
			var id = $(this).attr("id"); // id is the channel
			if($.cookie(id) === undefined){
				// channel not disabled -> disable
				$.cookie(id, 'true', { expires: 365 });
			} else {
				$.removeCookie(id);
			}
		});
		
		$.when.apply($, deferreds).then(allAjaxCallsDone);

	});
	
	function allAjaxCallsDone(){
		var playlist = $(".playlist");
		for(var i in ids){
			playlist.html(playlist.html() + "<div class='playlistitem' id='" + i + "'>" + ids[i].title + "</div>");
		}
		$(".playlistitem").bind("click", function(){
			var id = $(this).attr("id");
			lastid = currentid - 1;
			currentid = id > -1 ? id : 0;
			playNext();
		});
		$("#pause").removeClass("loading");
		$("#pause").bind("click", function(){
			togglePauseButton($(this));
		});
		$("#nextbtn").bind("click", function(){
			playNext();
		});
		$("#settings").bind("click", function(){
			channellistvisible ? $(".channellist").hide(0) : $(".channellist").fadeIn(450);;
			channellistvisible = !channellistvisible;
		});
		playNext();
	}

	function togglePauseButton(btn){
		if(playing){
			playing = false;
			player.pauseVideo();
			setPlayButtonValue("play");
		}else{
			playing = true;
			player.playVideo();
			setPlayButtonValue("pause");
		}
	}

	function setPlayButtonValue(val){
		$("#pause").html('<i class="' + val + ' icon"></i>');
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
		setPlayButtonValue("pause"); // Show pause button as the music is playing right now
		clearInterval(interval);
		player.loadVideoById(ids[currentid].id);
		
		if(currentid - 1 > -1){
			$("#" + (currentid - 1)).removeClass("darkitem");
		}
		$("#" + lastid).removeClass("darkitem");
		$("#" + currentid).addClass("darkitem");
		
		interval = setInterval(calcProgress, 500);
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

<div class="channellist">
	<div class="controls"><center><div id="next" class="ui compact button" onclick="document.location.reload();">Reload</div></center></div>
</div>

<div class="playlist">
	<div class="controls">
		<div class="ui icon buttons controlbuttons">
			<div class="ui blue top attached progress">
				<div class="bar" style="min-width: 0px!important"></div>
			</div>
			<div id="pause" class="ui loading compact button">
				<i class="pause icon"></i>
			</div>
			<div id="nextbtn" class="ui compact button">
				<i class="step forward icon"></i>
			</div>
			<div id="settings" class="ui compact button">
				<i class="setting icon"></i>
			</div>
		</div>
	</div>
</div>
<div id='yt' style='position: absolute; top: 0px; left: -1px; z-index: -1'></div>

</body>
</html>
