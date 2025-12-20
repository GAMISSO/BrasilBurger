using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("complement")]
    public class Complement
    {
        [Column("id")]
        public int Id { get; set; }

        [Column("nom")]
        public string Nom { get; set; } = null!;

        [Column("prix")]
        public int Prix { get; set; }

        [Column("type_complement")]
        public string TypeComplement { get; set; } = null!;

        [Column("created_at")]
        public DateTime CreatedAt { get; set; }

        [Column("image")]
        public string? Image { get; set; }
        public ICollection<MenuComplement> MenuComplements { get; set; } = new List<MenuComplement>();
    }
}