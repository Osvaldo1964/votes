import java.sql.*;
import org.h2.tools.Csv;

public class ExportH2 {
    public static void main(String[] args) throws Exception {
        String url = "jdbc:h2:C:/InfoVotantes/db/Infovotantes";
        String user = "sa";
        String pass = "";

        try (Connection conn = DriverManager.getConnection(url, user, pass)) {
            System.out.println("Conexión exitosa a: " + url);

            // 1. Listar Tablas
            Statement stmt = stmt = conn.createStatement();
            ResultSet rs = stmt.executeQuery("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='PUBLIC'");

            while(rs.next()) {
                String tableName = rs.getString("TABLE_NAME");
                System.out.println("Exportando tabla: " + tableName + "...");

                // 2. Exportar a CSV
                // CALL CSVWRITE('ruta/archivo.csv', 'SELECT * FROM tabla')
                // Usamos la ruta actual
                String fileName = tableName + ".csv";
                String sqlExport = "CALL CSVWRITE('" + fileName + "', 'SELECT * FROM " + tableName + "')";
                
                Statement exportStmt = conn.createStatement();
                exportStmt.execute(sqlExport);
                System.out.println("   -> Generado: " + fileName);
            }
            
            System.out.println("¡Proceso Terminado!");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
