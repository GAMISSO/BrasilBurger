using Microsoft.EntityFrameworkCore;
using Npgsql.EntityFrameworkCore.PostgreSQL;
using CloudinaryDotNet;
using Microsoft.Extensions.Logging;

var builder = WebApplication.CreateBuilder(args);

// Basic console logging to help debugging on Render
builder.Logging.ClearProviders();
builder.Logging.AddConsole();

// Create a temporary logger to log early startup diagnostics (no service provider yet)
using var startupLoggerFactory = LoggerFactory.Create(lb => lb.AddConsole());
var startupLogger = startupLoggerFactory.CreateLogger("Startup");

Cloudinary? cloudinary = null;
try
{
    var cloudName = Environment.GetEnvironmentVariable("CLOUDINARY_CLOUD_NAME");
    var apiKey = Environment.GetEnvironmentVariable("CLOUDINARY_API_KEY");
    var apiSecret = Environment.GetEnvironmentVariable("CLOUDINARY_API_SECRET");

    if (string.IsNullOrWhiteSpace(cloudName) || string.IsNullOrWhiteSpace(apiKey) || string.IsNullOrWhiteSpace(apiSecret))
    {
        startupLogger.LogWarning("One or more Cloudinary environment variables are missing. Cloudinary will not be configured.");
    }
    else
    {
        cloudinary = new Cloudinary(new Account(cloudName, apiKey, apiSecret));
        startupLogger.LogInformation("Cloudinary configured successfully.");
    }
}
catch (Exception ex)
{
    // Avoid crashing the process on startup for a third-party SDK error — log and continue
    startupLogger.LogError(ex, "Failed to initialize Cloudinary.");
}

// Add services to the container.
builder.Services.AddControllersWithViews();
// Session support (used by controllers/views)
builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.Cookie.HttpOnly = true;
    options.Cookie.IsEssential = true;
    options.IdleTimeout = TimeSpan.FromHours(2);
});

// Configure Entity Framework DbContext (PostgreSQL / Neon)
// Resolve connection string (try common keys)
var connectionString = builder.Configuration.GetConnectionString("DefaultConnection")
                       ?? builder.Configuration.GetConnectionString("Default");
if (string.IsNullOrWhiteSpace(connectionString))
{
    // Fail fast with a clear message so Render logs show the reason instead of a cryptic crash
    startupLogger.LogError("No connection string found (DefaultConnection or Default). Aborting startup.");
    throw new InvalidOperationException("Missing connection string. Set ConnectionStrings:DefaultConnection in configuration or as an environment variable.");
}

builder.Services.AddDbContext<Data.ApplicationDbContext>(options =>
{
    // Use Npgsql (PostgreSQL). Replace the default string by your actual Neon connection string
    options.UseNpgsql(connectionString);
});

// Register Cloudinary in DI only if available
if (cloudinary != null)
{
    builder.Services.AddSingleton(cloudinary);
}

var app = builder.Build();

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
    // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
    app.UseHsts();
}

app.UseHttpsRedirection();
// Serve static files from wwwroot (images, css, js)
app.UseStaticFiles();
app.UseRouting();

// Add session middleware
app.UseSession();

// Global exception logging for requests (helps capture errors in Render logs)
app.Use(async (context, next) =>
{
    try
    {
        await next();
    }
    catch (Exception ex)
    {
        var logger = context.RequestServices.GetService(typeof(ILogger<Program>)) as ILogger;
        logger?.LogError(ex, "Unhandled exception while processing request {Path}", context.Request.Path);
        throw;
    }
});

app.UseAuthorization();

app.MapStaticAssets();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Catalogue}/{action=Index}/{id?}")
    .WithStaticAssets();

// Redirect root URL to the Catalogue Index
app.MapGet("/", () => Results.Redirect("/Catalogue/Index"));
app.Run();
