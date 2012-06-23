package server;

import java.net.ServerSocket;
import java.net.Socket;
import java.nio.channels.ClosedByInterruptException;
import java.util.ArrayList;


/**
 * Listens for clients.
 * Once client is accepted creates thread which handles that particular client.
 * Has the right to terminate the application if it fails to create a listening
 * socket.
 */
public class Listener extends Thread {

	private ServerSocket listener;
	private int port;

	private ArrayList<ClientHandler> clients;

	private ApplicationTerminator terminator;

	private Logger logger;

	public volatile boolean terminate = false;

	public Listener(
			ArrayList<ClientHandler> clients,
			int port,
			ApplicationTerminator terminator
			) {
		this.clients = clients;
		this.port = port;
		this.terminator = terminator;
		logger = new Logger(System.out);
	}


	@Override
	public void run() {

		try {

			listener = new ServerSocket(port);
			logger.log(getListenerId() + " started");

		} catch (Exception e) {

			/*
			 * When we fail to create the listener there is no point in keeping
			 * the server running, the application shuts down.
			 */
			logger.logException(
					getListenerId() +
					" Failed to create listening socket!", e.getMessage());
			terminator.terminateApp();
			printTerminationMsg();

			return;
		}

		while (true) {
			try {

				Socket clientSocket = listener.accept();                
				logger.log(
						getListenerId() + " " +
								clientSocket.getRemoteSocketAddress() + " connected");
				new ClientHandler(clientSocket, clients);


			} catch (Exception e) {

				/**
				 * When we fail to accept the client we don't care, we continue
				 * listening for other clients.
				 */

				/**
				 * This exception was probably raised because we closed
				 * a listening socket due to termination and therefore
				 * no exception message is shown.
				 */
				if (terminate) {
					printTerminationMsg();
					return;
				}

				logger.logException(
						getListenerId() +
						" Failed to accept the client!",
						e.getMessage());
			}

		}

	}


	private String getListenerId() {
		return "[Listening thread]";
	}


	private void printTerminationMsg() {
		logger.log(getListenerId() + " terminating");
	}


	/**
	 * Closes the listening socket.
	 */
	public void cleanUp() {
		try { listener.close(); } catch (Exception e) {
			logger.logException(
					getListenerId() + " Clean up failed!",
					e.getMessage());
		}
	}

	
}
