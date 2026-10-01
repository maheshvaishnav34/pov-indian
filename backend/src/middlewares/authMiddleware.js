/**
 * authMiddleware.js
 * Verifies JWT tokens and enforces Role-Based Access Control (RBAC).
 * Supports internal server-to-server calls via X-Internal-API-Key.
 */
const jwt = require('jsonwebtoken');

function verifyToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(401).json({
      success: false,
      message: 'Unauthorized: Bearer token required'
    });
  }

  const token = authHeader.split(' ')[1];
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'default_jwt_secret');
    req.user = decoded;
    next();
  } catch (err) {
    return res.status(401).json({
      success: false,
      message: 'Unauthorized: Invalid or expired token'
    });
  }
}

function requireRole(...roles) {
  return (req, res, next) => {
    if (!req.user || !roles.includes(req.user.role)) {
      return res.status(403).json({
        success: false,
        message: 'Forbidden: Insufficient privileges'
      });
    }
    next();
  };
}

function adminOrInternalGuard(req, res, next) {
  const internalKey = req.headers['x-internal-api-key'];
  if (internalKey && internalKey === process.env.INTERNAL_API_KEY) {
    return next();
  }
  return verifyToken(req, res, () => {
    requireRole('admin', 'editor')(req, res, next);
  });
}

module.exports = { verifyToken, requireRole, adminOrInternalGuard };
