package brasilBurger.com.repositories.Impl;

import brasilBurger.com.config.database.Database;
import brasilBurger.com.entities.Burger;
import brasilBurger.com.entities.Complement;
import brasilBurger.com.entities.Enum.TypeComplement;
import brasilBurger.com.repositories.ComplementRepository;

import java.sql.*;
import java.util.Collections;
import java.util.List;

public class ComplementRepositoryImpl implements ComplementRepository {
    private static ComplementRepositoryImpl instance=null;
    private Database database;

    public static ComplementRepositoryImpl getInstance(Database database) {
        if(instance==null){
            instance=new ComplementRepositoryImpl(database);
        }
        return instance;
    }

    private ComplementRepositoryImpl(Database database) {
        this.database = database;
    }

    /*
    @Override
    public int insert(Complement complement) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("INSERT INTO Complement (nom, prix, type_complement, created_at, image)\n" +
                    "VALUES (?, ?, ?, ?, ?);");
            //convertion des types Java ==> Types e Bases doné
            ps.setString(1, complement.getNom());
            ps.setInt(2, complement.getPrix());
            ps.setString(2, String.valueOf(complement.getTypeComplement()));
            ps.setDate(4, Date.valueOf(complement.getCreatedAt()));
            ps.setString(4,complement.getImage());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        } catch(SQLException e){
            e.printStackTrace();
        }
        return 0;
    }*/

    private Complement mapToBurger(ResultSet rs) throws SQLException {
        Complement complement = new Complement();
        complement.setId(rs.getInt("id"));
        complement.setNom(rs.getString("nom"));
        complement.setPrix(rs.getInt("prix"));
        complement.setTypeComplement(TypeComplement.valueOf(rs.getString("type_complement")));
        complement.setCreatedAt(rs.getDate("created_at").toLocalDate());
        complement.setImage(rs.getString("image"));
        return complement;
    }

    @Override
    public List<Complement> selectAll() {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SELECT * FROM complement;");
            return database.<Complement>fetchAll(ps,this::mapToBurger);
        }catch (SQLException e){
            e.printStackTrace();
        }
        return Collections.emptyList();
    }
    /*
    @Override
    public int update(Complement complement) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SET nom=?, prix=? \n" +
                    "WHERE id=?;");
            ps.setString(1, complement.getNom());
            ps.setInt(2, complement.getPrix());
            ps.setInt(3,complement.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }*/

    @Override
    public int delete(Complement complement) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("DELETE FROM Complement WHERE id = ?;");
            ps.setInt(1,complement.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }
}
