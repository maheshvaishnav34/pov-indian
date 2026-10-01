/**
 * apiKeyGuard.js
 * Protects server-to-server internal API calls (e.g. from PHP frontend).
 * Ensures random unauthorized users cannot hit internal data endpoints directly.
 */
module.exports = function (req, res, next) {
  const incomingKey = req.headers['x-internal-api-key'];
  const validKey = process.env.INTERNAL_API_KEY;

  if (!incomingKey || incomingKey !== validKey) {
    return res.status(403).json({
      success: false,
      message: 'Forbidden: Invalid or missing X-Internal-API-Key header'
    });
  }
  next();
};
