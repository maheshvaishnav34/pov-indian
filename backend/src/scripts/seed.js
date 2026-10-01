const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const bcrypt = require('bcryptjs');
require('dotenv').config({ path: path.join(__dirname, '../../.env') });

async function seed() {
  console.log('--- Starting POV Indian Database Migration & Seeding ---');

  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true
  });

  console.log('Connected to MySQL server successfully.');

  // Create Database & Tables
  const schemaSql = fs.readFileSync(path.join(__dirname, '../../database/schema.sql'), 'utf8');
  await connection.query(schemaSql);
  console.log('Schema created/verified successfully.');

  await connection.changeUser({ database: process.env.DB_NAME || 'pov_indian_db' });

  // 1. Seed Admin User
  const [adminCount] = await connection.query('SELECT COUNT(*) as count FROM users WHERE email = ?', ['admin@povindian.com']);
  if (adminCount[0].count === 0) {
    const salt = await bcrypt.genSalt(12);
    const hash = await bcrypt.hash('Admin@123456', salt);
    await connection.query(
      'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
      ['POV Indian SuperAdmin', 'admin@povindian.com', hash, 'admin']
    );
    console.log('Admin user created: admin@povindian.com / Admin@123456');
  }

  // 2. Seed Categories
  const categories = [
    { name: 'Hotels & Accommodations', slug: 'hotels', count_text: '1,240+', icon: 'event.svg', hero_image: 'assets/img/heroes/cat-hotels.jpg', description: 'From luxury resorts to authentic homestays across India — verified stays with local host insights.' },
    { name: 'Restaurants & Cafés', slug: 'restaurants', count_text: '2,850+', icon: 'cafe.svg', hero_image: 'assets/img/heroes/cat-restaurants.jpg', description: 'Authentic culinary experiences from street food to fine dining, curated with local reviews.' },
    { name: 'Colleges & Universities', slug: 'colleges', count_text: '980+', icon: 'colleges.svg', hero_image: 'assets/img/heroes/cat-colleges.jpg', description: 'Top educational institutions with student reviews, campus guides, and admission insights.' },
    { name: 'Wellness & Ayurveda', slug: 'wellness', count_text: '760+', icon: 'beauty-spas.svg', hero_image: 'assets/img/heroes/cat-wellness.jpg', description: 'Traditional and modern wellness centers, retreats, and Ayurveda therapies across India.' },
    { name: 'Real Estate', slug: 'real-estate', count_text: '1,120+', icon: 'home-service.svg', hero_image: 'assets/img/heroes/cat-real-estate.jpg', description: 'Property listings with verified information and local neighbourhood insights.' },
    { name: 'Tourism & Travel', slug: 'tourism', count_text: '1,560+', icon: 'cars.svg', hero_image: 'assets/img/heroes/cat-tourism.jpg', description: 'Tour operators, guides, and authentic travel experiences beyond typical tourist checklists.' },
    { name: 'Healthcare & Hospitals', slug: 'healthcare', count_text: '1,480+', icon: 'hospital.svg', hero_image: 'assets/img/heroes/cat-healthcare.jpg', description: 'Trusted hospitals, clinics, and healthcare providers with verified patient-friendly information.' },
    { name: 'Manufacturing & Industrial', slug: 'manufacturing', count_text: '920+', icon: 'manufacturing.svg', hero_image: 'assets/img/heroes/cat-manufacturing.jpg', description: 'Industrial suppliers, manufacturers, and B2B partners across key Indian hubs.' },
    { name: 'Legal & Consulting Firms', slug: 'legal', count_text: '640+', icon: 'legal.svg', hero_image: 'assets/img/heroes/cat-legal.jpg', description: 'Law firms, consultants, and professional advisors for business and personal needs.' },
    { name: 'Home & Garden', slug: 'home-garden', count_text: '1,050+', icon: 'home-garden.svg', hero_image: 'assets/img/heroes/cat-home-garden.jpg', description: 'Home improvement, interiors, landscaping, and garden services from verified local pros.' },
    { name: 'Auto Services', slug: 'auto', count_text: '1,310+', icon: 'auto-services.svg', hero_image: 'assets/img/heroes/cat-auto.jpg', description: 'Car care, repairs, detailing, and automotive services trusted by local drivers.' },
    { name: 'Health & Beauty', slug: 'beauty', count_text: '1,720+', icon: 'health-beauty.svg', hero_image: 'assets/img/heroes/cat-beauty.jpg', description: 'Salons, spas, and beauty studios offering authentic treatments and local recommendations.' }
  ];

  for (const cat of categories) {
    await connection.query(
      `INSERT INTO categories (name, slug, count_text, icon, hero_image, description)
       VALUES (?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE count_text = VALUES(count_text), description = VALUES(description)`,
      [cat.name, cat.slug, cat.count_text, cat.icon, cat.hero_image, cat.description]
    );
  }
  console.log(`Seeded ${categories.length} categories.`);

  // 3. Seed Locations
  const locations = [
    { name: 'Delhi NCR', count_text: '2,450+', slug: 'delhi-ncr', state: 'Delhi' },
    { name: 'Mumbai', count_text: '2,180+', slug: 'mumbai', state: 'Maharashtra' },
    { name: 'Bangalore', count_text: '1,890+', slug: 'bangalore', state: 'Karnataka' },
    { name: 'Kolkata', count_text: '1,340+', slug: 'kolkata', state: 'West Bengal' },
    { name: 'Chennai', count_text: '1,250+', slug: 'chennai', state: 'Tamil Nadu' },
    { name: 'Jaipur', count_text: '980+', slug: 'jaipur', state: 'Rajasthan' },
    { name: 'Hyderabad', count_text: '1,120+', slug: 'hyderabad', state: 'Telangana' },
    { name: 'Pune', count_text: '1,050+', slug: 'pune', state: 'Maharashtra' },
    { name: 'Kochi', count_text: '760+', slug: 'kochi', state: 'Kerala' },
    { name: 'Udaipur', count_text: '540+', slug: 'udaipur', state: 'Rajasthan' },
    { name: 'Goa', count_text: '890+', slug: 'goa', state: 'Goa' },
    { name: 'Ahmedabad', count_text: '720+', slug: 'ahmedabad', state: 'Gujarat' }
  ];

  for (const loc of locations) {
    await connection.query(
      `INSERT INTO locations (name, slug, count_text, state)
       VALUES (?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE count_text = VALUES(count_text)`,
      [loc.name, loc.slug, loc.count_text, loc.state]
    );
  }
  console.log(`Seeded ${locations.length} locations.`);

  // 4. Seed Initial Listings
  const [catRows] = await connection.query('SELECT id, slug FROM categories');
  const catMap = Object.fromEntries(catRows.map(r => [r.slug, r.id]));

  const listings = [
    { cat_slug: 'real-estate', img: 'realestate_03.webp', avatar: 'ryan.webp', rating: 5.0, title: 'Heritage Haveli Stay in Jaipur', loc: 'Jaipur, Rajasthan', phone: '+91 98765 43210', price: 'From ₹4,500/night', badge: 'featured', desc: 'A verified heritage stay with courtyard dining, local host experiences, and authentic Rajasthani hospitality.' },
    { cat_slug: 'real-estate', img: 'realestate_01.webp', avatar: 'james.webp', rating: 4.0, title: 'Verified 3BHK in Whitefield', loc: 'Bangalore, Karnataka', phone: '+91 99887 66554', price: '₹1.05 Cr', badge: '', desc: 'Spacious family apartment close to IT parks, schools, and everyday conveniences in Whitefield.' },
    { cat_slug: 'restaurants', img: 'restaurant_05.webp', avatar: 'emma.webp', rating: 5.0, title: 'Coastal Seafood Kitchen', loc: 'Chennai, Tamil Nadu', phone: '+91 91234 56780', price: '₹800 – ₹2,200', badge: 'featured', desc: 'Fresh coastal flavours with chef-led tasting menus and verified local reviews.' },
    { cat_slug: 'restaurants', img: 'cafe_01.webp', avatar: 'david.webp', rating: 4.0, title: 'Filter Coffee & Local Bites', loc: 'Bangalore, Karnataka', phone: '+91 90123 45678', price: '₹150 – ₹600', badge: 'top', desc: 'Neighbourhood cafe known for strong filter coffee, regional snacks, and student-friendly seating.' },
    { cat_slug: 'tourism', img: 'tourism_01.jpg', avatar: 'lisa.webp', rating: 5.0, title: 'Kerala Backwaters Day Experience', loc: 'Alleppey, Kerala', phone: '+91 98700 11223', price: '₹2,999/person', badge: '', desc: 'Guided day cruise through canals with lunch on board and village stopovers curated by locals.' },
    { cat_slug: 'wellness', img: 'wellness_01.jpg', avatar: 'emma.webp', rating: 5.0, title: 'Ayurveda Wellness Retreat', loc: 'Kozhikode, Kerala', phone: '+91 97654 32100', price: '₹3,500 – ₹12,000', badge: '', desc: 'Doctor-supervised therapies, yoga sessions, and sattvic meals in a calm Kerala setting.' },
    { cat_slug: 'hotels', img: 'hotel_02.jpg', avatar: 'lisa.webp', rating: 4.0, title: 'Boutique Homestay Near Fort Kochi', loc: 'Kochi, Kerala', phone: '+91 95555 22110', price: 'From ₹2,800/night', badge: '', desc: 'Design-forward rooms steps from cafes, art galleries, and the historic Fort Kochi promenade.' },
    { cat_slug: 'colleges', img: 'college_01.jpg', avatar: 'james.webp', rating: 5.0, title: 'Campus Guide: Tech University Hub', loc: 'Delhi NCR', phone: '+91 91111 22334', price: 'Student verified', badge: 'bump', desc: 'Local student insights on hostels, commute, clubs, and campus life for international applicants.' },
    { cat_slug: 'hotels', img: 'hotel_01.jpg', avatar: 'ryan.webp', rating: 5.0, title: 'Luxury Heritage Resort Udaipur', loc: 'Udaipur, Rajasthan', phone: '+91 94444 55667', price: 'From ₹9,900/night', badge: 'featured', desc: 'Lake-facing suites, curated cultural evenings, and verified concierge support for travelers.' }
  ];

  for (const item of listings) {
    const categoryId = catMap[item.cat_slug] || 1;
    const slug = item.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const [existing] = await connection.query('SELECT id FROM listings WHERE title = ?', [item.title]);
    if (existing.length === 0) {
      await connection.query(
        `INSERT INTO listings (category_id, title, slug, location_text, phone, price, rating, badge, image, avatar, description, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')`,
        [categoryId, item.title, slug, item.loc, item.phone, item.price, item.rating, item.badge, item.img, item.avatar, item.desc]
      );
    }
  }
  console.log('Seeded initial listings.');

  // 5. Seed Blogs
  const blogs = [
    {
      slug: 'unexplored-waterfalls-kerala',
      img: 'hotel.webp',
      tag: 'Hidden Gems',
      tag_slug: 'hidden-gems',
      title: "Unexplored Waterfalls of Kerala: A Local's Guide",
      excerpt: 'Discover the lesser-known waterfalls of Kerala that most tourists miss but locals treasure as their weekend getaways.',
      author: 'Arjun Menon',
      read: '8 min read',
      body: "Kerala's famous cascades draw crowds, but locals know quieter falls tucked behind spice trails and village roads. This guide maps weekend-friendly spots, best seasons, packing tips, and how to travel respectfully through forest checkpoints.\n\nStart early, hire local jeep drivers where trails get rough, and keep plastic out of the stream beds. Pair your visit with a homestay lunch for the full POV Indian experience."
    },
    {
      slug: 'engineering-student-bangalore',
      img: 'cafe.webp',
      tag: 'Student Diaries',
      tag_slug: 'student-diaries',
      title: 'A Day in the Life: Engineering Student in Bangalore',
      excerpt: "Experience the daily routine, challenges, and joys of being an engineering student in India's tech capital.",
      author: 'Priya Sharma',
      read: '6 min read',
      body: "From 7am metro rides to late-lab debugging sessions, Bangalore student life is a mix of ambition and filter coffee. Priya walks through hostel mornings, club hours, internship hustle, and the small rituals that make campus feel like home."
    }
  ];

  for (const b of blogs) {
    const [existing] = await connection.query('SELECT id FROM blogs WHERE slug = ?', [b.slug]);
    if (existing.length === 0) {
      await connection.query(
        `INSERT INTO blogs (slug, image, tag, tag_slug, title, excerpt, author, read_time, body)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [b.slug, b.img, b.tag, b.tag_slug, b.title, b.excerpt, b.author, b.read, b.body]
      );
    }
  }
  console.log(`Seeded ${blogs.length} blog posts.`);

  await connection.end();
  console.log('--- Database Setup & Seeding Completed Successfully! ---');
}

seed().catch(err => {
  console.error('Seeding failed:', err.message);
  process.exit(1);
});
