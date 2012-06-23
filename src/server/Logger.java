
package server;

import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;

/**
 * Serves for displaying or logging messages.
 */
public class Logger {

    PrintWriter out;

    public Logger(OutputStream out) {
        this.out = new PrintWriter(out, true);
    }

    public void log(String msg) {
        out.println(msg);
    }

    void logException(String description, String excepMsg) {
        out.println(description);
        out.println("Exception: " + excepMsg);
    }

}
