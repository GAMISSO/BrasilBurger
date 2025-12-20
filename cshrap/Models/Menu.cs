using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("menu")]
    public class Menu
    {
        [Column("id")]
        public int Id { get; set; }

        [Column("nom")]
        public string Nom { get; set; } = null!;

        [Column("burger_id")]
        public int? BurgerId { get; set; }   // 👈 EXACT

        [Column("prix_total")]
        public int PrixTotal { get; set; }

        [Column("created_at")]
        public DateTime CreatedAt { get; set; }

        [Column("image")]
        public string? Image { get; set; }

        // Navigation
        public Burger? Burger { get; set; }
        public ICollection<MenuComplement> MenuComplements { get; set; } = new List<MenuComplement>();
    }
}