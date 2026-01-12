using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Data;
using Models;
using System;
using System.Linq;
using System.Security.Claims;
using Microsoft.Extensions.Logging;

namespace Controllers
{
    public class PaymentController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly ILogger<PaymentController> _logger;

        public PaymentController(ApplicationDbContext context, ILogger<PaymentController> logger)
        {
            _context = context;
            _logger = logger;
        }

        // ==========================
        // OBTENIR L'ID DE L'UTILISATEUR
        // ==========================
        private int? GetCurrentUserId()
        {
            var claimVal = User.FindFirstValue("user_id") ?? User.FindFirstValue(ClaimTypes.NameIdentifier);
            if (int.TryParse(claimVal, out var id)) return id;
            return HttpContext.Session.GetInt32("user_id");
        }

        // ==========================
        // PAGE PAIEMENT
        // ==========================
        public IActionResult Pay(int orderId)
        {
            var userId = GetCurrentUserId();
            if (userId == null)
            {
                TempData["error"] = "Veuillez vous connecter pour accéder à cette page";
                return RedirectToAction("Index", "Catalogue");
            }

            var order = _context.Orders
                .FirstOrDefault(o => o.Id == orderId && o.ClientProfilId == userId.Value);

            if (order == null)
            {
                TempData["error"] = "Commande introuvable";
                return RedirectToAction("MyOrders", "Order");
            }

            _logger.LogInformation("Pay - Affichage page paiement pour orderId={OrderId}, userId={UserId}", orderId, userId);

            return View(order);
        }

        // ==========================
        // TRAITEMENT PAIEMENT
        // ==========================
        [HttpPost]
        public IActionResult Process(
            int orderId,
            string methodePayement)
        {
            var userId = GetCurrentUserId();
            if (userId == null)
            {
                TempData["error"] = "Veuillez vous connecter pour payer";
                return RedirectToAction("Index", "Catalogue");
            }

            var order = _context.Orders
                .FirstOrDefault(o => o.Id == orderId && o.ClientProfilId == userId.Value);

            if (order == null)
            {
                TempData["error"] = "Commande introuvable";
                return RedirectToAction("MyOrders", "Order");
            }

            if (order.StateOrder != "En_cours")
            {
                TempData["error"] = "Commande non payable";
                return RedirectToAction("MyOrders", "Order");
            }

            // ==========================
            // SIMULATION DU PAIEMENT
            // ==========================
            try
            {
                var payement = new Payement
                {
                    MethodePayement = methodePayement == "OM" ? "Orange_Money" : "Wave",
                    Montant = order.TotalPrix,
                    TransactionRef = "SIM-" + Guid.NewGuid().ToString("N").Substring(0, 8),
                    StatutPayement = "Valider",
                    CreatedAt = DateTime.Now,
                    OrderId = order.Id
                };

                _context.Payements.Add(payement);
                _context.SaveChanges();

                _logger.LogInformation("Payement créé: id={PayementId}, orderId={OrderId}, montant={Montant}", payement.Id, order.Id, payement.Montant);

                // Lier le paiement à la commande
                order.PayementId = payement.Id;
                order.StateOrder = "Terminee";

                _context.SaveChanges();

                _logger.LogInformation("Order mise à jour: orderId={OrderId}, state=Terminee, payementId={PayementId}", order.Id, payement.Id);

                TempData["success"] = "✓ Paiement effectué avec succès ! Votre commande a été validée.";
                return RedirectToAction("MyOrders", "Order");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors du paiement: orderId={OrderId}, userId={UserId}", orderId, userId);
                TempData["error"] = "Une erreur est survenue lors du paiement. Veuillez réessayer.";
                return RedirectToAction("Pay", new { orderId });
            }
        }
    }
}
