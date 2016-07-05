<html>
<title>InstanceRadio</title>
<head>
<link rel="stylesheet" type="text/css" href="s/s.min.css">
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>

<script type="text/javascript" src="jquery.cookie-1.4.1.min.js"></script>
<script type="text/javascript" src="s/s.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="https://www.youtube.com/player_api"></script>
<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" type="text/css" href="jquery-ui-1.11.4.custom/jquery-ui.min.css">
<script type="text/javascript">
	<?php
		include 'config.php';
		$all_array = array();

		$maxResults = 5; // max is 50

		if(isset($_GET['latestonly']) || isset($_GET['latest'])){
			$maxResults = 2;
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
	var opt_remove_vid = true;

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
		["MrMoMMusic", "UUJBpeNOjvbn9rRte3w_Kklg"],
		["WaveMusic", "UUbuK8xxu2P_sqoMnDsoBrrg"],
		["NightcoreReality", "UUqX8hO4JWM6IJfEabbZmhUw"],
		["CloudKid", "UUSa8IUd1uEjlREMa21I3ZPQ"],
		["TheLalaTranceGirl", "UUMQBva6MUyidoNmcV8gIV9g"],
		["DubstepGutter", "UUG6QEHCBfWZOnv7UVxappyw"],
		["PenguinMusic", "UU0YSN3ge1paAcKMC_X3ktsw"],
		["UnitedDubstep", "UUVrYrjXtAIgBbVN1i_FiGtw"],
		["OneChilledPanda", "UUkUTBwZKwA9ojYqzj6VRlMQ"],
		["SuicideSheeep", "UULTZddgA_La9H4Ngg99t_QQ"],
		["TrapNation", "UUa10nxShhzNrCE1o2ZOPztg"],
		["TrapGutter", "UUaJdK74vrx8Mk6HlwNk0uEQ"],
		["KoalaKontrol", "UUBYg9_11ErMsFFNR66TRuLA"],
		["JompaMusic", "UU1WKD9pJt5Sa4DCVaoJSAGw"],
		["EpicMusicVN", "PL4adbQCQMmoZNMuDUsddQw4r9XWNdLbbI"],
		["OrionMusicNetwork", "UUdy-3GIGZy2DD65TPg3i1GA"],
		["InverseNetwork", "UUx5rscERi7IpVS9dO_HCl5A"],
		["JED", "UUCCs8U1UsY-KlOePhSrMJdg"],
		["NighTcoreFC", "UU5I3vUh2iNfQ3pCU3sodYRA"],
		["DeadMusicFC", "UUBsKAivSo21NEubmxiLPUWw"],
		["CrazyBass Promotions", "UU8uXrhG0n-i6as8V273Mknw"],
		["BassOneMusic", "UUmyBcA6xsJDuKn_An6wL-EA"],
		["Lustcore", "UUrNlBy9CwV-sHGbRa6mf1GA"],
		["KyraPromotions", "UUqolymr8zonJzC08v2wXNrQ"],
		["RackiePromotions", "UUqPgPXkG6acQomkAewewcNQ"],
		/*["NightcoreGalaxy", "PLHO3r5TU5dB9xtGsbOcwXVpkiPQiTZiDV"], Now EDMGalaxy */
		["NexusNetwork", "UUl4UOc8h1ZnO-inFPgAu7gw"],
		["ReinaXmina", "UUwyU7wNCjTmcrRWws7ZTlXw"],
		["DroneMusic", "UUQgYaMo37r74iLehFx3YsEQ"],
		["SynergyMusic", "UUrbRMQk4-CnFZLmLuz0KyHQ"],
		["KTMMusicRecords", "UUYTNxR2Z4ryz88WRWiaPCdg"],
		["AgeraPromotions", "UU5HQWGZnatHoYpJm-ViQIDQ"],
		["MelodyPromotions", "UUKsCyxmVqLsKDPXeQvdnEUA"],
		["StrobeNetworkRecords", "UUcoYD5HDg8P-gvJU-oDYq5Q"],
		["AvienCloud", "UUKioNqOX_kOCLcSIWPL_lxQ"],
		["LostSoundNetWork", "UUDWRMT6Ym4IpMqjX3EhHOlA"],
		["EDMGalaxy", "PLZclc0JXUCA5ng910IDqr05ZNZkufCEQg"]
	];

	$(document).ready(function(){
		$.ajaxPrefilter(function(opts, originalOpts, jqXHR) {
			var dfd = $.Deferred();
			jqXHR.done(dfd.resolve);

			jqXHR.fail(function() {
				var args = Array.prototype.slice.call(arguments);
				dfd.resolve(args);
			});

			return dfd.promise(jqXHR);
		});
		
		$(".channellistparent").hide(0);
		$('.ui.checkbox').checkbox();
		$('.cookie.nag').nag({key: 'accepts-cookies', value: true});
		
		$(document).keydown(function(e){
			console.log(e.keyCode);
			if (e.keyCode == 179) { 
				togglePauseButton();
			}
		});

		if (document.cookie.indexOf("visited") >= 0) {
			
		} else {
			$.cookie('visited', 'true', { expires: 365 });
		}
		
		var deferreds = [];
		var channellist = $(".channellist");
		if($.cookie("opt_remove_vid") === undefined){
			opt_remove_vid = false;
		}
		channellist.html(channellist.html() + "<div class='channellistitem' id='opt_remove_vid'><div class='ui toggle checkbox'><input type='checkbox' " + (!opt_remove_vid ? "" : "checked='checked'") + " id='opt_remove_vid'><label id='opt_remove_vid'>Remove previous video from queue</label></div></div>");
		for (var i = 0; i < channels.length; i++) {
			var disabled = true;
			if($.cookie(channels[i][0]) === undefined){ // channel not disabled, add to deferreds array
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
		
		$.when.apply($, deferreds).then(allAjaxCallsDone, allAjaxCallsDone); // second argument is on failure

	});
	
	function inNewTabFix()
	{
		if(currentid > 0){
			$("<a>").attr("href", "http://youtube.com/watch?v=" + ids[currentid - 1].id).attr("target", "_blank")[0].click();
		}
	}
	
	function allAjaxCallsDone(){
		var playlist = $(".playlist");
		for (var i in ids) {
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
			channellistvisible ? $(".channellistparent").hide(0) : $(".channellistparent").fadeIn(450);
			channellistvisible = !channellistvisible;
		});
				
		$("#results").slider({
			range: "min",
			min: 1,
			max: 50,
			value: <?php echo($maxResults);?>,
			slide: function(event, ui) {
				updateResCount(ui.value);
			}
		});
		$("#volumeSlider").slider({
			range: "min",
			min: 1,
			max: 100,
			value: 100,
			slide: function(event, ui) {
				player.setVolume(ui.value);
				$("#volume").html(ui.value);
			}
		});
		$("#results").css("margin", "0 5 0 5");
		$("#volumeSlider").css("margin", "0 5 0 5");
		playNext();
	}

	function togglePauseButton(btn){
		if (playing) {
			playing = false;
			player.pauseVideo();
			setPlayButtonValue("play");
		} else {
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
			url: /*"requests.php?url=" + encodeURIComponent(*/"https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails,snippet&maxResults=<?php echo($maxResults);?>&playlistId=" + str + "&key=<?php echo($key); ?>"/*)*/,
			dataType: "json"
		})
		.done(function(data) {
			for (var key in data.items) {
				var itemobj = data.items[key];
				var videoTitle = itemobj["snippet"]["title"];
				var videoId = itemobj["contentDetails"]["videoId"];
				if (debug) {
					console.log(videoTitle + " " + videoId);
				}
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
	}

	function playNext(){
		playing = true;
		setPlayButtonValue("pause"); // Show pause button as the music is playing right now
		clearInterval(interval);
		if(ids.length <= currentid){
			currentid = 0;
		}
		player.loadVideoById(ids[currentid].id);
		
		if(currentid - 1 > -1){
			$("#" + (currentid - 1)).removeClass("darkitem");
		}
		$("#" + lastid).removeClass("darkitem");
		$("#" + currentid).addClass("darkitem");
		if(opt_remove_vid && currentid > 0){
			console.log($("#" + (currentid - 1)).html());
			$("#" + (currentid - 1)).remove();
			console.log($("#" + lastid).html());
		}
		
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
			//videoId: 'JKL6ZhObBQs',
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
	
	var resCountVal = <?php echo($maxResults);?>;
	function updateResCount(value){
		resCountVal = value;
		$("#resCount").html(value);
	}
	
	function reloadPage(){
		var url = window.location.href;    
		if (url.indexOf("?") > -1) {
			url = url.substring(0, url.indexOf("?")) + "?maxResults=" + resCountVal;
		} else {
			url += "?maxResults=" + resCountVal;
		}
		window.location.href = url;	
	}
	
	
</script>
</head>
<body>

<div class="channellistparent">
	<div class="channellist">
		<div class="controls">
			<div id="next" class="ui compact button" onclick="reloadPage()">Reload</div>
			<div id="resultsOption">Results per channel: <div id="resCount"><?php echo($maxResults);?></div><div class="slider" id="results"></div></div>
			<br>
			Volume: <div id="volume">100</div>
			<div id="volumeSlider"></div>
			<br>
			<!-- Channels get added here -->
		</div>
	</div>
</div>

<div class="playlistparent">
	<div class="controls playercontrols">
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
			<div id="youtubebtn" onclick="inNewTabFix();" class="ui compact button">
				<i class="youtube icon"></i>
			</div>
			<div>
				<!-- placeholder -->
			</div>
		</div>
	</div>
	<div class="playlist">
		
	</div>
</div>
<div id='yt' style='position: absolute; top: 0px; left: 0px; z-index: -1; width: 100%; height: 100%'></div>

<div class="ui inline cookie nag">
	<span class="title">
	Cookies are used to save your channel preferences.
	</span>
	<i class="close icon"></i>
</div>

</body>
</html>
