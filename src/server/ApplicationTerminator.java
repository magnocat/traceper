package server;

import java.util.ArrayList;

/**
 * Provides polite termination of the application.
 * Terminates all running threads beside the one which called this class.
 * Consequently, calls on each network handling thread a method which closes
 * the sockets used by that thread.
 */
public class ApplicationTerminator {

    private Listener listener;
    private ArrayList<ClientHandler> clients;

    
    public void setListener(Listener listener) {
        this.listener = listener;
    }


    public void setClients(ArrayList<ClientHandler> clients) {
        this.clients = clients;
    }
   

    public void terminateApp() {

        // Check whether the listening and client threads were set.
        assert (listener != null) && (clients != null);

        listener.terminate = true;
        listener.cleanUp();

        /**
         * We iterate through the copy of the clients because cleanUp modifies
         * the clients list (it removes the client from the list).
         */
        ArrayList<ClientHandler> clientsCopy =
                new ArrayList<ClientHandler>(clients);
        for (ClientHandler ch : clientsCopy) {
            ch.terminate = true;
            ch.cleanUp();
        }
        
        // Here the application should terminate.
        System.exit(0);
    }

}
