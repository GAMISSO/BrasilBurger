using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("payement")]
public class Payement
{
    [Column("id")]
    public int Id { get; set; }

    [Column("methode_payement")]
    public string MethodePayement { get; set; } = null!;

    [Column("montant")]
    public int Montant { get; set; }

    [Column("transaction_ref")]
    public string? TransactionRef { get; set; }

    [Column("statut_payement")]
    public string StatutPayement { get; set; } = null!;

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [Column("order_id")]
    public int? OrderId { get; set; }

    // Navigation
    public Order? Order { get; set; }
}
}