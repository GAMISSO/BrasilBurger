using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Data;
using Models;
using System;
using System.Linq;
using System.Collections.Generic;

namespace Controllers
{
    public class OrderController : Controller
    {
        private readonly ApplicationDbContext _context;

        public OrderController(ApplicationDbContext context)
        {
            _context = context;
        }

        // ==========================
        // MES COMMANDES
        // ==========================
        public IActionResult MyOrders()
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var orders = _context.Orders
                .Where(o => o.ClientProfilId == userId)
                .OrderByDescending(o => o.CreatedAt)
                .ToList();

            // Précharger les lignes associées pour affichage rapide
            var orderIds = orders.Select(o => o.Id).ToList();
            var lines = _context.OrderLines
                .Where(l => orderIds.Contains(l.OrderId))
                .Include(l => l.Burger)
                .Include(l => l.Menu)
                .ToList();

            var dict = lines.GroupBy(l => l.OrderId)
                .ToDictionary(g => g.Key, g => g.ToList());

            ViewBag.LinesByOrder = dict;

            return View(orders);
        }

        // ==========================
        // CRÉER COMMANDE (burger ou menu)
        // ==========================
        [HttpGet]
        public IActionResult Create(int? itemId = null, string? itemType = null, int[]? complementIds = null)
        {
            // Permettre l'accès à la page sans être connecté
            // La connexion sera demandée lors de la validation de la commande

            ViewBag.Burgers = _context.Burgers.ToList();
            ViewBag.Menus = _context.Menus.Include(m => m.Burger).Include(m => m.MenuComplements).ThenInclude(mc => mc.Complement).ToList();
            ViewBag.Complements = _context.Complements.ToList();
            ViewBag.Zones = _context.Zones.ToList();
            ViewBag.SelectedComplementIds = complementIds?.ToList() ?? new List<int>();

            // Charger l'item spécifique si fourni
            if (itemId.HasValue && !string.IsNullOrEmpty(itemType))
            {
                if (itemType.ToLower() == "burger")
                {
                    var burger = _context.Burgers.Find(itemId.Value);
                    ViewBag.Item = burger;
                    ViewBag.TypeItem = "burger";
                }
                else if (itemType.ToLower() == "menu")
                {
                    var menu = _context.Menus
                        .Include(m => m.Burger)
                        .Include(m => m.MenuComplements)
                        .ThenInclude(mc => mc.Complement)
                        .FirstOrDefault(m => m.Id == itemId.Value);
                    ViewBag.Item = menu;
                    ViewBag.TypeItem = "menu";
                }
            }

            ViewBag.PreselectedItemId = itemId;
            ViewBag.PreselectedItemType = itemType;

            return View();
        }
        [HttpPost]
        public IActionResult Create(
            string itemType,
            int itemId,
            int quantity,
            string typeLivraison,
            string? adresseLivraison,
            int[]? complementIds)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
            {
                // Stocker les données de la commande en session pour après la connexion
                HttpContext.Session.SetString("pending_order_itemType", itemType);
                HttpContext.Session.SetInt32("pending_order_itemId", itemId);
                HttpContext.Session.SetInt32("pending_order_quantity", quantity);
                HttpContext.Session.SetString("pending_order_typeLivraison", typeLivraison);
                if (!string.IsNullOrEmpty(adresseLivraison))
                    HttpContext.Session.SetString("pending_order_adresseLivraison", adresseLivraison);
                if (complementIds != null && complementIds.Length > 0)
                    HttpContext.Session.SetString("pending_order_complementIds", string.Join(",", complementIds));

                // Demander la connexion mais rester sur la page de commande
                TempData["message"] = "Veuillez vous connecter pour valider votre commande";
                return RedirectToAction("Create", new { itemId, itemType });
            }

            // Validate main item
            if (itemType != "Burger" && itemType != "Menu")
            {
                TempData["error"] = "Sélection invalide : choisissez un Burger ou un Menu.";
                return RedirectToAction("Index", "Catalogue");
            }

            int prixItem = 0;
            if (itemType == "Burger")
            {
                var burger = _context.Burgers.Find(itemId);
                if (burger == null) return NotFound();

                prixItem = burger.Prix;
            }
            else // Menu
            {
                var menu = _context.Menus.Find(itemId);
                if (menu == null) return NotFound();

                prixItem = menu.PrixTotal;
            }

            // Collect complements and compute their total price per item
            var selectedComplements = new List<Complement>();
            int complementsPrice = 0;
            if (complementIds != null && complementIds.Length > 0)
            {
                selectedComplements = _context.Complements
                    .Where(c => complementIds.Contains(c.Id)).ToList();
                complementsPrice = selectedComplements.Sum(c => c.Prix);
            }

            var order = new Order
            {
                StateOrder = "En_cours",
                TypeLivraison = typeLivraison,
                AdresseLivraison = adresseLivraison,
                TotalPrix = (prixItem + complementsPrice) * quantity,
                CreatedAt = DateTime.Now,
                ClientProfilId = userId
            };

            _context.Orders.Add(order);
            _context.SaveChanges();

            var orderLine = new OrderLine
            {
                OrderId = order.Id,
                ItemType = itemType,
                BurgerId = itemType == "Burger" ? itemId : null,
                MenuId = itemType == "Menu" ? itemId : null,
                Quantity = quantity,
                Prix = prixItem,
                CreatedAt = DateTime.Now
            };

            _context.OrderLines.Add(orderLine);
            _context.SaveChanges();

            // Add complement lines (one per selected complement, quantity applied)
            foreach (var comp in selectedComplements)
            {
                var compLine = new OrderLine
                {
                    OrderId = order.Id,
                    ItemType = "Complement",
                    ComplementId = comp.Id,
                    Quantity = quantity,
                    Prix = comp.Prix,
                    CreatedAt = DateTime.Now
                };
                _context.OrderLines.Add(compLine);
            }
            if (selectedComplements.Count > 0)
                _context.SaveChanges();

            return RedirectToAction("MyOrders");
        }

        // ==========================
        // DÉTAIL COMMANDE
        // ==========================
        public IActionResult Details(int id)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .Include(o => o.Payement)
                .FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId);

            if (order == null)
                return NotFound();

            var lines = _context.OrderLines
                .Where(l => l.OrderId == id)
                .Include(l => l.Burger)
                .Include(l => l.Menu)
                .Include(l => l.Complement)
                .ToList();

            ViewBag.Lines = lines;

            return View(order);
        }

        // ==========================
        // ANNULER COMMANDE
        // ==========================
        public IActionResult Cancel(int id)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId);

            if (order == null)
                return NotFound();

            if (order.StateOrder != "En_cours")
            {
                TempData["error"] = "Impossible d’annuler cette commande";
                return RedirectToAction("MyOrders");
            }

            order.StateOrder = "Annulee";
            _context.SaveChanges();

            return RedirectToAction("MyOrders");
        }

        // ==========================
        // RECOMMANDER (dupliquer une commande existante)
        // ==========================
        public IActionResult Reorder(int id)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var original = _context.Orders.FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId);
            if (original == null)
                return NotFound();

            var originalLines = _context.OrderLines.Where(l => l.OrderId == id).ToList();

            var newOrder = new Order
            {
                StateOrder = "En_cours",
                TypeLivraison = original.TypeLivraison,
                AdresseLivraison = original.AdresseLivraison,
                TotalPrix = original.TotalPrix,
                CreatedAt = DateTime.Now,
                ClientProfilId = userId
            };

            _context.Orders.Add(newOrder);
            _context.SaveChanges();

            foreach (var ln in originalLines)
            {
                var clone = new OrderLine
                {
                    OrderId = newOrder.Id,
                    ItemType = ln.ItemType,
                    BurgerId = ln.BurgerId,
                    MenuId = ln.MenuId,
                    ComplementId = ln.ComplementId,
                    Quantity = ln.Quantity,
                    Prix = ln.Prix,
                    CreatedAt = DateTime.Now
                };
                _context.OrderLines.Add(clone);
            }
            _context.SaveChanges();

            TempData["success"] = "Commande ré-enregistrée avec succès.";
            return RedirectToAction("MyOrders");
        }
    }
}
