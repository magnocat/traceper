
package server;

//import minisentry;
//import mySqlInterface;
import common.Settings;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.util.ArrayList;

/**
 * Thread which communicates with one client.
 * Receives messages from client and resends the messages to all other clients.
 */
public class ClientHandler extends Thread {

    private String nick = "";
    
    private mySqlInterface interf;

    private Socket socket;
    private BufferedReader in;
    private PrintWriter out;

    private ArrayList<ClientHandler> allClients;

    private Logger logger;

    public volatile boolean terminate = false;


    private String getNick() {
        return nick;
    }

    
    /**
     * Creates the readers and writers through which the socket is accessed.
     */
    public ClientHandler(Socket socket, ArrayList<ClientHandler> allClients) {

        this.allClients = allClients;
        this.socket = socket;
        
        
        this.interf = new mySqlInterface();
		this.interf.initializeDB("jdbc:mysql://localhost/php?", "root", "");
		
		
		logger = new Logger(System.out);

        try {

            in = new BufferedReader(new InputStreamReader(socket.getInputStream()));
            out = new PrintWriter(socket.getOutputStream(), true);

            this.start();
            logger.log(getClientId() + " thread started");
            
        } catch(Exception e) {
            logger.logException(
                    "Failed to create client handler thread for " + 
                    socket.getRemoteSocketAddress() + ".",
                    e.getMessage());
        }
    }


    /**
     * Receives and distributes messages in an infinite loop.
     * The loop can be ended by a failed receive.
     */
    @Override
    public void run() {

        /**
         * The first message the handler receives is the nick of the client.
         * The handler then answers whether the nick is unique or not.
         * If not the client is automatically droped.
         */
    	
    	/*
        String recvNick = receive();
        
        
        if (isUnique(recvNick)) {

            this.nick = recvNick;
            send(Settings.YES);

        } else {

            send(Settings.NO);
            logger.log(getClientId() + " name already in use");
            printTerminationMsg();
            cleanUp();
            
            return;
        }

        /**
         * Any kind of work with the list of clients must be synchronized.
         * That means adding and removing should be synchronized.
         *//*
        synchronized (allClients) {
            allClients.add(this);
        }
        */

        while (true) {

            String msg = receive();
            logger.log(getClientId() + " incoming msg: " + msg);
            
            
            String output = minisentry.parseRequests(msg, this.interf);
            
            send(output);
            
            /*
            // TODO remove
            
                        
            if (msg == null) {
                return;
            }

            if (msg.equalsIgnoreCase(Settings.LIST_CLIENTS)) {
                sendAllClients();
                continue;
            }

            /**
             * Distribute the received message to other clients.
             * Append the message to the nick of the sender.
             *//*
            msg = nick + ": " + msg;
            distribute(msg);
            */

        }        
    }

        
    /**
     * Receives a single message from the client.
     * If we fail to receive a message, ie. exception is thrown then we drop
     * the client.
     * @return Received message or null if the receive fails.
     */
    private String receive() {

        try {

            String msg = in.readLine();
            if (msg == null) {
                cleanUp();
            }

            return msg;
            
        } catch (Exception e) {

            /**
             * The terminator is closing this thread => do not output any error
             * message. The cleanUp was also done in terminator.
             */
            if (terminate) {
                // Do nothing.
            } else {
                
                logger.logException(
                    getClientId() + ": receive failed!",
                    e.getMessage());
                cleanUp();
            }

            return null;
        } 
    }


    /**
     * Distributes (sends) the given message to all the other clients.
     */
    public void distribute(String msg) {
            
        for (ClientHandler ch :allClients) {
            if (ch == this) {
                continue;
            }

            ch.send(msg);
        }
    }


    /**
     * Sends the given message to the remote end.
     * If we fail to send the message, ie. exception is thrown then we
     * drop the client.
     * @return true if the message was successfully sent.
     * Otherwise returns false.
     */
    public boolean send(String msg) {
        try {

            out.println(msg);
            return true;
            
        } catch (Exception e) {            

            logger.logException(
                    getClientId() + ": send failed!",
                    e.getMessage());
            cleanUp();

            return false;
        }
    }

    
    /**
     * Removes this client handler from the list of handlers.
     */
    private void removeThis() {
        synchronized (allClients) {
            allClients.remove(this);
        }
    }


    private String getClientId() {
        
        String pomNick;
        if (nick.equals(""))
        {
            pomNick = "nonick";
        }
        else
        {
            pomNick = nick;
        }
        
        return "[" + pomNick + "/" + socket.getRemoteSocketAddress() + "]";
    }


    private void printTerminationMsg() {
        logger.log(getClientId() + " terminating");
    }


    private void close() {
        try {

            socket.close();
            in.close();
            out.close();            

        } catch (Exception e) {
            logger.logException(
                    getClientId() + ": clean up failed!",
                    e.getMessage());
        }
    }
    
    
    /**
     * Closes the soket and the readers.
     * Removes this client handler from the list of client handlers and prints
     * the message that the thread is terminating.
     */
    public void cleanUp() {
        close();
        removeThis();
        printTerminationMsg();
    }

    
    /**
     * Determines whether the chosen nick is unique.
     */
    private boolean isUnique(String nick) {
        synchronized (allClients) {
            
            for (ClientHandler ch :allClients) {
                if (ch.getNick().equals(nick)) {
                    return false;
                }
            }

            return true;

        }
    }

    
    /**
     * Sends the nicks of all connected clients.
     */
    private void sendAllClients() {
        
        for (ClientHandler ch :allClients) {

            // We don't send empty nicks
            if (ch.getNick().equals("")) {
                continue;
            }

            this.send(ch.getNick());
        }
    }

}
