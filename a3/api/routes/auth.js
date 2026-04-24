const express = require("express");
const jwt = require("jsonwebtoken");
const users = require("../data/users");

const router = express.Router();
const SECRET = process.env.JWT_SECRET || "Krish_Patel";

// POST /login
// Body: { username, password }
// On success: return a JWT that includes { userId, role } as claims.
router.post("/login", (req, res, next) => {
  try {
    const { username, password } = req.body;

    // Validate input
    if (!username || !password) {
      return res.status(400).json({ error: "Username and password are required" });
    }

    // Find user
    const user = users.find(u => u.username === username);

    // Check credentials
    if (!user || user.password !== password) {
      return res.status(401).json({ error: "Invalid username or password" });
    }

    // Create payload
    const payload = {
      userId: user.id,
      role: user.role
    };

    // Sign token
    const token = jwt.sign(payload, SECRET, {
      expiresIn: "1h"
    });

    // Return token
    res.json({ token });

  } catch (err) {
    next(err);
  }
});

module.exports = router;