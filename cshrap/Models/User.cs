using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
   [Table("users")]
public class User
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("login")]
    public string Login { get; set; } = null!;

    [Column("password_hash")]
    public string PasswordHash { get; set; } = null!;

    [Column("role_users")]
    public string RoleUsers { get; set; } = null!;
}

}