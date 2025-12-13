package brasilBurger.com.services.Impl;

import brasilBurger.com.entities.Menu;
import brasilBurger.com.repositories.ComplementRepository;
import brasilBurger.com.repositories.MenuRepository;
import brasilBurger.com.services.ComplementService;
import brasilBurger.com.services.MenuService;

import java.util.List;
import java.util.Optional;

public class MenuServiceImpl implements MenuService {

    private MenuRepository menuRepository;
    private static MenuService instance=null;
    public static MenuServiceImpl getInstance(MenuRepository menuRepository) {
        if (instance == null) {
            instance = new MenuServiceImpl(menuRepository);
        }
        return (MenuServiceImpl) instance;
    }

    private  MenuServiceImpl(MenuRepository menuRepository) {
        this.menuRepository = menuRepository;
    }

    @Override
    public int createMenu(Menu menu) {
        return menuRepository.insert(menu);
    }
    /*
    @Override
    public List<Menu> getAllMenus() {
        return menuRepository.selectAll();
    }

    @Override
    public Optional<Menu> getMenuById(int id) {
        List<Menu> menus = menuRepository.selectAll();
        for (Menu menu : menus) {
            if (menu.getId() == id) {
                return Optional.of(menu);
            }
        }
        return Optional.empty();
    }

    @Override
    public int deleteMenuById(int id) {
        List<Menu> menus = menuRepository.selectAll();
        for (Menu menu : menus) {
            if (menu.getId() == id) {
                menuRepository.delete(menu);
                return menuRepository.selectAll().size();
            }
        }
        return 0;
    }

    @Override
    public int updateMenu(Menu menu) {
        return menuRepository.update(menu);
    }*/
}
