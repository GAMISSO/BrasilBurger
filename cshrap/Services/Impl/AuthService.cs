using Data;
using Models;
using System.Linq;
using System.Security.Cryptography;
using System.Text;

namespace Services.Impl
{
    public class AuthService : IAuthService
    {
        private readonly ApplicationDbContext _context;

        public AuthService(ApplicationDbContext context)
        {
            _context = context;
        }

        public User? Login(string login, string password)
        {
            string hash = HashPassword(password);

            return _context.Users
                .FirstOrDefault(u => u.Login == login && u.PasswordHash == hash);
        }

        public ClientProfil Register(ClientProfil client)
        {
            _context.ClientProfiles.Add(client);
            _context.SaveChanges();
            return client;
        }

        private string HashPassword(string password)
        {
            using var sha = SHA256.Create();
            return Convert.ToBase64String(
                sha.ComputeHash(Encoding.UTF8.GetBytes(password))
            );
        }
    }
}
