const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.join(__dirname, '../.env') });

const STORAGE_DIR = path.resolve(__dirname, '../../../storage');

async function importStorageToDatabase() {
  console.log('====================================================');
  console.log(' Starting Import: Storage JSON -> MySQL Database');
  console.log('====================================================');

  const pool = await mysql.createPool({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'pov_indian_db',
    waitForConnections: true,
    connectionLimit: 10
  });

  // 1. Fetch Categories and Locations mapping
  const [catRows] = await pool.query('SELECT id, slug, name FROM categories');
  const catMap = {};
  for (const c of catRows) {
    catMap[c.slug] = c.id;
    catMap[c.name.toLowerCase()] = c.id;
  }

  const [locRows] = await pool.query('SELECT id, slug, name FROM locations');
  const locMap = {};
  for (const l of locRows) {
    locMap[l.slug] = l.id;
    locMap[l.name.toLowerCase()] = l.id;
  }

  // Helper to resolve category ID
  function resolveCatId(catSlug, catName, fallbackId) {
    if (catSlug && catMap[catSlug]) return catMap[catSlug];
    if (catName && catMap[catName.toLowerCase()]) return catMap[catName.toLowerCase()];
    if (fallbackId && Object.values(catMap).includes(Number(fallbackId))) return Number(fallbackId);
    return catRows[0]?.id || 1;
  }

  // Helper to resolve location ID
  function resolveLocId(locText, locationId) {
    if (locationId && Object.values(locMap).includes(Number(locationId))) return Number(locationId);
    if (!locText) return null;
    const lower = locText.toLowerCase();
    for (const [key, id] of Object.entries(locMap)) {
      if (lower.includes(key)) return id;
    }
    return null;
  }

  // 2. Import Listings from storage/listings
  const listingsDir = path.join(STORAGE_DIR, 'listings');
  let listingCount = 0;
  if (fs.existsSync(listingsDir)) {
    const files = fs.readdirSync(listingsDir).filter(f => f.startsWith('listing_') && f.endsWith('.json'));
    console.log(`Found ${files.length} listing files in storage.`);

    for (const file of files) {
      try {
        const raw = fs.readFileSync(path.join(listingsDir, file), 'utf8');
        const item = JSON.parse(raw);
        if (!item.title) continue;

        const catId = resolveCatId(item.cat_slug, item.cat, item.category_id);
        const locId = resolveLocId(item.loc || item.location_text, item.location_id);
        const slug = (item.title || '')
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');

        const locText = item.loc || item.location_text || 'India';
        const phone = item.phone || '+91 98765 43210';
        const price = item.price || 'On Request';
        const rating = Number(item.rating) || 5.0;
        const badge = item.badge || '';
        const img = item.img || item.image || 'realestate_03.webp';
        const avatar = item.avatar || 'ryan.webp';
        const desc = item.desc || item.description || '';

        // Check if listing already exists by title
        const [existing] = await pool.query('SELECT id FROM listings WHERE title = ?', [item.title]);
        if (existing.length === 0) {
          await pool.query(
            `INSERT INTO listings (category_id, location_id, title, slug, location_text, phone, price, rating, badge, image, avatar, description, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')`,
            [catId, locId, item.title, slug, locText, phone, price, rating, badge, img, avatar, desc]
          );
          listingCount++;
        }
      } catch (err) {
        console.warn(`Error processing listing ${file}:`, err.message);
      }
    }
    console.log(`Successfully imported ${listingCount} new listings into MySQL.`);
  }

  // 3. Import Users from storage/users
  const usersDir = path.join(STORAGE_DIR, 'users');
  let userCount = 0;
  if (fs.existsSync(usersDir)) {
    const userFolders = fs.readdirSync(usersDir).filter(f => fs.statSync(path.join(usersDir, f)).isDirectory());
    console.log(`Found ${userFolders.length} user folders in storage.`);

    for (const folder of userFolders) {
      const ufile = path.join(usersDir, folder, 'user.json');
      if (!fs.existsSync(ufile)) continue;

      try {
        const u = JSON.parse(fs.readFileSync(ufile, 'utf8'));
        if (!u.email) continue;

        const cleanEmail = u.email.toLowerCase().trim();
        const [existing] = await pool.query('SELECT id FROM users WHERE email = ?', [cleanEmail]);
        if (existing.length === 0) {
          const passHash = u.password_hash || '$2a$12$czXx.GQaNemjUR7mAqtvdOR2Ghzaw8nNShWKkRVi8ERpBz75m9eKq';
          const role = u.role || 'user';
          const name = u.name || 'User';

          await pool.query(
            'INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?)',
            [name, cleanEmail, passHash, role, u.is_active !== false ? 1 : 0]
          );
          userCount++;
        }
      } catch (err) {
        console.warn(`Error processing user ${folder}:`, err.message);
      }
    }
    console.log(`Successfully imported ${userCount} new users into MySQL.`);
  }

  // 4. Import Form Submissions from storage/submissions
  const subsDir = path.join(STORAGE_DIR, 'submissions');
  let subCount = 0;
  if (fs.existsSync(subsDir)) {
    const subFiles = fs.readdirSync(subsDir).filter(f => f.endsWith('.json'));
    console.log(`Found ${subFiles.length} submission files in storage.`);

    for (const file of subFiles) {
      try {
        const sub = JSON.parse(fs.readFileSync(path.join(subsDir, file), 'utf8'));
        const type = sub.type || 'contact';
        const fields = sub.fields || {};
        const email = fields.email || sub.email || 'anonymous@example.com';
        const name = fields.name || sub.name || null;
        const phone = fields.phone || sub.phone || null;
        const subject = fields.subject || sub.subject || null;
        const message = fields.message || sub.message || null;
        const ip = sub.ip || null;

        await pool.query(
          `INSERT INTO form_submissions (type, name, email, phone, subject, message, extra_data, ip_address, status)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')`,
          [type, name, email, phone, subject, message, JSON.stringify(fields), ip]
        );
        subCount++;
      } catch (err) {
        console.warn(`Error processing submission ${file}:`, err.message);
      }
    }
    console.log(`Successfully imported ${subCount} submissions into MySQL.`);
  }

  // Print final summary
  const [lTotal] = await pool.query('SELECT COUNT(*) as count FROM listings');
  const [uTotal] = await pool.query('SELECT COUNT(*) as count FROM users');
  const [sTotal] = await pool.query('SELECT COUNT(*) as count FROM form_submissions');

  console.log('====================================================');
  console.log(' FINAL DATABASE TOTALS:');
  console.log(` - Total Listings in MySQL: ${lTotal[0].count}`);
  console.log(` - Total Users in MySQL: ${uTotal[0].count}`);
  console.log(` - Total Submissions in MySQL: ${sTotal[0].count}`);
  console.log('====================================================');

  await pool.end();
}

importStorageToDatabase().catch(e => {
  console.error('Migration failed:', e);
  process.exit(1);
});
