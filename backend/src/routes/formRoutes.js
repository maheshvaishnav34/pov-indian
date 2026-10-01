const express = require('express');
const router = express.Router();
const formController = require('../controllers/formController');
const { formLimiter } = require('../middlewares/rateLimiter');
const { verifyToken, requireRole } = require('../middlewares/authMiddleware');

const adminOrInternalGuard = (req, res, next) => {
  const internalKey = req.headers['x-internal-api-key'];
  if (internalKey && internalKey === process.env.INTERNAL_API_KEY) {
    return next();
  }
  return verifyToken(req, res, () => {
    requireRole('admin', 'editor')(req, res, next);
  });
};

// Public form submission with rate limiting
router.post('/submit', formLimiter, formController.submitForm);

// Admin-only: View submissions
router.get('/all', adminOrInternalGuard, formController.getAllSubmissions);

module.exports = router;
