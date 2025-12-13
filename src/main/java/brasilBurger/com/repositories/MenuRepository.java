package brasilBurger.com.repositories;

import brasilBurger.com.entities.Menu;

import java.util.List;

public interface MenuRepository {
    /*int insert(Menu menu);*/
    List<Menu> selectAll();
    int update(Menu menu);
    //int delete(Menu menu);
}
