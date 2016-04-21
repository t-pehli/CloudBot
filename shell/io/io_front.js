/**
* 	 IO front-end logic
*/

// ----------- Constants & Globals ---------------
var IO_CLOCK = 1000;

var timeout;
var lastPath = "/";
var commandBuffer = [""];
var commandHistory = 0;
// -----------------------------------------------


// --------------- State Machine -----------------
var STM = {
	state: "idle", // possible states: idle|running|blocked
	initialise: function () {
		$("#cmd").val("");

		$("#path").text("");
		this.state = "running";
		longpoll( "ping -s" );
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
						commandBuffer.unshift( $("#cmd").val() );
						commandHistory = 0;
						$("#cmd").val("");
					}
				}
				else if( eventType == "keypress" && eventContent == "up"  ){

					if( commandHistory < commandBuffer.length ){

						$("#cmd").val( commandBuffer[commandHistory] );
						commandHistory++;
					}
				}
				else if( eventType == "keypress" && eventContent == "down"  ){

					if( commandHistory > 1 ){

						commandHistory--;
						$("#cmd").val( commandBuffer[commandHistory-1] );
					}
					else if ( commandHistory == 1 ){

						commandHistory = 0;
						$("#cmd").val("");
					}				
				}
				else if( eventType == "keypress" && eventContent == "tab"  ){

					if( $("#cmd").val() != "" ){

						longpoll( "autocomplete "+$("#cmd").val() );
					}
				}
				else if( eventType == "poll" ){

					var i=0;
					var i=0;
					while( i<eventContent.length && eventContent[i].type == "msg" ){
						// run though incoming printx msgs 

						var mainDiv = $("#main");
						mainDiv.append( "<p>"+eventContent[i].content+"</p>" );
						mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
						$("#cmd").focus();
						i++;
					}

					if( i<eventContent.length && eventContent[i].type == "auto" ){
					
						if( eventContent[i].content !== "" ){

							$("#cmd").val( eventContent[i].content );
						}
						clearTimeout(timeout);
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
					else if( i<eventContent.length && eventContent[i].type == "ping" ){
						// got a ping back, (re)start session

						$("#path").text( lastPath +">" );

						if( eventContent[i].content.substring(0, 3) == "set" ){

							var setArgs = eventContent[i].content.split(" "); // 0 = set
							
							if( setArgs[1] == "IO_CLOCK" ){
								
								IO_CLOCK = parseInt( setArgs[2] );
							}
						}
						else{

							var mainDiv = $("#main");
							mainDiv.append( "<p>"+eventContent[i].content+"</p>" );
							mainDiv.scrollTop( mainDiv.prop("scrollHeight") );
						}
						
						clearTimeout(timeout);

						this.state = "idle";
					}
					else if( i<eventContent.length && eventContent[i].type == "ask" ){
						// check if a scanx ask is left, block

						$("#path").text( eventContent[i].content +">" );
						clearTimeout(timeout);

						this.state = "blocked";
					}
					else {
						// if nothing is left, continue polling

					}
				}
				else if( eventType == "interrupt" ){
					
					$("#path").text( lastPath +">" );
					this.state = "running";
					clearTimeout( timeout );
					longpoll( "interrupt " + eventContent );
					$("#cmd").val("");
				}
				break;
			// ===================================
			case "blocked":
				if( eventType == "keypress" && eventContent == "enter" ){
					
					if( $("#cmd").val() != "" ){
						
						$("#path").text("");
						this.state = "running";
						longpoll( $("#cmd").val() );
						$("#cmd").val("");
					}
				}
				else if( eventType == "interrupt" ){

					
					$("#path").text( lastPath +">" );
					this.state = "running";
					clearTimeout( timeout );
					longpoll( "interrupt " + eventContent );
					$("#cmd").val("");
				}
				break;
			// ===================================
		}
	}
}; 
// -----------------------------------------------

// ------------- Helper Functions ----------------
function longpoll( postData ){
	// perform longpoll

	var onSuccess = function(response){

		clearTimeout(timeout);
		timeout = setTimeout( function(){ longpoll(); }, IO_CLOCK);

		STM.stateEvent( "poll", response );		
	}

	var onFail = function(e){

		STM.stateEvent( "interrupt", e.responseText );
	}


	if (typeof postData != 'undefined'){
		
		postData = JSON.stringify( postData );
	}
		
	jQuery.ajax({
		url: "/shell/io/buffer_manager.php",
		type: 'POST',
		dataType: 'JSON',
		data: { data: postData },
		success: onSuccess,
		error: onFail
	});

}
// -----------------------------------------------


// ------------------ Events ---------------------
$(document).keypress(function(e) {

	// console.log( "w: "+ e.which +" k: " + e.keyCode );

	if(e.which == 13) {
	// Enter pressed, push command to buffer

		STM.stateEvent( "keypress", "enter" );
	}
	else if(e.which == 0 && e.keyCode == 27) {
	// Esc pressed, send interrupt

		e.preventDefault();
		STM.stateEvent( "interrupt", "Interrupted by user." );
	}
	else if(e.which == 0 && e.keyCode == 9) {
	// Tab pressed, autocomplete

		e.preventDefault();
		STM.stateEvent( "keypress", "tab" );
	}
	else if(e.which == 0 && e.keyCode == 38) {
	// Tab pressed, autocomplete

		e.preventDefault();
		STM.stateEvent( "keypress", "up" );
	}
	else if(e.which == 0 && e.keyCode == 40) {
	// Tab pressed, autocomplete

		e.preventDefault();
		STM.stateEvent( "keypress", "down" );
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