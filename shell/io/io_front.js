/**
* 	 IO front-end logic
*/

// ----------- Constants & Globals ---------------
var waitTime = 1000;
// var errorTime = 5000;


var timeout;
var lastPath = "/";
// -----------------------------------------------


// --------------- State Machine -----------------
var STM = {
	state: "idle", // possible states: idle|running|blocked
	initialise: function ( newState ) {

		$("#cmd").val("");

		$("#path").text("");
		this.state = "running";
		longpoll();
	},
	stateEvent: function ( eventType, eventContent ) {

		switch( this.state ){
			// ===================================
			case "idle":
				if( eventType == "keypress" && eventContent == "enter" ){

					var mainDiv = $("#main");
					mainDiv.append( "<p>"+$("#path").text()+$("#cmd").val()+"</p>" );
					mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
					$("#cmd").focus();

					if( $("#cmd").val() != "" ){
						
						$("#path").text("");
						this.state = "running";
						longpoll( $("#cmd").val() );
						$("#cmd").val("");
					}
				}
				break;
			// ===================================
			case "running":
				if( eventType == "poll" ){
					
					var i=0;
					while( i<eventContent.length && eventContent[i].type == "msg" ){
						// run though incoming printx msgs 

						var mainDiv = $("#main");
						mainDiv.append( "<p>"+eventContent[i].content+"</p>" );
						mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
						$("#cmd").focus();
						i++;
					}

					if( i<eventContent.length && eventContent[i].type == "path" ){
						// check if a returnx path is left, finish

						lastPath = eventContent[i].content;
						$("#path").text( eventContent[i].content +">" );
						clearTimeout(timeout);

						this.state = "idle";
					}
					else if( i<eventContent.length && eventContent[i].type == "error" ){
						// check if an error is left, break and print

						$("#path").text( lastPath +">" );
						var mainDiv = $("#main");
						mainDiv.append( "<p>"+eventContent[i].content+"</p>" );
						mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
						clearTimeout(timeout);

						this.state = "idle";
					}
					else if( i<eventContent.length && eventContent[i].type == "query" ){
						// check if a scanx query is left, block

						$("#path").text( eventContent.content +">" );
						clearTimeout(timeout);

						this.state = "blocked";
					}
					else {
						// if nothing is left, continue polling

					}
				}
				else if( eventType == "interrupt" ){
					
					$("#path").text( lastPath +">" );
					this.state = "idle";
					clearTimeout(timeout);
					sendInterrupt( eventContent );
				}
				break;
			// ===================================
			case "blocked":
				if( eventType == "keypress" && eventContent == "enter" ){
					
					$("#path").text("");
					this.state = "running";
					longpoll( $("#cmd").val() );
				}
				else if( eventType == "interrupt" ){
					
					$("#path").text( lastPath +">" );
					this.state = "idle";
					clearTimeout(timeout);
					sendInterrupt( eventContent );
				}
				break;
			// ===================================
		}
	}
}; 
// -----------------------------------------------

// ------------- Helper Functions ----------------
function sendInterrupt( msg ){

	var mainDiv = $("#main");
	mainDiv.append( "<p>Interrupted: "+msg+"</p>" );
	mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
	$("#cmd").focus();
	
	jQuery.ajax({
		url: "/shell/io/buffer_manager.php",
		type: 'POST',
		dataType: 'JSON',
		data: { interrupt: msg }
	});
}

function longpoll( postData ){
	// perform longpoll

	var onSuccess = function(response){

		clearTimeout(timeout);
		timeout = setTimeout( function(){ longpoll(); }, waitTime);

		STM.stateEvent( "poll", response );
		
	}

	var onFail = function(e){

		STM.stateEvent( "interrupt", e.responseText );
	}


	if (typeof postData == 'undefined'){
		
		jQuery.ajax({
			url: "/shell/io/buffer_manager.php",
			type: 'POST',
			dataType: 'JSON',
			data: { data: postData },
			success: onSuccess,
			error: onFail
		});
	}
	else {
		
		jQuery.ajax({
			url: "/shell/io/buffer_manager.php",
			type: 'POST',
			dataType: 'JSON',
			data: { data: JSON.stringify( postData ) },
			success: onSuccess,
			error: onFail
		});
	}

}
// -----------------------------------------------


// ------------------ Events ---------------------
$(document).keypress(function(e) {
	// Enter pressed, push command to buffer

	if(e.which == 13) {

		STM.stateEvent( "keypress", "enter" );
	}
	else if(e.which == 27) {

		STM.stateEvent( "interrupt", "Interrupted by user." );
	}
});

$("#wrapper").click( function(){

	$("#cmd").focus();
});
// -----------------------------------------------



// ---------------- Main Logic -------------------
$(function(){
	// Page Loaded

	STM.initialise();
});	
// -----------------------------------------------
