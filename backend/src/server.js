const express = require('express');
const helmet = require('helmet');
const cors = require('cors');
require('dotenv').config();

const db = require('./config/db');
const apiKeyGuard = require('./middlewares/apiKeyGuard');
const { apiLimiter } = require('./middlewares/rateLimiter');
const errorHandler = require('./middlewares/errorHandler');

const categoryRoutes = require('./routes/categoryRoutes');
const locationRoutes = require('./routes/locationRoutes');
const listingRoutes = require('./routes/listingRoutes');
const blogRoutes = require('./routes/blogRoutes');
const formRoutes = require('./routes/formRoutes');
const authRoutes = require('./routes/authRoutes');
const rentalRoutes = require('./routes/rentalRoutes');
const educationRoutes = require('./routes/educationRoutes');

const app = express();

// Security HTTP headers
app.use(helmet());

// CORS configuration (allow requests from PHP frontend)
app.use(cors({
  origin: function (origin, callback) {
    // allow requests with no origin (like mobile apps, curl, or PHP server-to-server)
    if (!origin) return callback(null, true);
    // Allow any localhost / 127.0.0.1 port in local development
    if (/^http:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/.test(origin)) {
      return callback(null, true);
    }
    const allowed = (process.env.ALLOWED_ORIGIN || 'http://127.0.0.1:8000,http://localhost:8000')
      .split(',')
      .map(s => s.trim());
    if (allowed.includes(origin) || allowed.includes('*')) {
      return callback(null, true);
    }
    return callback(null, true); // Fallback allow in dev
  },
  credentials: true
}));

// Body parsers
app.use(express.json({ limit: '1mb' }));
app.use(express.urlencoded({ extended: true, limit: '1mb' }));

// Global rate limiting
app.use('/api/', apiLimiter);

// Health check with DB status
app.get('/api/health', async (req, res) => {
  const dbStatus = await db.testConnection();
  res.json({
    status: 'OK',
    timestamp: new Date().toISOString(),
    database: dbStatus.connected ? 'connected' : 'disconnected',
    ...(dbStatus.error && { db_notice: dbStatus.error })
  });
});

// Root & API status route handler (friendly landing page + JSON response)
const rootHandler = async (req, res) => {
  const dbStatus = await db.testConnection();
  const acceptsHtml = req.accepts(['html', 'json']) === 'html';

  if (acceptsHtml) {
    const dbBadge = dbStatus.connected 
      ? '<span style="background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:999px; font-weight:700;">🟢 Connected</span>'
      : '<span style="background:#fee2e2; color:#b91c1c; padding:4px 10px; border-radius:999px; font-weight:700;">🔴 Disconnected</span>';

    return res.send(`<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POV Indian - Backend API Server</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: #0f172a;
      color: #f8fafc;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .card {
      background: rgba(30, 41, 59, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(16px);
      border-radius: 20px;
      max-width: 620px;
      width: 100%;
      padding: 36px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(16, 185, 129, 0.15);
      color: #34d399;
      border: 1px solid rgba(52, 211, 153, 0.3);
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    h1 { font-size: 1.85rem; font-weight: 800; margin-bottom: 10px; color: #fff; }
    p { color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; }
    .status-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 28px;
    }
    .status-item {
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 14px 16px;
    }
    .status-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 6px; }
    .status-val { font-size: 0.95rem; font-weight: 700; color: #e2e8f0; }
    .btn-main {
      display: block;
      width: 100%;
      text-align: center;
      background: linear-gradient(135deg, #ea580c, #f97316);
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      padding: 14px 20px;
      border-radius: 12px;
      text-decoration: none;
      box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.4);
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .btn-main:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 25px -5px rgba(234, 88, 12, 0.5);
    }
    .links-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .links-grid a {
      color: #38bdf8;
      font-size: 0.85rem;
      text-decoration: none;
      background: rgba(56, 189, 248, 0.08);
      padding: 6px 12px;
      border-radius: 8px;
    }
    .links-grid a:hover { text-decoration: underline; background: rgba(56, 189, 248, 0.15); }
  </style>
</head>
<body>
  <div class="card">
    <div class="badge">● API SERVER ONLINE</div>
    <h1>POV Indian API Services</h1>
    <p>The Node.js REST API backend is running smoothly. To browse the full POV Indian platform, open the main frontend website below.</p>
    
    <div class="status-grid">
      <div class="status-item">
        <div class="status-label">Database (MySQL)</div>
        <div class="status-val">${dbBadge}</div>
      </div>
      <div class="status-item">
        <div class="status-label">API Port</div>
        <div class="status-val" style="color:#38bdf8;">${process.env.PORT || 5000} (Node.js)</div>
      </div>
      <div class="status-item">
        <div class="status-label">Frontend Web App</div>
        <div class="status-val" style="color:#fb923c;">http://127.0.0.1:8000</div>
      </div>
      <div class="status-item">
        <div class="status-label">API Status</div>
        <div class="status-val" style="color:#34d399;">Operational (200 OK)</div>
      </div>
    </div>

    <a href="http://127.0.0.1:8000" class="btn-main">👉 Open Main POV Indian Website (Port 8000)</a>

    <div class="links-grid">
      <a href="/api/health">/api/health</a>
      <a href="/api/v1/rentals">/api/v1/rentals</a>
      <a href="/api/v1/rentals/curated">/api/v1/rentals/curated</a>
      <a href="/api/v1/edu/institutions">/api/v1/edu/institutions</a>
      <a href="/api/v1/edu/exams">/api/v1/edu/exams</a>
    </div>
  </div>
</body>
</html>`);
  }

  res.json({
    success: true,
    message: 'POV Indian Backend API Server is running',
    frontend_url: 'http://127.0.0.1:8000',
    status: 'OK',
    version: '1.0.0',
    database: dbStatus.connected ? 'connected' : 'disconnected',
    endpoints: {
      health: '/api/health',
      rentals: '/api/v1/rentals',
      rentals_curated: '/api/v1/rentals/curated',
      education: '/api/v1/edu',
      categories: '/api/v1/categories',
      locations: '/api/v1/locations',
      listings: '/api/v1/listings'
    }
  });
};

app.get('/', rootHandler);
app.get('/api', rootHandler);
app.get('/api/v1', rootHandler);

// Internal API routes (Guarded by X-Internal-API-Key)
app.use('/api/v1/categories', apiKeyGuard, categoryRoutes);
app.use('/api/v1/locations', apiKeyGuard, locationRoutes);
app.use('/api/v1/listings', apiKeyGuard, listingRoutes);
app.use('/api/v1/blogs', apiKeyGuard, blogRoutes);

// Rentals & Stays platform routes (Find a place that fits your life)
app.use('/api/v1/rentals', rentalRoutes);

// Higher Education & College Discovery routes (POVIndian Master Platform)
app.use('/api/v1/edu', educationRoutes);

// Public / Semi-public routes (have specialized rate limiters and auth)
app.use('/api/v1/forms', formRoutes);
app.use('/api/v1/auth', authRoutes);

// 404 handler
app.use((req, res) => {
  res.status(404).json({ success: false, message: 'API endpoint not found', path: req.originalUrl });
});

// Centralized error handler
app.use(errorHandler);

const PORT = Number(process.env.PORT) || 5000;
app.listen(PORT, '0.0.0.0', () => {
  console.log(`====================================================`);
  console.log(` POV Indian Backend Server Running Successfully!`);
  console.log(` URL: http://127.0.0.1:${PORT}`);
  console.log(` Health: http://127.0.0.1:${PORT}/api/health`);
  console.log(`====================================================`);
});
