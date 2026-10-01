const pool = require('../backend/src/config/db');

async function main() {
  try {
    const [cols] = await pool.query('DESCRIBE listings');
    const existing = cols.map(c => c.Field);

    const additions = [
      { name: 'meta_title', type: 'VARCHAR(255) NULL AFTER description' },
      { name: 'meta_description', type: 'TEXT NULL AFTER meta_title' },
      { name: 'meta_keywords', type: 'VARCHAR(255) NULL AFTER meta_description' },
      { name: 'canonical_url', type: 'VARCHAR(255) NULL AFTER meta_keywords' },
      { name: 'og_image', type: 'VARCHAR(255) NULL AFTER canonical_url' },
      { name: 'schema_type', type: "VARCHAR(50) DEFAULT 'LocalBusiness' AFTER og_image" }
    ];

    for (const col of additions) {
      if (!existing.includes(col.name)) {
        console.log(`Adding column ${col.name}...`);
        await pool.query(`ALTER TABLE listings ADD COLUMN ${col.name} ${col.type}`);
      } else {
        console.log(`Column ${col.name} already exists.`);
      }
    }

    console.log("SEO columns successfully configured in 'listings' table!");
  } catch (err) {
    console.error("Migration error:", err);
  } finally {
    process.exit();
  }
}

main();
