package brasilBurger.com.repositories.Impl;

import brasilBurger.com.config.database.Database;
import brasilBurger.com.entities.Menu;
import brasilBurger.com.repositories.MenuRepository;

import java.sql.*;
import java.util.Collections;
import java.util.List;

public class MenuRepositoryImpl implements MenuRepository {
    private static MenuRepositoryImpl instance=null;
    private Database database;

    public static MenuRepositoryImpl getInstance(Database database) {
        if(instance==null){
            instance=new MenuRepositoryImpl(database);
        }
        return instance;
    }

    private MenuRepositoryImpl(Database database) {
        this.database = database;
    }
    /*
    @Override
    public int insert(Menu menu) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("INSERT INTO Menu (nom, burger_id, prix_total, created_at, image)\n" +
                    "VALUES (?, ?, ?, ?, ?);");
            //convertion des types Java ==> Types e Bases doné
            ps.setString(1, menu.getNom());
            ps.setInt(2, menu.getBurger().getId());
            ps.setInt(3, menu.getPrixTotal());
            ps.setDate(4, Date.valueOf(menu.getDate()));
            ps.setString(5,menu.getImage());
            if (menu.getComplements().size()==1){
                PreparedStatement ps_two=conn.prepareStatement("INSERT INTO menu_complement (menu_id, complement_id)\n" +
                        "VALUES (id, ?);");
            }
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        } catch(SQLException e){
            e.printStackTrace();
        }
        return 0;
    }*/

    private Menu mapToBurger(ResultSet rs) throws SQLException {
        Menu menu = new Menu();
        menu.setId(rs.getInt("id"));
        menu.setNom(rs.getString("nom"));
        menu.setPrixTotal(rs.getInt("prix_total"));
        menu.setDate(rs.getDate("created_at").toLocalDate());
        menu.setImage(rs.getString("image"));
        return menu;
    }

    @Override
    public List<Menu> selectAll() {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SELECT m.*, b.nom AS burgerNom, b.prix_total AS burgerPrix\n" +
                    "FROM Menu m\n" +
                    "JOIN Burger b ON m.burger_id = b.id;");
            return database.<Menu>fetchAll(ps,this::mapToBurger);
        }catch (SQLException e){
            e.printStackTrace();
        }
        return Collections.emptyList();
    }

    @Override
    public int update(Menu menu) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("SET nom = ?, prix_total = ? \n" +
                    "WHERE id = ?;");
            ps.setString(1, menu.getNom());
            ps.setInt(2, menu.getPrixTotal());
            ps.setInt(3,menu.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }
    /*
    @Override
    public int delete(Menu menu) {
        try{
            if(!database.isConnected()){
                throw new SQLException("Database is not connected");
            }
            Connection conn=database.getConnection();
            PreparedStatement ps=conn.prepareStatement("DELETE FROM menu WHERE id = ?;");
            ps.setInt(1,menu.getId());
            int rowsAffected=ps.executeUpdate();
            return rowsAffected;
        }catch (SQLException e){
            e.printStackTrace();
        }
        return 0;
    }*/
}
