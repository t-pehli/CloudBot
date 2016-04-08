# CloudBot
A PHP-based distributed skeleton OS designed to work on a webserver.

Notes:

System architecture:


	CloudOS bot:

	PRESENT
	-supports rudimentary os functions by itself, like shell access etc.
	-loads a single environment from a supported list, like a master program
	-the environment is responsible for UI, functionality and communication
	-communication is bot-user for simple envs (shell) and bot-bot for more advanced
	-every bot runs (pulses) for a certain time due to host restrictions
	-when finishing a pulse, a bot passes execution to itself or forward

	FUTURE
	-mutiple bots form a chain that acts as a virtual machine
	-every bot completes multiple pulses up to a limit (daily)
	-on day start, every bot gets the address of next 3 bots from master 
	-after hitting its quota, the bot tries to pass execution along
	-the current bot pings the next one
	-on fail, tries 2, then 3, then reruns itself and asks master

	Cloudmaster 
	-has current running bot info in db
	-backs up daily to secondary master server

Connection process:
1-   client connects to Cloudmaster
2-   master provides client basic system & IO logic
2.5- (later) master authenticates client
3-   master gives client address of current bot +2 next
4-   client connects with current bot
5-   bot sends environment data (GUI, logic e.t.c.)
6-   connection is ready
7-   client communicates with bot until execution passes over
8-   when the bot hit its quota, it broadcasts next bot found
9-   client reads the broadcast and connects with next bot


Bot outbound message structure:

{ type: (msg/ask/path/error), content }
