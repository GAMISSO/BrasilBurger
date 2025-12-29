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
        private readonly Microsoft.Extensions.Logging.ILogger<CatalogueController> _logger;

        public CatalogueController(ApplicationDbContext context, Microsoft.Extensions.Logging.ILogger<CatalogueController> logger)
        {
            _context = context;
            _logger = logger;
        }

        // ==========================
        // PAGE CATALOGUE GÉNÉRALE
        // ==========================
        public IActionResult Index(string? filtre = null)
        {
            // Initialiser avec des listes vides par défaut
            ViewBag.Burgers = new List<Burger>();
            ViewBag.Menus = new List<Menu>();
            ViewBag.Complements = new List<Complement>();

            // Si pas de filtre ou filtre = "tous", afficher tout
            if (string.IsNullOrWhiteSpace(filtre) || filtre == "tous")
            {
                ViewBag.Burgers = _context.Burgers.ToList();
                ViewBag.Menus = _context.Menus
                                        .Include(m => m.Burger)
                                        .Include(m => m.MenuComplements)
                                        .ThenInclude(mc => mc.Complement)
                                        .ToList();
                ViewBag.Complements = _context.Complements.ToList();
                ViewBag.CurrentFilter = "tous";
            }
            // Filtre burger
            else if (filtre == "burger")
            {
                ViewBag.Burgers = _context.Burgers.ToList();
                ViewBag.CurrentFilter = "burger";
            }
            // Filtre menu
            else if (filtre == "menu")
            {
                ViewBag.Menus = _context.Menus
                                        .Include(m => m.Burger)
                                        .Include(m => m.MenuComplements)
                                        .ThenInclude(mc => mc.Complement)
                                        .ToList();
                ViewBag.CurrentFilter = "menu";
            }

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

            ViewBag.Complements = _context.Complements.ToList();

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
            try
            {
                // Default: empty
                ViewBag.Burgers = new List<Burger>();
                ViewBag.Menus = new List<Menu>();

                if (string.IsNullOrWhiteSpace(type) || type == "all")
                {
                    ViewBag.Burgers = _context.Burgers.ToList();
                    ViewBag.Menus = _context.Menus.Include(m => m.Burger).ToList();
                    ViewBag.Complements = _context.Complements.ToList();
                }
                else if (type == "burger")
                {
                    ViewBag.Burgers = _context.Burgers.ToList();
                    ViewBag.Complements = new List<Complement>();
                }
                else if (type == "menu")
                {
                    ViewBag.Menus = _context.Menus.Include(m => m.Burger).ToList();
                    ViewBag.Complements = new List<Complement>();
                }
                else
                {
                    // Treat other types as complement categories (boisson, frite, ...)
                    ViewBag.Complements = _context.Complements
                        .Where(c => (c.TypeComplement ?? string.Empty).ToLower() == (type ?? string.Empty).ToLower())
                        .ToList();
                }

                ViewBag.CurrentFilter = type ?? "all";

                return View("Index");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors du filtrage du catalogue avec type={Type}", type);
                TempData["error"] = "Impossible d'appliquer le filtre, veuillez réessayer.";
                return RedirectToAction("Index");
            }
        }
    }
}
