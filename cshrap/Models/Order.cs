using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("order_table")]
public class Order
{
    [Column("id")]
    public int Id { get; set; }

    [Column("state_order")]
    public string StateOrder { get; set; } = null!;

    [Column("type_livraison")]
    public string TypeLivraison { get; set; } = null!;

    [Column("adresse_livraison")]
    public string? AdresseLivraison { get; set; }

    [Column("total_prix")]
    public int TotalPrix { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [Column("updated_at")]
    public DateTime? UpdatedAt { get; set; }

    [Column("client_profil_id")]
    public int? ClientProfilId { get; set; }

    [Column("zone_id")]
    public int? ZoneId { get; set; }

    // Navigation
    public ClientProfil? ClientProfil { get; set; }
    [Column("payement_id")]
    public int? PayementId { get; set; }

    public Payement Payement { get; set; } = null!;
}
}