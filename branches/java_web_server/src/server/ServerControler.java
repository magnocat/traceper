package server;

import common.Settings;
import java.io.*;
import java.util.ArrayList;


public class ServerControler {
	
    public static void main (String args[]) {

        int port;
        if (args.length > 0) {
            port = Integer.valueOf(args[0]);
        } else {
            port = Settings.PORT;
        }

        Logger logger = new Logger(System.out);

        ApplicationTerminator terminator = new ApplicationTerminator();
        ArrayList<ClientHandler> clients = new ArrayList<ClientHandler>();
        Listener listener = new Listener(clients, port, terminator);

        terminator.setClients(clients);
        terminator.setListener(listener);

        try {

            listener.start();

            BufferedReader in = new BufferedReader(
                            new InputStreamReader(System.in));

            String cmd = in.readLine();
            while (!cmd.equalsIgnoreCase(Settings.QUIT_COMMAND)) {

            	System.out.println("deneme");    
            	cmd = in.readLine();

            }
  
        } catch(Exception e) {

            logger.logException(getControlerId(), e.getMessage());            
            printTerminationMsg(logger);
        }
        finally {

            terminator.terminateApp();
        }
        
    }

    
    private static String getControlerId() {
        return "[Controling thread]";
    }


    private static void printTerminationMsg(Logger logger) {
        logger.log(getControlerId() + " terminating");
    }
} 