let express = require('express');
let bodyParser = require("body-parser");
// const cliSpinners = require('cli-spinners');
const readline = require('readline');
let sql = require("./app/db");
var port = 3100;
let dbname = 'fsoftpro_barcode_ppd';
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

var app = express();

// for json request and response
app.use(bodyParser.urlencoded({
  extended: true
}));
app.use(bodyParser.json());

app.listen(port, () => {
  // console.log("[success] task 1 : listening on port " + port);
});


const doHistory = () => {
  const init = (db_list) => {
    rl.question('Choose Database ' + db_list.map((value, index) => { return `\n[${index}] ${value}`; }) + "\nEnter: ", (input) => {
      let dbselect = db_list[input];
      console.log(`Select DB "${dbselect}"`);
      createTable(dbselect);
    });
  }

  const createTable = (dbname) => {
    rl.question('Create Table mb_master_history (Enter / n) ? ', val => {
      if (val != 'n') {
        sql.query("CREATE TABLE IF NOT EXISTS `" + dbname + "`.`mb_master_history` (`id_history` INT NOT NULL AUTO_INCREMENT,`id_user` INT NULL,`id_group` INT NULL,`barcode_start` INT NULL,`barcode_end` INT NULL,`barcode_qty` INT NULL, `barcode_use` INT DEFAULT 0,`date_purchase` date NULL,`date_received` date NULL,`date_added` datetime (0) NULL,`date_modify` datetime (0) NULL, `del` INT DEFAULT 0,PRIMARY KEY (`id_history`),INDEX `id_group` (`id_group`) USING BTREE,INDEX `barcode_start` (`barcode_start`) USING BTREE,INDEX `barcode_end` (`barcode_end`) USING BTREE,INDEX `barcode_qty` (`barcode_qty`) USING BTREE,INDEX `date_purchase` (`date_purchase`) USING BTREE,INDEX `date_received` (`date_received`) USING BTREE);");
      }
      truncateTable(dbname);
    });
  }

  const truncateTable = (dbname) => {
    rl.question('TRUNCATE Table mb_master_history (Enter / n) ? ', val => {
      if (val != 'n') {
        sql.query("TRUNCATE `" + dbname + "`.`mb_master_history`;");
      }
      findGroup(dbname);
    });
  }

  const findGroup = (dbname) => {
    rl.question('Loading big data (Enter / n) ? ', val => {
      if (val != 'n') {
        sql.query("SELECT * FROM `" + dbname + "`.`mb_master_config_barcode`;", (err, data) => {
          data.map((value, index) => {
            dumpData(index, data.length, value.group)
          });
        });
      } else {
        console.clear();
        console.log('Not Dump Data!!');
      }
    });
  }

  const dumpData = (index, countGroup, group) => {
    let sqlQuery = "INSERT INTO `" + dbname + "`.`mb_master_history` (id_user,id_group,barcode_start,barcode_end,barcode_qty,date_purchase,date_added,date_modify) SELECT 1 AS id_user,g.id_group,min(b.barcode_code) AS barcode_start,max(b.barcode_code) AS barcode_end,(max(b.barcode_code)-min(b.barcode_code))+1 AS barcode_qty,b.date_added AS date_purchase,NOW() AS date_added,NOW() AS date_modify FROM `" + dbname + "`.`mb_master_group` g LEFT JOIN `" + dbname + "`.`mb_master_barcode` b ON b.id_group=g.id_group WHERE g.group_code=" + group + "  GROUP BY b.date_added ORDER BY g.group_code ASC,b.date_added ASC";
    sql.query(sqlQuery, (err, data) => {
      let loadtext = '[';
      let percent = ((parseInt(index) + 1) / parseInt(countGroup)) * 100;
      for (let i = 0; i < 50; i++) {
        loadtext += (percent / 2 >= i) ? '=' : '_';
      }
      loadtext += ']';
      console.clear();
      if (percent >= 100) {
        console.log('Done!!');
        return 'Done dump data';
      } else {
        console.log('Dump data!!');
        console.log(`${dbname} query group ${group} = insert ${data.affectedRows} rows`);
        console.log(`${loadtext} ${percent.toFixed(2)}%`);
      }
    });
  }

  let db_list = ['fsoftpro_barcode', 'fsoftpro_barcode_lmc', 'fsoftpro_barcode_ppd'];
  init(db_list);
}


doHistory();







// app.use(function(req, res, next) {
//     res.header("Access-Control-Allow-Origin", "*");
//     res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
//     next();
// });

// app.get("/", (req, res) => {
//     // res.status(200).send("Hello API");

// });

// app.use((req, res, next) => {
//     var err = new Error("Not found path");
//     err.status = 404;
//     next(err);
// });

