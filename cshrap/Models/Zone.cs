namespace Models
{
    public class Zone
    {
        public int id { get; set; }
        public string nom { get; set; } = string.Empty;
        public int prix_zone { get; set; }
        public DateTime created_at { get; set; }
    }
}