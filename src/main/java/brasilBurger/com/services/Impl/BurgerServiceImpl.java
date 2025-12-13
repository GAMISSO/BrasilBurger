package brasilBurger.com.services.Impl;

import brasilBurger.com.entities.Burger;
import brasilBurger.com.repositories.BurgerRepository;
import brasilBurger.com.services.BurgerService;

import java.util.List;
import java.util.Optional;

public class BurgerServiceImpl implements BurgerService {
    private BurgerRepository burgerRepository;
    private static BurgerService instance=null;
    public static BurgerServiceImpl getInstance(BurgerRepository burgerRepository) {
        if (instance == null) {
            instance = new BurgerServiceImpl(burgerRepository);
        }
        return (BurgerServiceImpl) instance;
    }

    private  BurgerServiceImpl(BurgerRepository burgerRepository) {
        this.burgerRepository = burgerRepository;
    }

    @Override
    public int createBurger(Burger burger) {
        return burgerRepository.insert(burger);
    }
    /*
    @Override
    public List<Burger> getAllBurgers() {
        return burgerRepository.selectAll();
    }

    @Override
    public Optional<Burger> getBurgerById(int id) {
        List<Burger> burgers = burgerRepository.selectAll();
        for (Burger burger : burgers) {
            if (burger.getId() == id) {
                return Optional.of(burger);
            }
        }
        return Optional.empty();
    }

    @Override
    public int deleteBurgerById(int id) {
        List<Burger> burgers = burgerRepository.selectAll();
        for (Burger burger : burgers) {
            if (burger.getId() == id) {
                burgerRepository.delete(burger);
                return burgerRepository.selectAll().size();
            }
        }
        return 0;
    }

    @Override
    public int updateBurger(Burger burger) {
        return burgerRepository.update(burger);
    }*/
}
