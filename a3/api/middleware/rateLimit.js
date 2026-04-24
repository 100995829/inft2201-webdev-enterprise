const rateLimitStore = {}; // in-memory store

const MAX = parseInt(process.env.RATE_LIMIT_MAX) || 5;
const WINDOW = (parseInt(process.env.RATE_LIMIT_WINDOW_SECONDS) || 60) * 1000;

function rateLimit(req, res, next) {
  try {
    // Choose key (token-based preferred)
    const key = req.user ? `user-${req.user.userId}` : req.ip;

    const now = Date.now();

    if (!rateLimitStore[key]) {
      rateLimitStore[key] = {
        count: 1,
        startTime: now
      };
      return next();
    }

    const entry = rateLimitStore[key];

    // Check window
    if (now - entry.startTime < WINDOW) {
      entry.count++;

      if (entry.count > MAX) {
        const retryAfter = Math.ceil((WINDOW - (now - entry.startTime)) / 1000);

        const err = new Error("Too many requests");
        err.statusCode = 429;
        err.retryAfter = retryAfter;

        return next(err);
      }

      return next();
    } else {
      // Reset window
      rateLimitStore[key] = {
        count: 1,
        startTime: now
      };
      return next();
    }

  } catch (err) {
    next(err);
  }
}

module.exports = rateLimit;