package brasilBurger.com.services;

import brasilBurger.com.entities.Burger;
import brasilBurger.com.entities.Menu;

import java.util.List;
import java.util.Optional;

public interface MenuService {
    /*int createMenu(Menu menu);*/
    List<Menu> getAllMenus();
    /*Optional<Menu> getMenuById(int id);
    int deleteMenuById(int id);
    int updateMenu(Menu menu);*/
}
