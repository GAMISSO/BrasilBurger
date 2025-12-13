package brasilBurger.com.repositories.Impl;

import brasilBurger.com.config.database.Convert;
import brasilBurger.com.config.database.Database;
import brasilBurger.com.entities.Burger;
import brasilBurger.com.repositories.BurgerRepository;

import java.sql.*;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

public class BurgerRepositoryImpl implements BurgerRepository {

    private static BurgerRepositoryImpl instance=null;
    private Database database;

    public static BurgerRepositoryImpl getInstance(Database database) {
        if(instance==null){
            instance=new BurgerRepositoryImpl(database);
        }
        return instance;
    }

    private BurgerRepositoryImpl(Database database) {
        this.database = database;
    }

    @Override
    public int insert(Burger burger) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("INSERT INTO Burger (nom, prix, created_at, image_url)\n" +
                    "VALUES (?, ?, ?, ?);");
            //convertion des types Java ==> Types e Bases doné
            ps.setString(1, burger.getNom());
            ps.setInt(2, burger.getPrix());
            ps.setDate(3,Date.valueOf(burger.getCreatedAt()));
            ps.setString(4,burger.getImage_url());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        } catch(SQLException e){
            e.printStackTrace();
        }
        return 0;
    }
    /*
    private Burger mapToBurger(ResultSet rs) throws SQLException {
        Burger burger = new Burger();
        burger.setId(rs.getInt("id"));
        burger.setNom(rs.getString("nom"));
        burger.setPrix(rs.getInt("prix"));
        burger.setCreatedAt(rs.getDate("date").toLocalDate());
        burger.setImage_url(rs.getString("imageBurger"));
        return burger;
    }


    @Override
    public List<Burger> selectAll() {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SELECT * FROM Burger;");
            return database.<Burger>fetchAll(ps,this::mapToBurger);
        }catch (SQLException e){
            e.printStackTrace();
        }
        return Collections.emptyList();
    }

    @Override
    public int update(Burger burger) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SET nom = ?, prix = ? \n" +
                    "WHERE id = ?;");
            ps.setString(1, burger.getNom());
            ps.setInt(2, burger.getPrix());
            ps.setInt(3,burger.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }

    @Override
    public int delete(Burger burger) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("DELETE FROM Burger WHERE id = ?;");
            ps.setInt(1,burger.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }*/


}
