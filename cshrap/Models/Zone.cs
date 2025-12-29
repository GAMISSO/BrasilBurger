using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("zone")]
    public class Zone
    {
        [Column("id")]
        public int id { get; set; }

        [Column("nom")]
        public string nom { get; set; } = string.Empty;

        [Column("prix_zone")]
        public int prix_zone { get; set; }

        [Column("created_at")]
        public DateTime created_at { get; set; }
    }
}