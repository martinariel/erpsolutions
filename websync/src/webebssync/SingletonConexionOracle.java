/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package webebssync;
import java.sql.*;

/**
 *
 * @author Martin
 */
public class SingletonConexionOracle 
{
    
    private static Connection connection = null;
    
    public static Connection open()
    {
       if ( connection == null )
       {
            try 
            {
                // Load the JDBC driver
                String driverName = "oracle.jdbc.driver.OracleDriver";
                Class.forName(driverName);

                // Create a connection to the database
                String serverName = "127.0.0.1";
                String portNumber = "1521";
                String sid        = "mydatabase";
                String url        = "jdbc:oracle:thin:@" + serverName + ":" + portNumber + ":" + sid;
                String username   = "username";
                String password   = "password";
                
                connection = DriverManager.getConnection(url, username, password);
            } 
            catch (ClassNotFoundException e) 
            {
                // Could not find the database driver
                connection = null;
            } 
            catch (SQLException e) 
            {
                // Could not connect to the database
                connection = null;
            }
       }
       
       return connection;                  
    }
    
    //--------------------------------------------------------------------------
    
    public static ResultSet execute ( String sql )
    {
        if ( connection == null )
            return null;
        
        ResultSet rset;
       
        try 
        {
            Statement stmt = connection.createStatement();
            try 
            {
                rset = stmt.executeQuery ( sql );
           
            } 
            finally 
            {
                try { stmt.close(); } catch ( Exception ignore ) {}
            }
       } 
       catch ( Exception ignore ) 
       {
           return null;
       }
        
        return rset;
    }
    
    //--------------------------------------------------------------------------
    
    public static void close()
    {
        if ( connection != null )
        {
            try
            {
                connection.close();
            }
            catch ( Exception e )
            {
            }
        }
    }
}
