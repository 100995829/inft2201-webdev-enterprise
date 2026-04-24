// Centralized error handler.
// This should be the LAST app.use(...) in server.js.

module.exports = function errorHandler(err, req, res, next) {
  const requestId = req.requestId || null;

  // Determine status code
  const statusCode = err.statusCode || err.status || 500;

  // Categorize error
  let errorCategory = "InternalServerError";

  if (statusCode === 400) errorCategory = "BadRequest";
  else if (statusCode === 401) errorCategory = "Unauthorized";
  else if (statusCode === 403) errorCategory = "Forbidden";
  else if (statusCode === 404) errorCategory = "NotFound";
  else if (statusCode === 429) errorCategory = "TooManyRequests";

  // Safe message (no internal leaks)
  let message = "An unexpected error occurred.";

  if (statusCode >= 400 && statusCode < 500 && err.message) {
    message = err.message;
  }

  // Log full error internally
  console.error(`Unhandled error for request ${requestId}`, err);

  // Optional: Retry-After header (for rate limiting)
  if (err.retryAfter) {
    res.set("Retry-After", err.retryAfter);
  }

  // Send standardized response
  res.status(statusCode).json({
    error: errorCategory,
    message: message,
    statusCode: statusCode,
    requestId: requestId,
    timestamp: new Date().toISOString()
  });
};