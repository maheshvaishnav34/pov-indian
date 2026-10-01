/**
 * errorHandler.js
 * Centralized, safe error handling middleware.
 * Conceals internal stack traces in production to prevent information disclosure.
 */
module.exports = function (err, req, res, next) {
  console.error('[Backend Error]:', err.message || err);

  const isDev = process.env.NODE_ENV !== 'production';

  res.status(err.status || 500).json({
    success: false,
    message: err.message || 'Internal Server Error',
    ...(isDev && { stack: err.stack })
  });
};
