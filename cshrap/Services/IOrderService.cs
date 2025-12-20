using Models;
using System.Collections.Generic;

namespace Services
{
    public interface IOrderService
    {
        Order CreateOrder(
            int clientId,
            string itemType,
            int itemId,
            int quantity,
            string typeLivraison,
            string adresseLivraison
        );

        List<Order> GetClientOrders(int clientId);
        Order GetOrderDetails(int orderId, int clientId);
    }
}
