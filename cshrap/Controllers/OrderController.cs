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
                _logger.LogInformation("MyOrders - userId={UserId}", userId);

                //  DÉBOGAGE : Vérifier si ClientProfil existe
                var clientProfil = _context.ClientProfiles.Find(userId.Value);
                _logger.LogInformation("MyOrders - ClientProfil trouvé : {HasProfile}, Nom: {Nom}",
                    clientProfil != null, clientProfil?.Nom ?? "N/A");

                //  DÉBOGAGE : Compter TOUTES les commandes dans la base
                var totalOrders = _context.Orders.Count();
                _logger.LogInformation("MyOrders - Total commandes dans la base : {TotalOrders}", totalOrders);

                //  DÉBOGAGE : Afficher toutes les commandes avec leur client_profil_id
                var allOrdersDebug = _context.Orders
                    .Select(o => new { o.Id, o.ClientProfilId, o.StateOrder })
                    .Take(20)
                    .ToList();
                foreach (var od in allOrdersDebug)
                {
                    _logger.LogInformation("  → Order #{OrderId}, ClientProfilId={ClientProfilId}, State={State}",
                        od.Id, od.ClientProfilId, od.StateOrder);
                }

                // Charger les commandes de l'utilisateur courant
                var orders = _context.Orders
                    .Where(o => o.ClientProfilId == userId.Value)
                    .Include(o => o.Payement)
                    .OrderByDescending(o => o.CreatedAt)
                    .ToList();

                _logger.LogInformation("MyOrders - Found {Count} orders for userId={UserId}", orders.Count, userId);

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

                _logger.LogInformation("MyOrders - Loaded {LineCount} order lines", lines.Count);

                //  Passer le count total pour la vue
                ViewBag.TotalOrdersCount = totalOrders;

                return View(orders);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de l'affichage des commandes pour userId={UserId}", userId);
                ViewBag.LinesByOrder = new Dictionary<int, List<OrderLine>>();
                return View(new List<Order>());
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

                // Sauvegarder aussi les compléments s'il y en a
                if (selectedComplements.Count > 0)
                    _context.SaveChanges();

                _logger.LogInformation("Order created: orderId={OrderId}, clientProfilId={ClientProfilId}, total={Total}", order.Id, userId.Value, order.TotalPrix);

                // NE PAS simuler le paiement automatiquement - l'utilisateur doit le valider
                TempData["success"] = "Commande créée avec succès! Cliquez sur 'Payer maintenant' pour continuer.";
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

                // Ne pas afficher d'erreur à l'utilisateur, renvoyer vers les commandes
                return RedirectToAction("MyOrders");
            }
        }

        // ==========================
        // COMMANDES VALIDÉES / PAYÉES
        // ==========================
        public IActionResult ValidatedOrders()
        {
            var userId = GetCurrentUserId();
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            try
            {
                _logger.LogInformation("ValidatedOrders - userId={UserId}", userId);

                // Récupérer les commandes payées de l'utilisateur
                var orders = _context.Orders
                    .Include(o => o.Payement)
                    .Where(o => o.ClientProfilId == userId.Value)  // Filtrer par ClientProfilId directement
                    .Where(o => o.Payement != null && o.Payement.StatutPayement == "Valider")
                    .OrderByDescending(o => o.CreatedAt)
                    .ToList();

                _logger.LogInformation("ValidatedOrders - Found {Count} paid orders for userId={UserId}", orders.Count, userId);

                var orderIds = orders.Select(o => o.Id).ToList();
                var lines = _context.OrderLines
                    .Where(l => orderIds.Contains(l.OrderId))
                    .Include(l => l.Burger)
                    .Include(l => l.Menu)
                    .Include(l => l.Complement)
                    .ToList();

                ViewBag.LinesByOrder = lines
                    .GroupBy(l => l.OrderId)
                    .ToDictionary(g => g.Key, g => g.ToList());

                _logger.LogInformation("ValidatedOrders - Loaded {LineCount} order lines", lines.Count);

                return View("ValidatedOrders", orders);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de l'affichage des commandes validées pour userId={UserId}", userId);
                ViewBag.LinesByOrder = new Dictionary<int, List<OrderLine>>();
                return View("ValidatedOrders", new List<Order>());
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

        // DEBUG: Vérifier les données de l'utilisateur
        public IActionResult Debug()
        {
            var userId = GetCurrentUserId();
            var claimUserId = User.FindFirstValue("user_id");
            var claimNameId = User.FindFirstValue(ClaimTypes.NameIdentifier);
            var sessionUserId = HttpContext.Session.GetInt32("user_id");
            var userName = User?.Identity?.Name;
            var isAuth = User?.Identity?.IsAuthenticated ?? false;

            if (userId == null)
            {
                return Content($@"
<html><body style='font-family: monospace; padding: 20px;'>
<h2 style='color: red;'>❌ NON CONNECTÉ</h2>
<p><strong>User.Identity.Name:</strong> {userName ?? "NULL"}</p>
<p><strong>IsAuthenticated:</strong> {isAuth}</p>
<p><strong>Claim user_id:</strong> {claimUserId ?? "NULL"}</p>
<p><strong>Claim NameIdentifier:</strong> {claimNameId ?? "NULL"}</p>
<p><strong>Session user_id:</strong> {sessionUserId?.ToString() ?? "NULL"}</p>
<hr>
<a href='/Auth/Login'>Se connecter</a> | <a href='/Catalogue'>Catalogue</a>
</body></html>
", "text/html");
            }

            // Charger les données
            var user = _context.Users.Find(userId.Value);
            var clientProfil = _context.ClientProfiles.Find(userId.Value);

            // TOUTES les commandes dans la base (pour comparaison)
            var allOrders = _context.Orders.ToList();
            var allOrdersForUser = _context.Orders.Where(o => o.ClientProfilId == userId.Value).ToList();

            var orders = _context.Orders
                .Include(o => o.Payement)
                .Where(o => o.ClientProfilId == userId.Value)
                .ToList();

            var paidOrders = orders.Where(o => o.Payement != null && o.Payement.StatutPayement == "Valider").ToList();

            var debug = $@"
<html><body style='font-family: monospace; padding: 20px;'>
<h2>🔍 DEBUG INFO - {userName}</h2>

<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>
<h3>👤 Authentification</h3>
<p><strong>UserId (GetCurrentUserId):</strong> <code>{userId}</code></p>
<p><strong>User.Identity.Name:</strong> <code>{userName ?? "NULL"}</code></p>
<p><strong>IsAuthenticated:</strong> <code>{isAuth}</code></p>
<p><strong>Claim user_id:</strong> <code>{claimUserId ?? "NULL"}</code></p>
<p><strong>Claim NameIdentifier:</strong> <code>{claimNameId ?? "NULL"}</code></p>
<p><strong>Session user_id:</strong> <code>{sessionUserId?.ToString() ?? "NULL"}</code></p>
</div>

<div style='background: #fff3cd; padding: 10px; margin: 10px 0;'>
<h3>📋 Données Utilisateur</h3>
<p><strong>User dans DB:</strong> {(user != null ? $"ID={user.Id}, Login={user.Login}, Role={user.RoleUsers}" : "❌ NOT FOUND")}</p>
<p><strong>ClientProfil dans DB:</strong> {(clientProfil != null ? $"ID={clientProfil.Id}, Nom={clientProfil.Nom} {clientProfil.Prenom}" : "❌ NOT FOUND")}</p>
</div>

<div style='background: #d1ecf1; padding: 10px; margin: 10px 0;'>
<h3>📦 Commandes (Total dans la base: {allOrders.Count})</h3>
<p><strong>Commandes pour userId={userId}:</strong> {allOrdersForUser.Count}</p>
<p><strong>Commandes chargées (avec Payement):</strong> {orders.Count}</p>
<p><strong>Commandes payées:</strong> {paidOrders.Count}</p>
</div>

<h3>📋 Détail des commandes pour ClientProfilId={userId}</h3>
<table border='1' cellpadding='5' style='border-collapse: collapse;'>
<tr style='background: #333; color: white;'>
    <th>Order ID</th>
    <th>ClientProfilId</th>
    <th>StateOrder</th>
    <th>TotalPrix</th>
    <th>PayementId</th>
    <th>Statut Paiement</th>
    <th>CreatedAt</th>
</tr>
{string.Join("", orders.Select(o => $@"
<tr>
    <td>{o.Id}</td>
    <td>{o.ClientProfilId}</td>
    <td><strong>{o.StateOrder}</strong></td>
    <td>{o.TotalPrix} FCFA</td>
    <td>{o.PayementId?.ToString() ?? "NULL"}</td>
    <td>{(o.Payement != null ? o.Payement.StatutPayement : "Aucun")}</td>
    <td>{o.CreatedAt:dd/MM/yyyy HH:mm}</td>
</tr>
"))}
</table>

{(allOrdersForUser.Count == 0 ? @"
<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 20px 0;'>
<h3>⚠️ PROBLÈME DÉTECTÉ</h3>
<p>Aucune commande trouvée pour ClientProfilId={userId}</p>
<p>Vérifiez dans la base de données PostgreSQL que les commandes de 'mamou' ont bien <code>client_profil_id = {userId}</code></p>
</div>
" : "")}

<hr>
<p><a href='/Order/MyOrders'>← Retour à Mes Commandes</a></p>
</body></html>
";

            return Content(debug, "text/html");
        }

        private int? GetCurrentUserId()
        {
            var claimVal = User.FindFirstValue("user_id") ?? User.FindFirstValue(ClaimTypes.NameIdentifier);
            if (int.TryParse(claimVal, out var id)) return id;
            return HttpContext.Session.GetInt32("user_id");
        }
    }
}
