using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Http;
using Data;
using Models;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System;

namespace Controllers
{
    public class AuthController : Controller
    {
        private readonly ApplicationDbContext _context;

        public AuthController(ApplicationDbContext context)
        {
            _context = context;
        }

        // =====================
        // LOGIN
        // =====================
        [HttpPost]
        public IActionResult Login(string login, string password, string? returnUrl)
        {
            string hash = HashPassword(password);

            var user = _context.Users
                .FirstOrDefault(u => u.Login == login && u.PasswordHash == hash);

            if (user == null)
            {
                TempData["error"] = "Login ou mot de passe incorrect";
                return RedirectToAction("Index", "Catalogue");
            }

            HttpContext.Session.SetInt32("user_id", user.Id);
            HttpContext.Session.SetString("role", user.RoleUsers);

            if (!string.IsNullOrWhiteSpace(returnUrl) && Url.IsLocalUrl(returnUrl))
                return Redirect(returnUrl);

            return RedirectToAction("Index", "Catalogue");
        }

        // =====================
        // INSCRIPTION CLIENT
        // =====================
        [HttpPost]
        public IActionResult Register(
            string login,
            string password,
            string nom,
            string prenom,
            string telephone,
            string email,
            string adresse,
            string? returnUrl)
        {
            // Vérifier login unique
            if (_context.Users.Any(u => u.Login == login))
            {
                TempData["error"] = "Login déjà utilisé";
                return RedirectToAction("Index", "Catalogue");
            }

            // 1️⃣ Créer User
            var user = new User
            {
                Login = login,
                PasswordHash = HashPassword(password),
                RoleUsers = "Client"
            };

            _context.Users.Add(user);
            _context.SaveChanges();

            // 2️⃣ Créer ClientProfil (1–1)
            var clientProfil = new ClientProfil
            {
                Id = user.Id, // 👈 FK + PK
                Nom = nom,
                Prenom = prenom,
                Telephone = telephone,
                Email = email,
                Adresse = adresse,
                CreatedAt = DateTime.Now
            };

            _context.ClientProfiles.Add(clientProfil);
            _context.SaveChanges();

            // Auto login
            HttpContext.Session.SetInt32("user_id", user.Id);
            HttpContext.Session.SetString("role", "Client");

            if (!string.IsNullOrWhiteSpace(returnUrl) && Url.IsLocalUrl(returnUrl))
                return Redirect(returnUrl);

            return RedirectToAction("Index", "Catalogue");
        }

        // =====================
        // LOGOUT
        // =====================
        public IActionResult Logout()
        {
            HttpContext.Session.Clear();
            return RedirectToAction("Index", "Catalogue");
        }

        // =====================
        // HASH PASSWORD
        // =====================
        private string HashPassword(string password)
        {
            using var sha = SHA256.Create();
            var bytes = sha.ComputeHash(Encoding.UTF8.GetBytes(password));
            return Convert.ToBase64String(bytes);
        }
    }
}
