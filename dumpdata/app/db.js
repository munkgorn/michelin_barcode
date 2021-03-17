const sql = () => {
  let mysql = require("mysql");

  let db = { 
    host: 'localhost',
    username: 'root',
    password: '',
    db_name: 'fsoftpro_barcode_lmc'
  };

  let connection = mysql.createConnection({
  host     : db.host,
  user     : db.username,
  password : db.password,
  database : db.db_name
  });
  connection.connect((err) => {
  if (err) {
    console.error(err);
  } else {
    // console.log('[success] task 2 : connected to the database');
  }
  });

  return connection;
}

module.exports = sql();