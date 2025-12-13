package brasilBurger.com.services;

import brasilBurger.com.entities.Burger;

import java.util.List;
import java.util.Optional;

public interface BurgerService {
    /*int createBurger(Burger burger);*/
    List<Burger> getAllBurgers();
    /*Optional<Burger> getBurgerById(int id);
    int deleteBurgerById(int id);
    int updateBurger(Burger burger);*/
}
