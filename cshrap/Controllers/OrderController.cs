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

            return View(orders);
        }

        // ==========================
        // CRÉER COMMANDE (burger ou menu)
        // ==========================
        [HttpGet]
        public IActionResult Create(int? itemId = null, string? itemType = null)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            ViewBag.Burgers = _context.Burgers.ToList();
            ViewBag.Menus = _context.Menus.ToList();
            ViewBag.Complements = _context.Complements.ToList();

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
                return RedirectToAction("Index", "Catalogue");

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
    }
}
