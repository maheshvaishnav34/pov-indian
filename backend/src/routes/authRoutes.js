const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');
const { authLimiter } = require('../middlewares/rateLimiter');
const { verifyToken, adminOrInternalGuard } = require('../middlewares/authMiddleware');

router.post('/login', authLimiter, authController.login);
router.post('/register', authLimiter, authController.register);
router.post('/update-interests', adminOrInternalGuard, authController.updateInterests);
router.get('/profile', verifyToken, authController.getProfile);
router.get('/users', adminOrInternalGuard, authController.getAllUsers);

module.exports = router;

