// Generic authorization middleware that accepts a policy function.
// The policy function will receive (user, resource) and must return true/false.

module.exports = function (policy) {
  return (req, res, next) => {
    try {
      if (!policy(req.user, req.mail)) {
        return res.status(403).json({ error: "Forbidden" });
      }
      next();
    } catch (err) {
      next(err);
    }
  };
};