using Data;
using Models;
using System;
using System.Collections.Generic;
using System.Linq;

namespace Services.Impl
{
    public class OrderService : IOrderService
    {
        private readonly ApplicationDbContext _context;

        public OrderService(ApplicationDbContext context)
        {
            _context = context;
        }

        public Order CreateOrder(
            int clientId,
            string itemType,
            int itemId,
            int quantity,
            string typeLivraison,
            string adresseLivraison)
        {
            int prix = 0;

            if (itemType == "Burger")
            {
                var burger = _context.Burgers.Find(itemId);
                if (burger == null) throw new InvalidOperationException($"Burger {itemId} not found");
                prix = burger.Prix;
            }

            if (itemType == "Menu")
            {
                var menu = _context.Menus.Find(itemId);
                if (menu == null) throw new InvalidOperationException($"Menu {itemId} not found");
                prix = menu.PrixTotal;
            }

            var order = new Order
            {
                ClientProfilId = clientId,
                StateOrder = "En_cours",
                TypeLivraison = typeLivraison,
                AdresseLivraison = adresseLivraison,
                TotalPrix = prix * quantity,
                CreatedAt = DateTime.Now
            };

            _context.Orders.Add(order);
            _context.SaveChanges();

            var line = new OrderLine
            {
                OrderId = order.Id,
                ItemType = itemType,
                BurgerId = itemType == "Burger" ? itemId : null,
                MenuId = itemType == "Menu" ? itemId : null,
                Quantity = quantity,
                Prix = prix,
                CreatedAt = DateTime.Now
            };

            _context.OrderLines.Add(line);
            _context.SaveChanges();

            return order;
        }

        public List<Order> GetClientOrders(int clientId)
            => _context.Orders
                .Where(o => o.ClientProfilId == clientId)
                .ToList();

        public Order? GetOrderDetails(int orderId, int clientId)
            => _context.Orders
                .FirstOrDefault(o => o.Id == orderId && o.ClientProfilId == clientId);
    }
}
