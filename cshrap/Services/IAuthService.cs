using Models;

namespace Services
{
    public interface IAuthService
    {
        User Login(string login, string password);
        ClientProfil Register(ClientProfil client);
    }
}
