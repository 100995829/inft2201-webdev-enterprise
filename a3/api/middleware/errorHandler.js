// Centralized error handler.
// This should be the LAST app.use(...) in server.js.

module.exports = function errorHandler(err, req, res, next) {
  const requestId = req.requestId || null;

  // Determine status code
  const statusCode = err.statusCode || err.status || 500;

  // Determine safe error name & message (no stack leaks)
  const errorName = err.name || "InternalServerError";

  let message = "An unexpected error occurred.";

  // Only expose safe messages (4xx errors)
  if (statusCode >= 400 && statusCode < 500) {
    message = err.message;
  }

  // Log full error (for developers)
  console.error(`Unhandled error for request ${requestId}`, err);

  // Send structured response
  res.status(statusCode).json({
    error: errorName,
    message: message,
    statusCode: statusCode,
    requestId: requestId,
    timestamp: new Date().toISOString()
  });
};