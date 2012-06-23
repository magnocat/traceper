package client;

import common.Settings;
import java.net.*;
import java.util.ArrayList;
import java.io.*;

class Receiver extends Thread{
	final BufferedReader in;

	Receiver(final BufferedReader in){
		this.in = in;
	}

	@Override
	public void run(){
		String fromServer;
		while(true)
			try{
				if ((fromServer  = in.readLine()) != null){
				synchronized (TCPClient.buffer) {
                                    TCPClient.buffer.add(fromServer);
                                }
					System.out.print('\t');
				}
				else throw new RuntimeException("Received null character from server. This occurs also when the server disconnects.");
			}
		catch (Exception e) {
			System.out.println(e.getMessage());
			System.exit(-1);
		}
	}
}

public class TCPClient{

	public static ArrayList<String> buffer = new ArrayList<String>();

	public static void main (String args[]) throws IOException{ 
		// arguments supply nickname and hostname.

		Socket s = null;
		PrintWriter out = null;
		int colon = args[1].indexOf(':');
		int socketNo = Settings.PORT;
		String host = args[1];
		if (colon != -1)  {
			socketNo = Integer.parseInt(args[1].substring(colon + 1));
			host = args[1].substring(0, colon);
		}

		try{
			s = new Socket(host, socketNo);
			out = new PrintWriter(s.getOutputStream(),true);
			out.println(args[0]);
			final BufferedReader br = new BufferedReader (new InputStreamReader(s.getInputStream()));
			final String nickStatus = br.readLine();

			if (nickStatus.equals(Settings.YES)) new Receiver(br).start();
			else throw new RuntimeException("Nickname already in use.");
		}
		catch (Exception e){ // EOFException, IOException, RuntimeException
			System.out.println(e.getMessage());
			System.exit(-1);
		}

		final BufferedReader br = new BufferedReader(new InputStreamReader(System.in)); 
		String userInput = "";
		while(true)
			if((userInput = br.readLine()) != null){
				if (! userInput.equals("")) out.println(userInput);	
				if (!buffer.isEmpty()){
                                    synchronized (buffer) {
					for (String msg: buffer) System.out.println(msg);
					buffer.clear();
                                    }
				}
			}
	}
}