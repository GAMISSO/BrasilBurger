using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Models
{
    [Table("order_line")]
    public class OrderLine
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        // =========================
        // RELATION COMMANDE
        // =========================
        [Column("order_id")]
        public int OrderId { get; set; }

        public Order Order { get; set; }

        // =========================
        // TYPE D’ITEM
        // =========================
        [Column("item_type")]
        public string ItemType { get; set; } // Burger | Menu

        // =========================
        // BURGER / MENU
        // =========================
        [Column("burger_id")]
        public int? BurgerId { get; set; }
        public Burger Burger { get; set; }

        [Column("menu_id")]
        public int? MenuId { get; set; }
        public Menu Menu { get; set; }

        // =========================
        // QUANTITÉ & PRIX
        // =========================
        [Column("quantity")]
        public int Quantity { get; set; }

        [Column("prix")]
        public int Prix { get; set; }

        [Column("created_at")]
        public DateTime CreatedAt { get; set; }
    }
}
