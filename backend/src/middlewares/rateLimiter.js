/**
 * rateLimiter.js
 * Prevents DDoS, brute-force login attempts, and form spam.
 * Automatically skips internal server-to-server requests from PHP bridge.
 */
const rateLimit = require('express-rate-limit');

// Helper to detect trusted internal requests
const isInternalRequest = (req) => {
  const internalKey = req.headers['x-internal-api-key'];
  return !!(internalKey && internalKey === process.env.INTERNAL_API_KEY);
};

// General API rate limit: 500 requests per 15 minutes per IP (skips internal server calls)
const apiLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 500,
  skip: isInternalRequest,
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    success: false,
    message: 'Too many requests from this IP, please try again in 15 minutes.'
  }
});

// Strict rate limit for form submissions: 20 per 15 minutes
const formLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 20,
  skip: isInternalRequest,
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    success: false,
    message: 'Too many submissions from this IP. Please try again after 15 minutes.'
  }
});

// Auth / Login rate limit: 30 attempts per 15 minutes
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 30,
  skip: isInternalRequest,
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    success: false,
    message: 'Too many login attempts. Please try again in 15 minutes.'
  }
});

module.exports = { apiLimiter, formLimiter, authLimiter };
