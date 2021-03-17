function User (user) {
  this.username = (user.username) ? user.username : '';
  this.password = (user.password) ? user.password : '';
  this.status = (user.status) ? user.status : '';
};

module.exports = User;