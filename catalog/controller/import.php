<?php
class ImportController extends Controller
{
    public function __construct()
    {
        if ($this->hasSession('id_user') == false) {
            $this->rmSession('id_user');
            $this->rmSession('username');
            $this->setSession('error', 'Please Login');
            $this->redirect('home');
        }

        if ($this->hasSession('id_user_group')) {
            if (!in_array($this->getSession('id_user_group'), array(1, 2))) {
                $this->setSession('error', 'Permission fail');
                $this->redirect('dashboard');
            }
        }
    }

    private function linkIndex() {
        echo '<br><hr><br><a href="index.php?route=import">Back</a>';
        exit();
    }

    public function removeData() {
        $import = $this->model('import');

        $table = array(
            'mb_master_barcode',
            'mb_master_group',
            'mb_master_product'
        );
        
        $listtable = $_POST['listtable'];
        foreach ($listtable as $t) {
            if (in_array($t, $table)) {
                $result = $import->removeTable($t);
                echo 'Truncate '.$t.' : '.($result==1?'success':'fail').'<br>';
            }
        }

        $this->linkIndex();

        

        // $this->redirect('import&successRemove');
    }
    public function menual()
    {
        $import = $this->model('import');
        $date = '2019-05-23 00:00:00';
        for ($i = 3360000; $i <= 3399999; $i++) {
            $import->insertBarcode($i, $date);
        }
    }

    public function importAssociation()
    {
        $import = $this->model('import');

        if (method_post()) {


            $this->rmSession('import_group');


            $dir = 'uploads/import/';
            $path = DOCUMENT_ROOT . $dir;
            $path_csv = DOCUMENT_ROOT . $dir;

            $file = $_FILES['import_file'];

            $fileType = strtolower(pathinfo(basename($file["name"]), PATHINFO_EXTENSION));
            $newname = 'import_mockup_association';
            $file_csv = 'CSV_' . $newname . '.csv';
            $newname .= '.' . $fileType;
            $acceptFileType = array('xlsx');

            // check folder upload
            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777);
                umask($oldmask);
            }

            // check file
            if ($file['error'] == 0 && in_array($fileType, $acceptFileType)) {
                if (upload($file, $path, $newname)) {
                    // Read file to insert database
                    $config = $this->model('config');
                    $group = $this->model('group');

                    $results = readExcel($dir . $newname, 0, 0);

                    $dir = 'uploads/mockupdata/';
                    $path = DOCUMENT_ROOT . $dir;
                    $path_csv = DOCUMENT_ROOT . $dir;
                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777);
                        umask($oldmask);
                    }
                    $newname = 'import_group';
                    $file_csv = 'CSV_' . $newname . '.csv';
                    $csv_file = $path_csv . $file_csv;
                    $col = array();
                    $row = 0;
                    $fp = fopen($csv_file, 'w');

                    // DATE
                    $date = $_POST['date'];

                    foreach ($results as $value) {
                        // if ($row > 0) {
                        $insert = array(
                            $this->getSession('id_user'),
                            $value[0],
                            (int) $value[1],
                            (isset($value[2])?$value[2]:''),
                            $date,
                        );
                        fputcsv($fp, $insert, ',', chr(0));
                        // }
                        $row++;
                    }

                    fclose($fp);
                    $result = $import->loadCSVAssociation($csv_file);
                    echo 'Import association with date '.($result?'success':'fail').'<br>';
                    // $this->generateJsonFreeGroup();
                }
            }

            // redirect('import&successAss');
            $this->linkIndex();
        }
    }

    public function importAll() {
        $import = $this->model('import');
        if (method_post()) {
            $dir = 'uploads/import/';
            $path = DOCUMENT_ROOT . $dir;
            $path_csv = DOCUMENT_ROOT . $dir;

            $file = $_FILES['import_file'];

            $fileType = strtolower(pathinfo(basename($file["name"]), PATHINFO_EXTENSION));
            $newname = 'import_relationship_' . date('YmdHis');
            $file_csv = 'CSV_' . $newname . '.csv';
            $newname .= '.' . $fileType;
            $acceptFileType = array('xlsx');

            // check folder upload
            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777);
                umask($oldmask);
            }

            // check file
            if ($file['error'] == 0 && in_array($fileType, $acceptFileType)) {
                $resultupload = upload($file, $path, $newname);
                if ($resultupload) {

                    echo 'Upload file : '.($resultupload==1?'success':'fail').'<br>';

                    // Read file to insert database
                    $config = $this->model('config');
                    $group = $this->model('group');

                    $results = readExcel($dir . $newname, 0, 0);
                    $results2 = readExcel($dir . $newname, 0, 1);

                    $dir = 'uploads/mockupdata/';
                    $path = DOCUMENT_ROOT . $dir;
                    $path_csv = DOCUMENT_ROOT . $dir;
                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777);
                        umask($oldmask);
                    }
                    $newname = 'import_group';
                    $file_csv = 'CSV_' . $newname . '.csv';
                    $csv_file = $path_csv . $file_csv;
                    $col = array();
                    $row = 0;
                    $fp = fopen($csv_file, 'w');
                    foreach ($results as $value) {
                        if ($row > 0) {
                            $insert = array(
                                $this->getSession('id_user'),
                                $value[0],
                                $value[1],
                                $value[2],
                                $value[2],
                                $value[2],
                                $value[3],
                                (int) $value[4],
                            );
                            fputcsv($fp, $insert, ',', chr(0));
                        }
                        $row++;
                    }
                    fclose($fp);
                    $result = $import->loadCSVGroup($csv_file);
                    echo 'Load table group : '.($result==1?'success':'fail').'<br>';

                    $col = array();
                    $row = 0;
                    $dir = 'uploads/mockupdata/';
                    $path = DOCUMENT_ROOT . $dir;
                    $path_csv = DOCUMENT_ROOT . $dir;
                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777);
                        umask($oldmask);
                    }
                    $file_csv = 'import_barcode';
                    $json = array();
                    $csv_file = $path_csv . $file_csv . '.csv';
                    $fp = fopen($csv_file, 'w');
                    foreach ($results2 as $value) {
                        if ($row > 0) {
                            $id_group = $group->findIdGroup($value[0]);

                            for ($i = (int) $value[1]; $i <= $value[2]; $i++) {
                                $insert = array(
                                    $this->getSession('id_user'),
                                    $id_group,
                                    $value[0],
                                    $i,
                                    (int)$value[6],
                                    (int)$value[5],
                                    $value[4],
                                    $value[4],
                                    // date('Y-m-d H:i:s', strtotime($value[4].' 00:00:00')),
                                    // date('Y-m-d H:i:s', strtotime($value[4].' 00:00:00')),
                                );
                                fputcsv($fp, $insert, ',', chr(0));
                            }
                        }
                        $row++;
                    }
                    fclose($fp);
                    $import->loadCSVBarcode($csv_file);
                    echo 'Load table barcode : '.($result==1?'success':'fail').'<br>';
                    echo $this->linkIndex();
                    exit();

                }

            }
        }
    }

    public function index()
    {
        $data = array();

        $import = $this->model('import');

        $data['tablerm'] = array(
            'mb_master_barcode',
            'mb_master_group',
            'mb_master_product'
        );

       

        $table = $import->getTables();
        $data['table'] = array();
        foreach ($table as $value) {
            $data['table'][] = $value['TABLE_NAME'];
        }

        $data['column'] = array();
        if (isset($_GET['table'])) {
            $column = $import->getColumns(get('table'));
            foreach ($column as $value) {
                $data['column'][] = $value['COLUMN_NAME'];
            }

        }


        $association = $this->model('association');
        $data['assdate'] = $association->getDateWK();

        $this->view('import/index', $data);
    }

    public function removeAssoiation() {
        $association = $this->model('association');
        $association->removeJunkSave($_POST['dateass']);
        echo 'success remove asssociation date : '.$_POST['dateass'];
        $this->linkIndex();
    }

    public function addDefaultGroup()
    {
        $group = $this->model('group');
        $group->addDefaultGroup();
    }
    public function generateJsonFreeGroup()
    {
        $association = $this->model('association');
        $lists = $association->getFreeGroup();
        $json = array();
        foreach ($lists as $value) {
            $json[] = $value;
        }
        $fp = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
        return $json;
    }
}
