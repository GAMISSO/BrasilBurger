using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("client_profil")]
public class ClientProfil
{
    [Column("id")]
    public int Id { get; set; }   // FK + PK vers users.id

    [Column("nom")]
    public string Nom { get; set; } = null!;

    [Column("prenom")]
    public string Prenom { get; set; } = null!;

    [Column("adresse")]
    public string? Adresse { get; set; }

    [Column("telephone")]
    public string? Telephone { get; set; }

    [Column("email")]
    public string? Email { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    // Navigation
    public User User { get; set; } = null!;
}


}