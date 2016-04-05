<html>
<head>
	<title>CloudBot OS</title>

	<script type="text/javascript" src="os/lib/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
		// longpolling front-end logic
		var waitTime = 100;
		// var errorTime = 5000;
		var t;

		longpoll(function(response){
			// onSuccess

			$("#response").append("<p>"+ response.join("<br>") +"</p>");
			console.log(response);

		}, function(error){
			// onFailure
			console.log("Ajax Error: " + e.responseText);
		});


		function longpoll( onSuccess, onFailure ){
	
			jQuery.ajax({
				url: "/os/io/io_back.php",
				type: 'POST',
				dataType: 'json',
				success: function(response){

					clearTimeout(t);
					// t = setTimeout( function(){ longpoll( onSuccess, onFailure ); }, waitTime);
					onSuccess(response);
				},
				error: function(e){

					clearTimeout(t);
					onFailure("Ajax Error: " + e.responseText);
				}
		    });

		}

	</script>
</head>
<body>
<div>
	Cloudbot
</div>
<div id="response"></div>
</body>
</html>