package server;
import java.net.ServerSocket;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.ResultSet;

public class mySqlInterface {

	private Connection conn;

	public void initializeDB(String adress, String userName, String password)
	{
		try {
			// The newInstance() call is a work around for some
			// broken Java implementations
			Class.forName("com.mysql.jdbc.Driver").newInstance();
		} catch (Exception ex) {
			// handle the error
			System.out.println("SQLException: " + ex.getMessage());
		}
		this.conn = null;
		try {
			this.conn =
					DriverManager.getConnection(adress +
							"user="+userName+"&password="+password);
			// Do something with the Connection
		} catch (SQLException ex) {
			// handle any errors
			System.out.println("SQLException: " + ex.getMessage());
			System.out.println("SQLState: " + ex.getSQLState());
			System.out.println("VendorError: " + ex.getErrorCode());
		}
	}

	public int runQuery(String query)
	{
		int rowCount = 0;
		Statement stmt = null;
		ResultSet rs = null;
		try {
			stmt = this.conn.createStatement();
			//String query="SELECT Id FROM traceper_users WHERE deviceId = 355543020162426 LIMIT 1;";
			//String query="SELECT * FROM traceper_friends";
			rs = stmt.executeQuery(query);


			while (rs.next())
			{
				System.out.println(rs.getString(1));
				//System.out.println(":");
			}

			rs.last();
			rowCount = rs.getRow();
			System.out.println(rowCount);

			/*
			// or alternatively, if you don't know ahead of time that
			// the query will be a SELECT...
			if (stmt.execute("SELECT * FROM traceper_friends")) {
				rs = stmt.getResultSet();
			}
			// Now do something with the ResultSet ....
			 * 
			 */
		}
		catch (SQLException ex){
			// handle any errors
			System.out.println("SQLException: " + ex.getMessage());
			System.out.println("SQLState: " + ex.getSQLState());
			System.out.println("VendorError: " + ex.getErrorCode());
		}
		finally {
			// it is a good idea to release
			// resources in a finally{} block
			// in reverse-order of their creation
			// if they are no-longer needed
			if (rs != null) {
				try {
					rs.close();
				} catch (SQLException sqlEx) { } // ignore
				rs = null;
			}
			if (stmt != null) {
				try {
					stmt.close();
				} catch (SQLException sqlEx) { } // ignore
				stmt = null;
			}
		}
		return rowCount;
	}
	
	public void updateLocationRecord(double latitude, double longitude, double altitude, int year, int month, int day, int hour, int minute, int second, String deviceId)
	{
		String calculatedTime = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+second;
		/*
		String query="UPDATE traceper_users SET latitude ="
				+latitude+",longitude ="+longitude+" , altitude ="+altitude+",dataArrivedTime = NOW(), dataCalculatedTime = "+calculatedTime+" WHERE deviceId = "+deviceId+" LIMIT 1;";
		*/
		String query="UPDATE traceper_users SET latitude ="
				+latitude+",longitude ="+longitude+" , altitude ="+altitude+",dataArrivedTime = NOW() WHERE deviceId = "+deviceId+" LIMIT 1;";
		
		System.out.println(query);
		
		Statement stmt = null;
		try {
			stmt = this.conn.createStatement();
			//String query="SELECT Id FROM traceper_users WHERE deviceId = 355543020162426 LIMIT 1;";
			//String query="SELECT * FROM traceper_friends";
			stmt.executeUpdate(query);

		}
		catch (SQLException ex){
			// handle any errors
			System.out.println("SQLException: " + ex.getMessage());
			System.out.println("SQLState: " + ex.getSQLState());
			System.out.println("VendorError: " + ex.getErrorCode());
		}
		finally {
			// it is a good idea to release
			// resources in a finally{} block
			// in reverse-order of their creation
			// if they are no-longer needed
			if (stmt != null) {
				try {
					stmt.close();
				} catch (SQLException sqlEx) { } // ignore
				stmt = null;
			}
		}
	}
}


/*
try {
	// The newInstance() call is a work around for some
	// broken Java implementations
	Class.forName("com.mysql.jdbc.Driver").newInstance();
} catch (Exception ex) {
	// handle the error
	System.out.println("SQLException: " + ex.getMessage());
}
Connection conn = null;
try {
	conn =
			DriverManager.getConnection("jdbc:mysql://localhost/php?" +
					"user=root&password=");
	// Do something with the Connection
} catch (SQLException ex) {
	// handle any errors
	System.out.println("SQLException: " + ex.getMessage());
	System.out.println("SQLState: " + ex.getSQLState());
	System.out.println("VendorError: " + ex.getErrorCode());
}

Statement stmt = null;
ResultSet rs = null;
try {
	stmt = conn.createStatement();
	String query="SELECT Id FROM traceper_users WHERE deviceId = 355543020162426 LIMIT 1;";
	//String query="SELECT * FROM traceper_friends";
	rs = stmt.executeQuery(query);
	
	  
    
	while (rs.next())
	{
		System.out.println(rs.getString(1));
		System.out.println(":");
	}
	
	rs.last();
    int rowCount = rs.getRow();
    System.out.println(rowCount);
	
    /*
	// or alternatively, if you don't know ahead of time that
	// the query will be a SELECT...
	if (stmt.execute("SELECT * FROM traceper_friends")) {
		rs = stmt.getResultSet();
	}
	// Now do something with the ResultSet ....
	 * 
	 
}
catch (SQLException ex){
	// handle any errors
	System.out.println("SQLException: " + ex.getMessage());
	System.out.println("SQLState: " + ex.getSQLState());
	System.out.println("VendorError: " + ex.getErrorCode());
}
finally {
	// it is a good idea to release
	// resources in a finally{} block
	// in reverse-order of their creation
	// if they are no-longer needed
	if (rs != null) {
		try {
			rs.close();
		} catch (SQLException sqlEx) { } // ignore
		rs = null;
	}
	if (stmt != null) {
		try {
			stmt.close();
		} catch (SQLException sqlEx) { } // ignore
		stmt = null;
	}
}
*/
