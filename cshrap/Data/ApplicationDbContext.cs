using Microsoft.EntityFrameworkCore;
using Models;
namespace Data
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options) { }

        public DbSet<User> Users { get; set; } = null!;
        public DbSet<Order> Orders { get; set; } = null!;
        public DbSet<Payement> Payements { get; set; } = null!;
        public DbSet<Burger> Burgers { get; set; } = null!;
        public DbSet<Complement> Complements { get; set; } = null!;
        public DbSet<Menu> Menus { get; set; } = null!;
        public DbSet<OrderLine> OrderLines { get; set; } = null!;
        public DbSet<Zone> Zones { get; set; } = null!;
        public DbSet<ClientProfil> ClientProfiles { get; set; } = null!;
        public DbSet<MenuComplement> MenuComplements { get; set; } = null!;

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            // Only configure if not already configured (e.g., via dependency injection)
            if (!optionsBuilder.IsConfigured)
            {
                optionsBuilder.UseNpgsql(
                    "DefaultConnection"
                );
            }
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
{
    // User <-> ClientProfil (1–1)
    modelBuilder.Entity<ClientProfil>()
        .HasOne(cp => cp.User)
        .WithOne()
        .HasForeignKey<ClientProfil>(cp => cp.Id);

    // Menu -> Burger
    modelBuilder.Entity<Menu>()
        .HasOne(m => m.Burger)
        .WithMany()
        .HasForeignKey(m => m.BurgerId);

    // Order -> Payement (1–1)
    modelBuilder.Entity<Order>()
        .HasOne(o => o.Payement)
        .WithOne(p => p.Order)
        .HasForeignKey<Payement>(p => p.OrderId);

    modelBuilder.Entity<MenuComplement>()
        .HasKey(mc => new { mc.MenuId, mc.ComplementId });

    modelBuilder.Entity<MenuComplement>()
        .HasOne(mc => mc.Menu)
        .WithMany(m => m.MenuComplements)
        .HasForeignKey(mc => mc.MenuId);

    modelBuilder.Entity<MenuComplement>()
        .HasOne(mc => mc.Complement)
        .WithMany(c => c.MenuComplements)
        .HasForeignKey(mc => mc.ComplementId);

    base.OnModelCreating(modelBuilder);

}

    }
}
