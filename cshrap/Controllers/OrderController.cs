using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Data;
using Models;
using System;
using System.Linq;
using System.Collections.Generic;
using System.Security.Claims;

namespace Controllers
{
    public class OrderController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly Microsoft.Extensions.Logging.ILogger<OrderController> _logger;

        public OrderController(ApplicationDbContext context, Microsoft.Extensions.Logging.ILogger<OrderController> logger)
        {
            _context = context;
            _logger = logger;
        }

        // ==========================
        // MES COMMANDES
        // ==========================
        public IActionResult MyOrders()
        {
            var userId = GetCurrentUserId();
            if (userId == null)
            {
                TempData["error"] = "Veuillez vous connecter pour voir vos commandes";
                return RedirectToAction("Index", "Catalogue");
            }

            try
            {
                var orders = _context.Orders
                    .Where(o => o.ClientProfilId == userId.Value)
                    .OrderByDescending(o => o.CreatedAt)
                    .ToList();

                // Précharger les lignes associées pour affichage rapide
                var orderIds = orders.Select(o => o.Id).ToList();
                var lines = _context.OrderLines
                    .Where(l => orderIds.Contains(l.OrderId))
                    .Include(l => l.Burger)
                    .Include(l => l.Menu)
                    .Include(l => l.Complement)
                    .ToList();

                var dict = lines.GroupBy(l => l.OrderId)
                    .ToDictionary(g => g.Key, g => g.ToList());

                ViewBag.LinesByOrder = dict;

                return View(orders);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de l'affichage des commandes pour userId={UserId}", userId);
                TempData["error"] = "Impossible de charger vos commandes pour le moment.";
                return RedirectToAction("Index", "Catalogue");
            }
        }

        // ==========================
        // CRÉER COMMANDE (burger ou menu)
        // ==========================
        [HttpGet]
        public IActionResult Create(int? itemId = null, string? itemType = null, int[]? complementIds = null)
        {
            try
            {
                // Permettre l'accès à la page sans être connecté
                // La connexion sera demandée lors de la validation de la commande

                ViewBag.Burgers = _context.Burgers.ToList();
                ViewBag.Menus = _context.Menus.Include(m => m.Burger).Include(m => m.MenuComplements).ThenInclude(mc => mc.Complement).ToList();
                ViewBag.Complements = _context.Complements.ToList();
                // Charger zones si disponibles, sinon ignorer
                try { ViewBag.Zones = _context.Zones.ToList(); } catch (Exception ex) { _logger.LogWarning(ex, "Chargement des zones impossible"); ViewBag.Zones = new List<Zone>(); }
                ViewBag.SelectedComplementIds = complementIds?.ToList() ?? new List<int>();

                // Charger l'item spécifique si fourni
                if (itemId.HasValue && !string.IsNullOrEmpty(itemType))
                {
                    if (itemType.ToLower() == "burger")
                    {
                        var burger = _context.Burgers.Find(itemId.Value);
                        if (burger == null) { TempData["error"] = "Burger introuvable"; return RedirectToAction("Index", "Catalogue"); }
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
                        if (menu == null) { TempData["error"] = "Menu introuvable"; return RedirectToAction("Index", "Catalogue"); }
                        ViewBag.Item = menu;
                        ViewBag.TypeItem = "menu";
                    }
                    else
                    {
                        TempData["error"] = "Type d'article invalide";
                        return RedirectToAction("Index", "Catalogue");
                    }
                }

                ViewBag.PreselectedItemId = itemId;
                ViewBag.PreselectedItemType = itemType;

                return View();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de l'accès à la page de commande (Create GET) itemId={ItemId} itemType={ItemType}", itemId, itemType);
                TempData["error"] = "Impossible d'accéder à la page de commande pour le moment.";
                return RedirectToAction("Index", "Catalogue");
            }
        }
        [HttpPost]
        public IActionResult Create(
            string itemType,
            int itemId,
            int quantity,
            string typeLivraison,
            string? adresseLivraison,
            int[]? complementIds,
            int[]? complementQuantities)
        {
            try
            {
                var userId = GetCurrentUserId();
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
                    if (complementQuantities != null && complementQuantities.Length > 0)
                        HttpContext.Session.SetString("pending_order_complementQuantities", string.Join(",", complementQuantities));

                    // Demander la connexion mais rester sur la page de commande
                    TempData["message"] = "Veuillez vous connecter pour valider votre commande";
                    return RedirectToAction("Create", new { itemId, itemType });
                }

                // Vérifier si l'utilisateur a un ClientProfil, sinon en créer un
                var clientProfil = _context.ClientProfiles.Find(userId.Value);
                if (clientProfil == null)
                {
                    // Récupérer les infos de l'utilisateur
                    var user = _context.Users.Find(userId.Value);
                    if (user == null)
                    {
                        TempData["error"] = "Utilisateur introuvable. Veuillez vous reconnecter.";
                        return RedirectToAction("Index", "Catalogue");
                    }

                    // Créer un ClientProfil de base
                    clientProfil = new ClientProfil
                    {
                        Id = userId.Value,
                        Nom = user.Login,
                        Prenom = "",
                        Adresse = adresseLivraison ?? "",
                        Telephone = "",
                        Email = "",
                        CreatedAt = DateTime.Now
                    };
                    _context.ClientProfiles.Add(clientProfil);
                    _context.SaveChanges();
                    _logger.LogInformation("ClientProfil créé automatiquement pour userId={UserId}", userId.Value);
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

                // Collect complements and compute their total price (independent of main quantity)
                var selectedComplements = new List<(Complement Comp, int Qty)>();
                int complementsTotal = 0;
                if (complementIds != null && complementIds.Length > 0)
                {
                    // Safely align quantities with ids
                    var quantities = complementQuantities ?? Array.Empty<int>();
                    var comps = _context.Complements.Where(c => complementIds.Contains(c.Id)).ToList();
                    for (int i = 0; i < complementIds.Length; i++)
                    {
                        var comp = comps.FirstOrDefault(c => c.Id == complementIds[i]);
                        if (comp == null) continue;
                        var qty = (i < quantities.Length ? quantities[i] : 1);
                        if (qty < 1) qty = 1;
                        selectedComplements.Add((comp, qty));
                        complementsTotal += comp.Prix * qty;
                    }
                }

                var order = new Order
                {
                    StateOrder = "En_cours",
                    TypeLivraison = typeLivraison,
                    AdresseLivraison = adresseLivraison,
                    TotalPrix = (prixItem * quantity) + complementsTotal,
                    CreatedAt = DateTime.Now,
                    ClientProfilId = userId.Value
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
                    Prix = prixItem * quantity,
                    CreatedAt = DateTime.Now
                };

                _context.OrderLines.Add(orderLine);
                _context.SaveChanges();

                // Add complement lines with their own quantities
                foreach (var compTuple in selectedComplements)
                {
                    var compLine = new OrderLine
                    {
                        OrderId = order.Id,
                        ItemType = "Complement",
                        ComplementId = compTuple.Comp.Id,
                        Quantity = compTuple.Qty,
                        Prix = compTuple.Comp.Prix * compTuple.Qty,
                        CreatedAt = DateTime.Now
                    };
                    _context.OrderLines.Add(compLine);
                }
                if (selectedComplements.Count > 0)
                    _context.SaveChanges();

                TempData["success"] = "Commande validée avec succès!";
                return RedirectToAction("MyOrders");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de la validation de la commande (Create POST) itemType={ItemType} itemId={ItemId} userId={UserId}", itemType, itemId, GetCurrentUserId());

                // Extraire l'erreur interne pour plus de détails
                var innerException = ex.InnerException;
                var errorDetails = ex.Message;
                while (innerException != null)
                {
                    errorDetails += " | Inner: " + innerException.Message;
                    innerException = innerException.InnerException;
                }
                _logger.LogError("Détails complets: {ErrorDetails}", errorDetails);

                TempData["error"] = $"Impossible de valider la commande: {ex.Message}";
                return RedirectToAction("Create", new { itemId, itemType });
            }
        }

        // ==========================
        // DÉTAIL COMMANDE
        // ==========================
        public IActionResult Details(int id)
        {
            var userId = GetCurrentUserId();
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .Include(o => o.Payement)
                .FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId.Value);

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
            var userId = GetCurrentUserId();
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId.Value);

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
            var userId = GetCurrentUserId();
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var original = _context.Orders.FirstOrDefault(o => o.Id == id && o.ClientProfilId == userId.Value);
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
                ClientProfilId = userId.Value
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

        private int? GetCurrentUserId()
        {
            var claimVal = User.FindFirstValue("user_id") ?? User.FindFirstValue(ClaimTypes.NameIdentifier);
            if (int.TryParse(claimVal, out var id)) return id;
            return HttpContext.Session.GetInt32("user_id");
        }
    }
}
