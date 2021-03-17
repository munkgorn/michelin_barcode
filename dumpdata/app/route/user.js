let express = require("express");
let router = express.Router();
let sql = require("../db");
let User = require("../model/user");

// GET all
router.get("/", (req, res) => {
  sql.query("SELECT * FROM user", (err, data) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data);
  });
});

// GET WHERE
router.get("/status/:status", (req, res) => {
  sql.query("SELECT * FROM user WHERE status = ?", [req.params.status], (err, data) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data);
  });
});

// GET ONCE
router.get("/:id", (req, res) => {
  sql.query("SELECT * FROM user WHERE id = ?", [req.params.id], (err, data) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data);
  });
});

// POST (create new data)
router.post("/", (req, res) => {
  let obj = new User(req.body);
  delete obj.id; // not insert id, id is primary key
  sql.query("INSERT INTO user SET ?", obj, (err, data) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data); // use data.insertId or data.affectedRows
  });
});

// PUT (update current data)
router.put("/:id", (req, res) => {
  let obj = new User(req.body);
  sql.query("UPDATE user SET ? WHERE ?", [obj, {id: req.params.id}], (err, data, field) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data); // use data.insertId or data.affectedRows
  });
});

// DELETE (delete 1 data)
router.delete("/:id", (req, res) => {
  sql.query("DELETE FROM user WHERE ?", {id: req.params.id}, (err, data, field) => {
    if (err) return res.status(400).send(err);
    res.status(200).send(data); // use data.insertId or data.affectedRows
  });
});

module.exports = router;