using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Authentication;
using Microsoft.AspNetCore.Authentication.Cookies;
using Data;
using Models;
using System.Linq;
using System.Text;
using System;
using System.Security.Claims;

namespace Controllers
{
    public class AuthController : Controller
    {
        private readonly ApplicationDbContext _context;
        private readonly Microsoft.Extensions.Logging.ILogger<AuthController> _logger;

        public AuthController(ApplicationDbContext context, Microsoft.Extensions.Logging.ILogger<AuthController> logger)
        {
            _context = context;
            _logger = logger;
        }

        // =====================
        // LOGIN
        // =====================
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Login(string login, string password, string? returnUrl)
        {
            try
            {
                var user = _context.Users
                    .FirstOrDefault(u => u.Login == login && u.PasswordHash == password);

                if (user == null)
                {
                    TempData["error"] = "Login ou mot de passe incorrect";
                    return RedirectToAction("Index", "Catalogue");
                }

                // Create claims for the authenticated user
                var claims = new List<Claim>
                {
                    new Claim(ClaimTypes.NameIdentifier, user.Id.ToString()),
                    new Claim(ClaimTypes.Name, user.Login),
                    new Claim(ClaimTypes.Role, user.RoleUsers),
                    new Claim("user_id", user.Id.ToString())
                };

                var identity = new ClaimsIdentity(claims, "BrasilBurgerAuth");
                var principal = new ClaimsPrincipal(identity);

                // Sign in with cookie authentication
                await HttpContext.SignInAsync(
                    "BrasilBurgerAuth",
                    principal,
                    new AuthenticationProperties
                    {
                        IsPersistent = true,
                        ExpiresUtc = DateTimeOffset.UtcNow.AddDays(7)
                    });

                // Also set session for compatibility
                HttpContext.Session.SetInt32("user_id", user.Id);
                HttpContext.Session.SetString("role", user.RoleUsers);

                if (!string.IsNullOrWhiteSpace(returnUrl) && Url.IsLocalUrl(returnUrl))
                    return Redirect(returnUrl);

                return RedirectToAction("Index", "Catalogue");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors du login pour {Login}", login);
                TempData["error"] = "Une erreur est survenue lors de la connexion.";
                return RedirectToAction("Index", "Catalogue");
            }
        }

        // =====================
        // INSCRIPTION CLIENT
        // =====================
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Register(
            string login,
            string password,
            string nom,
            string prenom,
            string telephone,
            string email,
            string adresse,
            string? returnUrl)
        {
            try
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
                    PasswordHash = password,
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

                // Auto login with cookie authentication
                var claims = new List<Claim>
                {
                    new Claim(ClaimTypes.NameIdentifier, user.Id.ToString()),
                    new Claim(ClaimTypes.Name, user.Login),
                    new Claim(ClaimTypes.Role, "Client"),
                    new Claim("user_id", user.Id.ToString())
                };

                var identity = new ClaimsIdentity(claims, "BrasilBurgerAuth");
                var principal = new ClaimsPrincipal(identity);

                // Sign in with cookie authentication
                await HttpContext.SignInAsync(
                    "BrasilBurgerAuth",
                    principal,
                    new AuthenticationProperties
                    {
                        IsPersistent = true,
                        ExpiresUtc = DateTimeOffset.UtcNow.AddDays(7)
                    });

                // Also set session for compatibility
                HttpContext.Session.SetInt32("user_id", user.Id);
                HttpContext.Session.SetString("role", "Client");

                if (!string.IsNullOrWhiteSpace(returnUrl) && Url.IsLocalUrl(returnUrl))
                    return Redirect(returnUrl);

                return RedirectToAction("Index", "Catalogue");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Erreur lors de l'inscription pour {Login}", login);
                TempData["error"] = "Une erreur est survenue lors de l'inscription.";
                return RedirectToAction("Index", "Catalogue");
            }
        }

        // =====================
        // PROFIL UTILISATEUR
        // =====================
        public IActionResult Profile()
        {
            var userIdStr = User.FindFirstValue("user_id") ?? User.FindFirstValue(ClaimTypes.NameIdentifier);
            if (!int.TryParse(userIdStr, out var userId))
                return RedirectToAction("Index", "Catalogue");

            var user = _context.Users.Find(userId);
            var profil = _context.ClientProfiles.Find(userId);

            ViewBag.User = user;
            ViewBag.Profile = profil;

            return View();
        }

        // =====================
        // LOGOUT
        // =====================
        public async Task<IActionResult> Logout()
        {
            await HttpContext.SignOutAsync("BrasilBurgerAuth");
            HttpContext.Session.Clear();
            return RedirectToAction("Index", "Catalogue");
        }
    }
}
