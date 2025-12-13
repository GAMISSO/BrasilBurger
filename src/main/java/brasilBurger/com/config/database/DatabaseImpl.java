package brasilBurger.com.config.database;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Optional;

public class DatabaseImpl implements Database {
    private Connection connection;
    public static DatabaseImpl instance;

    public static DatabaseImpl getInstance(Map<String, String> config) {
       if (instance == null)
           instance = new DatabaseImpl(config);
       return instance;
   }

    public static DatabaseImpl getInstance(String driver, String url) {
     if (instance == null)
           instance = new DatabaseImpl(driver, url);
       return instance;
    }

    private DatabaseImpl(Map<String, String> config) {
      String driver = config.get("driver");
       String url = config.get("url");
       connection = openConnection(driver, url);
    }

    private DatabaseImpl(String driver, String url) {
        connection = openConnection(driver, url);
    }

    public Connection openConnection(String driver, String url) {
        try {
            Class.forName(driver);
            return DriverManager.getConnection(url);
        } catch (ClassNotFoundException | SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public Connection getConnection() {
        return connection;
    }

    @Override
    public boolean isConnected() {
        return connection != null;
    }

    @Override
    public void closeConnection() {
        if (connection != null) {
            try {
                connection.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }
    }

    @Override
    public <T> Optional<T> fetch(PreparedStatement ps, Convert<T> convert) throws SQLException {
        ResultSet rs = ps.executeQuery();
        T data = null;
        if (rs.next()) {
            data = convert.toEntity(rs);
        }
        return Optional.ofNullable(data);
    }

    @Override
    public <T> List<T> fetchAll(PreparedStatement ps, Convert<T> convert) throws SQLException {
        ResultSet rs = ps.executeQuery();
        List<T> datas = new ArrayList<>();
        while (rs.next()) {
            datas.add(convert.toEntity(rs));
        }
        return datas;
    }
}
