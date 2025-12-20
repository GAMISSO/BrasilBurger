using Data;
using Models;
using System.Collections.Generic;
using System.Linq;
using Microsoft.EntityFrameworkCore;

namespace Services.Impl
{
    public class CatalogService : ICatalogService
    {
        private readonly ApplicationDbContext _context;

        public CatalogService(ApplicationDbContext context)
        {
            _context = context;
        }

        public List<Burger> GetBurgers()
            => _context.Burgers.ToList();

        public List<Menu> GetMenus()
            => _context.Menus
                .Include(m => m.Burger)
                .Include(m => m.MenuComplements)
                    .ThenInclude(mc => mc.Complement)
                .ToList();

        public List<Complement> GetComplements()
            => _context.Complements.ToList();

        public Burger GetBurger(int id)
            => _context.Burgers.Find(id);

        public Menu GetMenu(int id)
            => _context.Menus
                .Include(m => m.Burger)
                .Include(m => m.MenuComplements)
                    .ThenInclude(mc => mc.Complement)
                .FirstOrDefault(m => m.Id == id);
    }
}
