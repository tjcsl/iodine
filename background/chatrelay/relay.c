/* relay.c
 *
 * This code acts as a relay between php and several socket connections so that they don't expire between page loads.
 * Used for the AJAX transport version of iodine chat.
 *
 */

#include <sys/types.h>
#include <sys/msg.h>
#include <sys/ipc.h>
#include <string.h>
#include <stdio.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netinet/tcp.h>
#include <netdb.h>

#define NUMSOCKS 1000
//Just using this for testing right now, something more permanent later.
#define IRCSERVER "blackhole.homelinux.com"
#define MAXSTRLENGTH 1000

int main (void) {

        key_t recipckey, sendipckey;
        int rec_mq_id,send_mq_id;
        struct { long type; char text[1000]; } recmsg,sendmsg;
        int received;
	struct { long id; int sock; } sockets[NUMSOCKS];
	struct hostent *hp;
	struct sockaddr_in address;
	char buffer[MAXSTRLENGTH];
	int on=1;

	// Get basic info for the sockets and store it.
	if((hp = gethostbyname(IRCSERVER)) == NULL){ //If we can't find the host
		exit(128);
	}
	bcopy(hp->h_addr, &address.sin_addr, hp->h_length);
	address.sin_port = htons(6667);
	address.sin_family = AF_INET;
	
	// Clear the list of sockets
	memset(sockets,0,300);// 300 ints long

        /* We use the key 15 for messages just recieved from the user */
        /* Set up the reference to the recieving message queue */
	/* We use the key 14 for messages to send to the user */
	/* Set up the reference to the sending message queue */
        recipckey = 15;
	sendipckey= 14;

        rec_mq_id = msgget(recipckey, 0);
        send_mq_id = msgget(sendipckey, 0);

	int marker=0,unused=-1;
	while (1) { // Main loop
		// Handle all incoming messages
		while((received = msgrcv(rec_mq_id, &recmsg, sizeof(recmsg), 0, IPC_NOWAIT))!=-1) {
			printf("%s (%d) (%d)\n", recmsg.text, received,recmsg.type);
			int i;
			marker=0;
			for(i=0;i<NUMSOCKS;i++) {
				if(sockets[i].id==recmsg.type) {
					write(sockets[i].sock,recmsg.text);
					marker=1;
					break;
				}
			}
			if(marker==0) { //We don't have a socket for this one yet
				for(i=0;i<NUMSOCKS;i++) {
					if(sockets[i].id==0) {
						unused=i;
						marker=1;
						break;
					}
				}
				if(marker==1) { //We found a free index!
					sockets[i].id=recmsg.type;
					sockets[i].sock = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP);
					setsockopt(sockets[i].sock, IPPROTO_TCP, TCP_NODELAY, (const char *)&on, sizeof(int));
					if(connect(sockets[i].sock, (struct sockaddr *)&address, sizeof(struct sockaddr_in)) == -1){
						marker=0;
					}
					printf("Created Socket");
				}
			}
			if(marker==1) { // If we have a socket to write to, write to it.
				write(sockets[i].sock,recmsg.text);
			}
		}
		int i;
		for(i=0;i<NUMSOCKS;i++) {
			if(read(sockets[i].sock, buffer, MAXSTRLENGTH - 1) != 0) {
				memset(sendmsg.text, 0, 1000); /* Clear out the space */
				strcpy(sendmsg.text, buffer); // Just for testing 
				printf("%s",buffer);
				sendmsg.type = sockets[i].id;
				msgsnd(send_mq_id, &sendmsg, sizeof(sendmsg), 0);
			}
		}
	}
}
