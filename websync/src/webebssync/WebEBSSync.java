package webebssync;
import java.sql.*;

/**
 *
 * @author Martin
 */
public class WebEBSSync 
{
    
    private static Connection oracle;
    
    private static final int DATA_PACKET_SIZE = 16;
    
   
    //--------------------------------------------------------------------------

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) 
    {
        
        /*
        oracle = SingletonConexionOracle.open();
        
        if ( oracle == null )
        {
            System.out.println( "Error al conectarse a Oracle.");
            return;
        }
         
         */
      
        importarClientes();
        
        if ( args.length >= 1 )
        {
            String c = args[0].trim();
            
            if ( c.equalsIgnoreCase( "TODOS") )
            {
                importarClientes();
                importarVendedores();
                importarTerminos();
                importarProductos();
                importarPrecios();
                
            }
            else if ( c.equalsIgnoreCase( "CLIENTES" ) )
            {
                importarClientes();
            }
            else if ( c.equalsIgnoreCase( "VENDEDORES") )
            {
                importarVendedores();
            }
            else if ( c.equalsIgnoreCase( "TERMINOS" ) )
            {
                importarTerminos();
            }
            else if ( c.equalsIgnoreCase ( "PRECIOS" ) )
            {
                importarPrecios();
            }
            else if ( c.equalsIgnoreCase ( "PRODUCTOS" ) )
            {
                importarProductos();
            }    
        }
        
        SingletonConexionOracle.close();
        
    }
    
    //--------------------------------------------------------------------------
    
    private static void importar ( String entidad , String sql )
    {
        WS.OfarWebInterfaceV1_Impl w = new WS.OfarWebInterfaceV1_Impl();
        
        try
        {
            boolean ok = w.getOfarWebInterfaceV1Port().iniciar( entidad );
            
            if ( ok )
            {
                int           i       = 0;
                StringBuilder packet  = new StringBuilder();
                ResultSet     r       = SingletonConexionOracle.execute ( sql );
               
                
                ResultSetMetaData m = r.getMetaData();
                int columnas = m.getColumnCount();

                while ( r != null && r.next() )
                {
                    if ( i == 0 )
                    {
                        //Agrego el nombre de columnas
                        
                        for ( int j = 1 ; j <= columnas ; j++)
                        {
                            packet.append ( m.getColumnName(j));
                            packet.append ( "|;-|" );
                        }
                        
                        packet.append ( "||||" );
                    }
                    
                    if ( i++ == DATA_PACKET_SIZE )
                    {
                        ok = w.getOfarWebInterfaceV1Port().
                                agregarRegistro( entidad , packet.toString());

                        if ( !ok )
                            break;

                        packet = new StringBuilder();
                        i      = 0;
                    }

                    for ( int j = 1 ; j <= columnas ; j++ )
                    {
                        packet.append ( r.getString(j) );
                        packet.append ( "|;-|"         );
                    }

                    packet.append ( "||||");

                }

                if ( r != null ) r.close();
            
            }
            
            if ( ok )
            {
                w.getOfarWebInterfaceV1Port().finalizar( entidad );
            }        
        }
        catch ( Exception ignore ) { }
    }
    
    
    //--------------------------------------------------------------------------
    
    private static void importarClientes()
    {
        importar ( "RA_CUSTOMERS" , "SELECT * FROM CLIENTES");      
    }
    
    //--------------------------------------------------------------------------
    
    private static void importarVendedores()
    {
        importar ( "SALESREPS" , "SELECT * FROM SALESREPS");
    }
    
    //--------------------------------------------------------------------------
    
    private static void importarTerminos()
    {
        importar ( "RA_TERMS_TL" , "SELECT * FROM RA_TERMS_TL");
    }
    
    //--------------------------------------------------------------------------
    
    private static void importarPrecios()
    {
        importar ( "LIST_PRICE" , "SELECT * FROM LIST_PRICE" );
    }
    
    //--------------------------------------------------------------------------
    
    private static void importarProductos()
    {
        importar ( "MTL_SYSTEM_ITEMS_B" , "SELECT * FROM MTL_SYSTEM_ITEMS_B");
    }
    
    //--------------------------------------------------------------------------
    
}
