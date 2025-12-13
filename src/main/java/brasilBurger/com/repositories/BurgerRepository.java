package brasilBurger.com.repositories;

import brasilBurger.com.entities.Burger;

import java.util.List;
import java.util.Optional;

public interface BurgerRepository {
    int insert(Burger burger);
    /*List<Burger> selectAll();
    int update(Burger burger);
    int delete(Burger burger);*/
}
