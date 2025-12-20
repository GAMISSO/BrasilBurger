using Microsoft.EntityFrameworkCore;
using Npgsql.EntityFrameworkCore.PostgreSQL;
using CloudinaryDotNet;

var builder = WebApplication.CreateBuilder(args);
var cloudinary = new Cloudinary(new Account(
    Environment.GetEnvironmentVariable("CLOUDINARY_CLOUD_NAME")!,
    Environment.GetEnvironmentVariable("CLOUDINARY_API_KEY")!,
    Environment.GetEnvironmentVariable("CLOUDINARY_API_SECRET")!
));

// Add services to the container.
builder.Services.AddControllersWithViews();

// Configure Entity Framework DbContext (PostgreSQL / Neon)
var connectionString = builder.Configuration.GetConnectionString("Default");
builder.Services.AddDbContext<Data.ApplicationDbContext>(options =>
{
    // Use Npgsql (PostgreSQL). Replace the default string by your actual Neon connection string
    options.UseNpgsql(connectionString);
});

var app = builder.Build();

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
    // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseRouting();

app.UseAuthorization();

app.MapStaticAssets();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Catalogue}/{action=Index}/{id?}")
    .WithStaticAssets();

app.MapGet("/", () => "hello Render");
app.Run();
