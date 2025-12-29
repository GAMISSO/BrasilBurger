using System;
using System.Security.Cryptography;
using System.Text;

var password = "Client@01";

using var sha = SHA256.Create();
var hash = Convert.ToBase64String(
    sha.ComputeHash(Encoding.UTF8.GetBytes(password))
);

Console.WriteLine($"Password: {password}");
Console.WriteLine($"SHA256 Base64 Hash: {hash}");
