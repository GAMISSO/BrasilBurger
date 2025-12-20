using Data;
using Models;
using System;

namespace Services.Impl
{
    public class PayementService : IPayementService
    {
        private readonly ApplicationDbContext _context;

        public PayementService(ApplicationDbContext context)
        {
            _context = context;
        }

        public Payement Pay(int orderId, string methode)
        {
            var order = _context.Orders.Find(orderId);

            var payement = new Payement
            {
                OrderId = orderId,
                MethodePayement = methode,
                Montant = order.TotalPrix,
                TransactionRef = Guid.NewGuid().ToString(),
                StatutPayement = "Valider",
                CreatedAt = DateTime.Now
            };

            _context.Payements.Add(payement);
            _context.SaveChanges();

            order.PayementId = payement.Id;
            order.StateOrder = "Terminee";

            _context.SaveChanges();

            return payement;
        }
    }
}
