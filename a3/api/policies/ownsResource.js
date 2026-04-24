// For the mail API, the resource will be req.mail.
// Returns true if mail.userId === user.userId.

function ownsResource(user, mail) {
  return user && mail && user.userId === mail.userId;
}

module.exports = ownsResource;