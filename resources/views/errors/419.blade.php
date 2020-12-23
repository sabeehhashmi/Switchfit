<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Page Expired</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

	<!-- Custom stlylesheet -->
	<style>
		* {
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}

		body {
			padding: 0;
			margin: 0;
		}
		.logo-container {

			padding: 15px 0px;
			align-self: center;
		}
		.logo-container img {
			max-width: 103px;
			background: linear-gradient(to bottom, #3f1120 0%,#330f23 22%,#10092d 71%,#08082f 88%,#08082f 100%);
			padding: 12px;
		}
		.home-link {
			color: #3c15a3;
			text-decoration: none;
			font-size: 20px;
			font-weight: bold;
			font-family: 'Roboto', sans-serif;
		}
		#notfound {
			position: relative;
			height: 135vh;
			background: linear-gradient(to bottom, #3f1120 0%,#330f23 22%,#10092d 71%,#08082f 88%,#08082f 100%);
		}

		#notfound .notfound {
			position: absolute;
			left: 50%;
			top: 36%;
			-webkit-transform: translate(-50%, -50%);
			-ms-transform: translate(-50%, -50%);
			transform: translate(-50%, -50%);
		}

		.notfound {
			max-width: 767px;
			width: 100%;
			line-height: 1.4;
			padding: 89px 40px;
			text-align: center;
			background: #fff;
			-webkit-box-shadow: 0 15px 15px -10px rgba(0, 0, 0, 0.1);
			box-shadow: 0 15px 15px -10px rgba(0, 0, 0, 0.1);
		}

		.notfound .notfound-404 {
			position: relative;
			height: 180px;
		}

		.notfound .notfound-404 h1 {
			font-family: 'Roboto', sans-serif;
			position: absolute;
			left: 50%;
			top: 50%;
			-webkit-transform: translate(-50%, -50%);
			-ms-transform: translate(-50%, -50%);
			transform: translate(-50%, -50%);
			font-size: 165px;
			font-weight: 700;
			margin: 0px;
			color: #3c15a3;
			text-transform: uppercase;
		}

		.notfound .notfound-404 h1>span {
			color: #342757;
		}

		.notfound h2 {
			font-family: 'Roboto', sans-serif;
			font-size: 22px;
			font-weight: 400;
			text-transform: uppercase;
			color: #151515;
			margin-top: 0px;
			margin-bottom: 25px;
		}

		.notfound .notfound-search {
			position: relative;
			max-width: 320px;
			width: 100%;
			margin: auto;
		}

		.notfound .notfound-search>input {
			font-family: 'Roboto', sans-serif;
			width: 100%;
			height: 50px;
			padding: 3px 65px 3px 30px;
			color: #151515;
			font-size: 16px;
			background: transparent;
			border: 2px solid #c5c5c5;
			border-radius: 40px;
			-webkit-transition: 0.2s all;
			transition: 0.2s all;
		}

		.notfound .notfound-search>input:focus {
			border-color: #00b7ff;
		}

		.notfound .notfound-search>button {
			position: absolute;
			right: 15px;
			top: 5px;
			width: 40px;
			height: 40px;
			text-align: center;
			border: none;
			background: transparent;
			padding: 0;
			cursor: pointer;
		}

		.notfound .notfound-search>button>span {
			width: 15px;
			height: 15px;
			position: absolute;
			left: 50%;
			top: 50%;
			-webkit-transform: translate(-50%, -50%) rotate(-45deg);
			-ms-transform: translate(-50%, -50%) rotate(-45deg);
			transform: translate(-50%, -50%) rotate(-45deg);
			margin-left: -3px;
		}

		.notfound .notfound-search>button>span:after {
			position: absolute;
			content: '';
			width: 10px;
			height: 10px;
			left: 0px;
			top: 0px;
			border-radius: 50%;
			border: 4px solid #c5c5c5;
			-webkit-transition: 0.2s all;
			transition: 0.2s all;
		}

		.notfound-search>button>span:before {
			position: absolute;
			content: '';
			width: 4px;
			height: 10px;
			left: 7px;
			top: 17px;
			border-radius: 2px;
			background: #c5c5c5;
			-webkit-transition: 0.2s all;
			transition: 0.2s all;
		}

		.notfound .notfound-search>button:hover>span:after {
			border-color: #00b7ff;
		}

		.notfound .notfound-search>button:hover>span:before {
			background-color: #00b7ff;
		}

		@media only screen and (max-width: 767px) {
			.notfound h2 {
				font-size: 18px;
			}
		}

		@media only screen and (max-width: 480px) {
			.notfound .notfound-404 h1 {
				font-size: 141px;
			}
		}

	</style>

	</head>

	<body>

		<div id="notfound">
			<div class="notfound">
				<div class="logo-container">
					<img src="{{ url('/') }}/assets/images/login-logo.png" alt="logo">
				</div>
				<div class="notfound-404">
					<h1>4<span>1</span>9</h1>
				</div>
				<h2>the page you requested is expired</h2>
				<a class="home-link" href="{{ url('/') }}">Go To Home</a>
			</div>
		</div>

	</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

	</html>
