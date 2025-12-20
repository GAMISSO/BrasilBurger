using Models;
using System.Collections.Generic;

namespace Services
{
    public interface ICatalogService
    {
        List<Burger> GetBurgers();
        List<Menu> GetMenus();
        List<Complement> GetComplements();

        Burger GetBurger(int id);
        Menu GetMenu(int id);
    }
}
