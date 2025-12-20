using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Data;
using Models;
using System.Linq;

namespace Controllers
{
    public class CatalogueController : Controller
    {
        private readonly ApplicationDbContext _context;

        public CatalogueController(ApplicationDbContext context)
        {
            _context = context;
        }

        // ==========================
        // PAGE CATALOGUE GÉNÉRALE
        // ==========================
        public IActionResult Index()
        {
            ViewBag.Burgers = _context.Burgers.ToList();
            ViewBag.Menus = _context.Menus
                                    .Include(m => m.Burger)
                                    .ToList();
            ViewBag.Complements = _context.Complements.ToList();

            return View();
        }

        // ==========================
        // DÉTAIL BURGER
        // ==========================
        public IActionResult BurgerDetails(int id)
        {
            var burger = _context.Burgers.FirstOrDefault(b => b.Id == id);
            if (burger == null)
                return NotFound();

            return View(burger);
        }

        // ==========================
        // DÉTAIL MENU
        // ==========================
        public IActionResult MenuDetails(int id)
        {
            var menu = _context.Menus
                .Include(m => m.Burger)
                .FirstOrDefault(m => m.Id == id);

            if (menu == null)
                return NotFound();

            // Récupération des compléments via table de jointure
            var complements = _context.MenuComplements
                .Include(mc => mc.Complement)
                .Where(mc => mc.MenuId == id)
                .Select(mc => mc.Complement)
                .ToList();

            ViewBag.Complements = complements;

            return View(menu);
        }

        // ==========================
        // FILTRAGE DU CATALOGUE
        // ==========================
        public IActionResult Filter(string type)
        {
            ViewBag.Complements = _context.Complements.ToList();

            if (type == "burger")
            {
                ViewBag.Burgers = _context.Burgers.ToList();
                ViewBag.Menus = new List<Menu>();
            }
            else if (type == "menu")
            {
                ViewBag.Burgers = new List<Burger>();
                ViewBag.Menus = _context.Menus.Include(m => m.Burger).ToList();
            }
            else
            {
                ViewBag.Burgers = _context.Burgers.ToList();
                ViewBag.Menus = _context.Menus.Include(m => m.Burger).ToList();
            }

            return View("Index");
        }
    }
}
