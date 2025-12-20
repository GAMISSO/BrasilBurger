using Models;

namespace Services
{
    public interface IPayementService
    {
        Payement Pay(int orderId, string methode);
    }
}
