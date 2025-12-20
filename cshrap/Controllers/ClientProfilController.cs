using Microsoft.AspNetCore.Mvc;
using Data;
using Models;
using System.Linq;

namespace Controllers
{
    public class ClientProfileController : Controller
    {
        private readonly ApplicationDbContext _context;

        public ClientProfileController(ApplicationDbContext context)
        {
            _context = context;
        }

        // ==========================
        // AFFICHER PROFIL CLIENT
        // ==========================
        public IActionResult Index()
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var client = _context.ClientProfiles
                .FirstOrDefault(c => c.Id == userId);

            if (client == null)
                return NotFound();

            return View(client);
        }

        // ==========================
        // PAGE MODIFICATION PROFIL
        // ==========================
        public IActionResult Edit()
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            var client = _context.ClientProfiles
                .FirstOrDefault(c => c.Id == userId);

            if (client == null)
                return NotFound();

            return View(client);
        }

        // ==========================
        // SAUVEGARDE MODIFICATION
        // ==========================
        [HttpPost]
        public IActionResult Edit(ClientProfil model)
        {
            var userId = HttpContext.Session.GetInt32("user_id");
            if (userId == null)
                return RedirectToAction("Index", "Catalogue");

            if (!ModelState.IsValid)
                return View(model);

            var client = _context.ClientProfiles
                .FirstOrDefault(c => c.Id == userId);

            if (client == null)
                return NotFound();

            client.Nom = model.Nom;
            client.Prenom = model.Prenom;
            client.Telephone = model.Telephone;
            client.Adresse = model.Adresse;
            client.Email = model.Email;

            _context.SaveChanges();

            TempData["success"] = "Profil mis à jour avec succès";
            return RedirectToAction("Index");
        }
    }
}
