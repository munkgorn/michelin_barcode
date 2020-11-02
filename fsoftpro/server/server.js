const express = require("express");
const mysql = require("mysql");
const bodyParser = require("body-parser");
const config = require("./db.config");
var fs = require('fs');
var path = require('path');

const app = express();
const port = 5000;

const db = mysql.createConnection({
  host: config.host,
  user: config.username,
  password: config.password,
  database: config.database,
  multipleStatements: true
});

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(function (req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header(
    "Access-Control-Allow-Headers",
    "Origin, X-Requested-With, Content-Type, Accept"
  );
  next();
});

app.listen(port, () => {
  console.log(`Server started at port ${port}`);
});

db.connect();

app.post("/truncate", (req, res) => {
  let text = '';
  let sql = "TRUNCATE mb_master_group;";
  sql += "TRUNCATE mb_master_product;";
  sql += "TRUNCATE mb_master_barcode;";
  sql += "TRUNCATE mb_master_import_barcode;";
  sql += "update mb_master_config_barcode set remaining = total;;";
  let query = db.query(sql, (err, results) => { 
    if (err) throw err;
    text = 'truncate all success';
    res.send(text);
  });

});


app.post("/setdate", (req, res) => {
  let text = '';
  let sql = "update mb_master_barcode set date_added = '"+req.body.todate+"', date_modify = '"+req.body.todate+"' WHERE date_added LIKE '"+req.body.datefrom+"%';";
  sql += "update mb_master_import_barcode set date_added = '"+req.body.todate+"', date_modify = '"+req.body.todate+"' WHERE date_added LIKE '"+req.body.datefrom+"%';";
  sql += "update mb_master_group set date_added = '"+req.body.todate+"', date_modify = '"+req.body.todate+"' WHERE date_added LIKE '"+req.body.datefrom+"%';";
  sql += "update mb_master_product set date_wk = '"+req.body.todate+"' WHERE date_wk LIKE '"+req.body.datefrom+"%';";
  let query = db.query(sql, (err, results) => {
    if (err) throw err;
    text = 'set date all success';
    res.send(text);
  });
});

app.post("/getAssociationDate", (req, res) => {
  let text = '';
  let sql = "SELECT date_modify FROM mb_master_group GROUP BY date_modify;";
  let query = db.query(sql, (err, results) => {
    if (err) throw err;
    res.send(results);
  });
});

app.post('/removefile', (req, res) => {
  let pathdir = __dirname + '/../../uploads/';
  fs.readdir(pathdir, (err, dirs) => {
    if (err) console.log(err);

    
      for (const dir of dirs) {
        // console.log(dir);
        // const childPath = pathdir + dir + '/';
        // console.log(childPath);
        fs.readdir(childPath, (err, files) => {
          files.forEach(file => {
            console.log(file);
          });
        //   console.log(files);
        //   // if (typeof files !== 'undefined') {
        //   //   for (const file of files) {
        //   //     const filePath = childPath + file;
        //   //     console.log(filePath);
        //   //     // fs.unlink(filePath, err => {
        //   //     //     if (err) console.log(err);
        //   //     //     res.send("Remove file " + filePath);
        //   //     // });
        //   //   }
        //   // }
          
        });
        // const newLocal = pathdir + file;
          // fs.unlink(newLocal, err => {
          //     if (err) console.log(err);
          //     res.send("Remove file " + newLocal);
          // });
      }
  });
});