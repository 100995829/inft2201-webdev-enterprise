const { v4: uuidv4 } = require("uuid");

// TODO: Implement request logging middleware.
// Requirements:
// - Generate a requestId (uuid).
// - Attach it to req.requestId.
// - Log method, path, and requestId to the console (or to a file if you prefer).
// - Later, your error handler should re-use the same requestId in its output.

module.exports = function requestLogger(req, res, next) {
  const requestId = uuidv4();

  // Attach to request
  req.requestId = requestId;

  // Log request
  console.log(`REQUEST ${requestId} ${req.method} ${req.originalUrl}`);

  next();
};