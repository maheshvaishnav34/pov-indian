const pool = require('../backend/src/config/db');

async function main() {
  try {
    const [rows] = await pool.query('DESCRIBE listings');
    console.log("=== COLUMNS IN 'listings' TABLE ===");
    rows.forEach(r => console.log(`${r.Field} | ${r.Type} | ${r.Null} | ${r.Default}`));
    
    const [first] = await pool.query('SELECT * FROM listings LIMIT 1');
    console.log("\nSample Listing:", first[0]);
  } catch (err) {
    console.error("Error:", err);
  } finally {
    process.exit();
  }
}

main();
