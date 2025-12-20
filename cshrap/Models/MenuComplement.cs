using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("menu_complement")]
    public class MenuComplement
    {
        [Column("menu_id")]
        public int MenuId { get; set; }

        [Column("complement_id")]
        public int ComplementId { get; set; }

        // Navigations
        public Menu Menu { get; set; } = null!;
        public Complement Complement { get; set; } = null!;
    }
}
