using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("burger")]
public class Burger
{
    [Column("id")]
    public int Id { get; set; }

    [Column("nom")]
    public string Nom { get; set; } = null!;

    [Column("prix")]
    public int Prix { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [Column("image_url")]
    public string? ImageUrl { get; set; }
}
}