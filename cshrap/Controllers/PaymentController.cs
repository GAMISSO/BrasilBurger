using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Data;
using Models;
using System;
using System.Linq;

namespace Controllers
{
    public class PaymentController : Controller
    {
        private readonly ApplicationDbContext _context;

        public PaymentController(ApplicationDbContext context)
        {
            _context = context;
        }

        // ==========================
        // PAGE PAIEMENT
        // ==========================
        public IActionResult Pay(int orderId)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .Include(o => o.ClientProfil)
                .FirstOrDefault(o => o.Id == orderId && o.ClientProfilId == userId);

            if (order == null)
                return NotFound();

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
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var order = _context.Orders
                .FirstOrDefault(o => o.Id == orderId && o.ClientProfilId == userId);

            if (order == null)
                return NotFound();

            if (order.StateOrder != "En_cours")
            {
                TempData["error"] = "Commande non payable";
                return RedirectToAction("MyOrders", "Order");
            }

            // ==========================
            // SIMULATION DU PAIEMENT
            // ==========================
            var payement = new Payement
            {
                MethodePayement = methodePayement, // Wave | Orange_Money
                Montant = order.TotalPrix,
                TransactionRef = Guid.NewGuid().ToString(),
                StatutPayement = "Valider",
                CreatedAt = DateTime.Now,
                OrderId = order.Id
            };

            _context.Payements.Add(payement);
            _context.SaveChanges();

            // Lier le paiement à la commande
            order.PayementId = payement.Id;
            order.StateOrder = "Terminee";

            _context.SaveChanges();

            TempData["success"] = "Paiement effectué avec succès ! Votre commande a été validée.";
            return RedirectToAction("MyOrders", "Order");
        }
    }
}
