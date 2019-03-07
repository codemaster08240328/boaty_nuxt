<!DOCTYPE html>
<html>
  <head>
    <title>SailChecker Racing Game</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
		<meta name="description" content="Do you think you can Sail? Do you think you can Race? Try out our new Sail Racing Challenge Game see if you can beat our CEO.">

		<meta property="og:title" content="SailChecker Racing Game">
		<meta property="og:description" content="Do you think you can Sail? Do you think you can Race? Try out our new Sail Racing Challenge Game see if you can beat our CEO.">
		<meta property="og:image" content="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2013/03/Rolex-Racing.jpg">
		<meta property="og:url" content="https://sailchecker.com/sailchallenge/">
		<meta name="twitter:card" content="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2013/03/Rolex-Racing.jpg">
		<!--  Non-Essential, But Recommended -->

		<meta property="og:site_name" content="SailChecker">
		<meta name="twitter:image:alt" content="Competitive Yacht Racing">


		<!--  Non-Essential, But Required for Analytics -->
		<meta name="twitter:site" content="@SailChecker">


	<link rel="shortcut icon" href="https://sailchecker.com/sailchallenge/favicon.png" type="image/png"/>
    <style>
		/* Always set the map height explicitly to define the size of the div
		* element that contains the map. */
		#map {
			height: 100%;
		}
		/* Optional: Makes the sample page fill the window. */
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}

		#preload-01 { background: url(assets/ff2.png) no-repeat -9999px -9999px; }
		#preload-02 { background: url(assets/ff4.png) no-repeat -9999px -9999px; }
		#preload-03 { background: url(assets/winbanner.png) no-repeat -9999px -9999px; }
		#preload-04 { background: url(assets/losebanner.png) no-repeat -9999px -9999px; }

		@media (min-width:320px)  { 
			/* smartphones, iPhone, portrait 480x320 phones */ 
			#tack {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:236px;
				height:45px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:70px;
				left:0;
				right:0;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/mobile/tack3.png);
				z-index:9999999;
				
				text-align:center;
				cursor:pointer;
			}
			
			#compass {
				position:absolute;
				width:96px;
				height:96px;
				top:65px;
				right:5px;
				background:url(assets/mobile/compass6.png);
				z-index:9999999;
			}
		  
			#arrow {
				position:relative;
				width:15px;
				height:34px;
				margin-left:auto;
				margin-right:auto;
				left:0;
				right:0;
				top:32px;
				background:url(assets/mobile/pin3.png);
				z-index:9999998;
			}

			#windkn {
				position:relative;
				margin-left:auto;
				margin-right:auto;
				padding-top:2px;
				color:#fff;
				left:0;
				right:0;
				top:48px;
				font-size:10px;
				text-align:center;
				width:76px;
				height:40px;
				background:url(assets/mobile/banner3.png);
				z-index:9999999;
			}

			#fdist {
				position:absolute;
				width:100px;
				top:172px;
				right:3px;
				font-size:11px;
				color:#fff;
				text-align:center;
				z-index:9999999;
			}
			
			#ff {
				position:absolute;
				width:45px;
				height:46px;
				top:70px;
				left:10px;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/mobile/forward1.png);
				color:#fff;
				text-align:center;
				z-index:99999999;
				cursor:pointer;
			}
			
			#title {
				position:absolute;
				width:321px;
				height:95px;
				top:0;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/mobile/title.png);
				color:#fff;
				text-align:center;
				z-index:9999999;
				cursor:pointer;
			}
			
			#commentBox {
				position:absolute;
				width:320px;
				max-height: 400px;
				height: 400px;
				top:100px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				z-index:99999999;
				display:none;
				overflow: scroll;
			}

			#instructions {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:45px;
				height:45px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:125px;
				left:261px;
				right:0;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/mobile/instructions.png);
				z-index:9999999;
				font-size:40px;
				text-align:center;
				cursor:pointer;
			}
		  
			#fbshare {
				position:absolute;
				top:120px;
				left:10px;
				z-index:9999999;
				cursor:pointer;
				display:block;
			}
			
			#ins {
				position:absolute;
				top:160px;
				left:10px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/ins.png);
				display:block;
			}
			
			#fb {
				position:absolute;
				top:200px;
				left:10px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/fb.png);
				display:block;
			}
			
			#tw {
				position:absolute;
				top:240px;
				left:10px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/tw.png);
				display:block;
			}
		  
			.fb-comments {
				margin-top:25px;
			}
			
			#mc_embed_signup {
				background:#fff; 
				clear:left; 
				font:8px Helvetica,Arial,sans-serif; 
				position:absolute;
				width:320px;
				top:110px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				display:none;
				z-index:99999999;
			}
			
			#instructionspop {
				position:absolute;
				width:321px;
				height:422px;
				left:0;
				right:0;
				top:0px;
				margin-left:auto;
				margin-right:auto;
				background:url(assets/mobile/instructionspop.jpg);
				cursor:pointer;
				z-index:99999999;
				display:none;
			}
		}
		@media (min-width:481px)  { /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */ }
		@media (min-width:641px)  { 
			/* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */ 
			#tack {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:236px;
				height:45px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:20px;
				left:0;
				right:0;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/mobile/tack3.png);
				z-index:9999999;
				
				text-align:center;
				cursor:pointer;
			}
			
			#compass {
				position:absolute;
				width:96px;
				height:96px;
				top:10px;
				right:10px;
				background:url(assets/mobile/compass6.png);
				z-index:9999999;
			}
		  
			#arrow {
				position:relative;
				width:15px;
				height:34px;
				margin-left:auto;
				margin-right:auto;
				left:0;
				right:0;
				top:30px;
				background:url(assets/mobile/pin3.png);
				z-index:9999998;
			}

			#windkn {
				position:relative;
				margin-left:auto;
				margin-right:auto;
				padding-top:2px;
				color:#fff;
				left:0;
				right:0;
				top:48px;
				font-size:10px;
				text-align:center;
				width:76px;
				height:40px;
				background:url(assets/mobile/banner3.png);
				z-index:9999999;
			}

			#fdist {
				position:absolute;
				width:100px;
				top:116px;
				right:8px;
				font-size:11px;
				color:#fff;
				text-align:center;
				z-index:9999999;
			}
			
			#ff {
				position:absolute;
				width:45px;
				height:46px;
				top:20px;
				left:20px;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/mobile/forward1.png);
				color:#fff;
				text-align:center;
				z-index:9999999;
				cursor:pointer;
			}
			
			#title {
				position:absolute;
				width:321px;
				height:95px;
				top:0;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/mobile/title.png);
				color:#fff;
				text-align:center;
				z-index:9999999;
				cursor:pointer;
			}
			
			#commentBox {
				position:absolute;
				width:320px;
				max-height: 400px;
				height: 400px;
				top:100px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				z-index:99999999;
				display:none;
				overflow: scroll;
			}

			#instructions {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:45px;
				height:45px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:80px;
				left:281px;
				right:0;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/mobile/instructions.png);
				z-index:9999999;
				font-size:40px;
				text-align:center;
				cursor:pointer;
			}
		  
			#fbshare {
				position:absolute;
				top:70px;
				left:20px;
				z-index:9999999;
				cursor:pointer;
				display:block;
			}
			
			#ins {
				position:absolute;
				top:120px;
				left:20px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/ins.png);
				display:block;
			}
			
			#fb {
				position:absolute;
				top:170px;
				left:20px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/fb.png);
				display:block;
			}
			
			#tw {
				position:absolute;
				top:220px;
				left:20px;
				width:30px;
				height:29px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/mobile/tw.png);
				display:block;
			}
		  
			.fb-comments {
				margin-top:25px;
			}
			
			#mc_embed_signup {
				background:#fff; 
				clear:left; 
				font:8px Helvetica,Arial,sans-serif; 
				position:absolute;
				width:320px;
				top:110px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				display:none;
				z-index:99999999;
			}
			
			#instructionspop {
				position:absolute;
				width:321px;
				height:422px;
				left:0;
				right:0;
				top:160px;
				margin-left:auto;
				margin-right:auto;
				background:url(assets/mobile/instructionspop.jpg);
				cursor:pointer;
				z-index:99999999;
				display:none;
			}
		}
		@media (min-width:961px)  { /* tablet, landscape iPad, lo-res laptops ands desktops */ }
		@media (min-width:1025px) { 
			/* big landscape tablets, laptops, and desktops */ 
			#tack {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:446px;
				height:83px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:20px;
				left:0;
				right:0;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/tack3.png);
				z-index:9999999;
				font-size:40px;
				text-align:center;
				cursor:pointer;
			}
			
			#tack div {
				font-size:20px;
			}

			#compass {
				position:absolute;
				width:268px;
				height:270px;
				top:10px;
				right:10px;
				background:url(assets/compass6.png);
				z-index:9999999;
			}
		  
			#arrow {
				position:relative;
				width:38px;
				height:88px;
				margin-left:auto;
				margin-right:auto;
				left:0;
				right:0;
				top:90px;
				background:url(assets/pin3.png);
				z-index:9999998;
			}

			#windkn {
				position:relative;
				margin-left:auto;
				margin-right:auto;
				padding-top:10px;
				color:#fff;
				left:0;
				right:0;
				top:135px;
				font-size:20px;
				text-align:center;
				width:210px;
				height:102px;
				background:url(assets/banner3.png);
				z-index:9999999;
			}

			#fdist {
				position:absolute;
				width:200px;
				top:300px;
				right:45px;
				font-size:24px;
				color:#fff;
				text-align:center;
				z-index:9999999;
			}

			#ff {
				position:absolute;
				width:102px;
				height:104px;
				top:20px;
				left:20px;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/forward1.png);
				color:#fff;
				text-align:center;
				z-index:9999999;
				cursor:pointer;
			}

			#title {
				position:absolute;
				width:753px;
				height:152px;
				top:0;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				font-size:40px;
				/*background:#00293c;*/
				background:url(assets/title.png);
				color:#fff;
				text-align:center;
				z-index:9999999;
				cursor:pointer;
			}

			#commentBox {
				position:absolute;
				width:520px;
				max-height: 400px;
				height: 400px;
				top:100px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				z-index:99999999;
				display:none;
				overflow: scroll;
			}

			#instructions {
				position:absolute;
				/*width:350px;
				height:80px;*/
				width:84px;
				height:85px;
				/*border:1px solid #d5d5d5;*/
				margin-left:auto;
				margin-right:auto;
				bottom:20px;
				left:0;
				right:556px;
				/*padding:10px;*/
				/*background:#f5f5f5;*/
				background:url(assets/instructions.png);
				z-index:9999999;
				font-size:40px;
				text-align:center;
				cursor:pointer;
			}
		  
			#fbshare {
				position:absolute;
				top:30px;
				left:135px;
				z-index:9999999;
				cursor:pointer;
				display:block;
			}
			
			#ins {
				position:absolute;
				top:70px;
				left:130px;
				width:49px;
				height:49px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/ins.png);
				display:block;
			}
			
			#fb {
				position:absolute;
				top:70px;
				left:189px;
				width:49px;
				height:49px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/fb.png);
				display:block;
			}
			
			#tw {
				position:absolute;
				top:70px;
				left:248px;
				width:49px;
				height:49px;
				z-index:9999999;
				cursor:pointer;
				background:url(assets/tw.png);
				display:block;
			}
		  
			.fb-comments {
				margin-top:25px;
			}
			
			#mc_embed_signup {
				background:#fff; 
				clear:left; 
				font:14px Helvetica,Arial,sans-serif; 
				position:absolute;
				width:520px;
				top:170px;
				left:0;
				right:0;
				margin-left:auto;
				margin-right:auto;
				padding:5px;
				border:1px solid #00293c;
				background:#fff;
				display:none;
				z-index:99999999;
			}
			
			#instructionspop {
				position:absolute;
				width:463px;
				height:373px;
				left:0;
				right:0;
				top:160px;
				margin-left:auto;
				margin-right:auto;
				background:url(assets/instructionspop.png);
				cursor:pointer;
				z-index:99999999;
				display:none;
			}
		}
		
		@media (min-width:1281px) { /* hi-res laptops and desktops */ }
    </style>
	<link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
	
  </head>
  <body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=840650692669936";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	
	
    <div id="map"></div>
    <div id="compass">
		<div id="arrow"></div>
		<div id="windkn">0 kn</div>
	</div>
	<div id="fdist"></div>
	
	<div id="commentBox">
		<div class="fb-comments" data-href="https://sailchecker.com/sailgame/main.html" data-width="500" data-numposts="5"></div>
	</div>
	
	<div id="instructions"></div>
	
	<div id="tack">
		<!--TACK
		<div>( space )</div>-->
	</div>
	
	<div id="ff"></div>
	<div id="fbshare">
		<div class="fb-share-button" data-href="https://www.facebook.com/sailChecker1/" data-layout="button_count" data-size="small" data-mobile-iframe="false"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.facebook.com%2FsailChecker1%2F&amp;src=sdkpreparse">Share</a></div>
	</div>
	<a href="https://www.instagram.com/sailchecker/" target="_blank"><div id="ins"></div></a>
	<a href="https://www.facebook.com/sailChecker1/" target="_blank"><div id="fb"></div></a>
	<a href="https://twitter.com/sailchecker?lang=en" target="_blank"><div id="tw"></div></a>
	
	<div id="title"></div>
	<div id="instructionspop"></div>
	
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery.rotate.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvlKchA46j0y9l-PDK6An7K1W6tonYw4g&libraries=geometry"></script>
    <script type="text/javascript" src="js/windsimulator.js"></script>
	<script type="text/javascript" src="js/game.js"></script>
    <script type="text/javascript" src="js/helpers.js"></script>
    <script type="text/javascript" src="js/boat.js?2=2"></script>
    <script type="text/javascript" src="js/usgsoverlay.js"></script>
  </body>
</html>