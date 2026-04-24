const jwt = require("jsonwebtoken");

const SECRET = process.env.JWT_SECRET || "Krish_Patel";

module.exports = function authenticateJWT(req, res, next) {
  const authHeader = req.headers.authorization;
  if (!authHeader || !authHeader.startsWith("Bearer ")) {
    return next(new Error("Authorization header is missing or invalid"));
  }

  const token = authHeader.substring(7); // Remove "Bearer " prefix

  try {
    const decoded = jwt.verify(token, SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    next(new Error("Invalid or expired token"));
  }
};