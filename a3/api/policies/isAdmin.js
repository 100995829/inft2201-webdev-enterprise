// Returns true if the user has the "admin" role.

function isAdmin(user) {
  return user && user.role === "admin";
}

module.exports = isAdmin;