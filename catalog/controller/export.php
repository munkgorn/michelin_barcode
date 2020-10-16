<?php 
require_once DOCUMENT_ROOT.'/system/lib/PHPExcel/Classes/PHPExcel.php';
class ExportController extends Controller {

    public function association() {
        $association = $this->model('association');
        $date_wk = get('date');

        $excel = array();
        $excel[] = array(
            'ID',
            'Size Product Code',
            'Sum Product',
            'Last Week 0',
            'Last Week 0 Remaining QTY',
            'Propose',
            'Propose Remaining QTY',
            'Message',
            'Validated',
        );

        $date_lastweek = $association->getDateLastWeek();
        $products = $association->getProducts($date_wk);
        foreach ($products as $product) {
            $last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($product['size'], $date_lastweek) : '';
            $remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;

            $relation_group = $association->getRelationshipBySize($product['size']);

            $propose = '';
            $propose_remaining_qty = '';
            $message = '';

            if ($remaining_qty>=$product['sum_prod']) {
                $propose = $last_week;
                $propose_remaining_qty = $remaining_qty;
                $message = 'Last Weeek' ;
            } else if (!empty($relation_group)) {
                $qty = $association->getRemainingByGroup($relation_group);
                if ($propose_remaining_qty>=$product['sum_prod']) {
                    $propose = $relation_group;
                    $propose_remaining_qty = $qty;
                    $message = 'Relationship';
                }
            }

            $text = '';
            $text = $message;

            $excel[] = array(
                'id_product' => $product['id_product'],
                'size' => $product['size'],
                'sum_prod' => $product['sum_prod'],
                'last_wk0' => $last_week,
                'remaining_qty' => number_format((int)$remaining_qty,0),
                'propose' => $propose,
                'propose_remaining_qty' => number_format((int)$propose_remaining_qty,0),
                'message' => $text,
                'save' => $product['group_code']
            );

          
        }

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        $name = 'export_association_date_'.$date_wk.'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }

    public function purchase() {
        $excel = array();

        $start_group = get('start_group');
        $end_group = get('end_group');
        $purchase = $this->model('purchase');
        $group = $this->model('group');

        // 3 year ago
        $date_first_3_year = date('Y-m-d', strtotime($purchase->getStartDateOfYearAgo()));
        $date_lasted_order = date('Y-m-d', strtotime($purchase->getEndDateOfYearAgo()));

        $excel[] = array(
            'Group',
            'Next Order Start',
            'Next Order End',
            'QTY',
            $date_first_3_year.' Start (First NB from oldest order)',
            $date_lasted_order.' End (Last NB from oldest order)',
            'Prefix Start',
            'Prefix End',
            'Prefix Range',
            'Status'
        );

        // Get List
        $filter = array(
            'start_group' => $start_group,
            'end_group' => $end_group
        );
        
        $mapping = $purchase->getPurchases($filter);
        foreach ($mapping as $key => $value) {
            $value['barcode_start_year'] = $purchase->getStartBarcodeOfYearAgo($value['group_code']);
            $value['barcode_end_year'] = $purchase->getEndBarcodeOfYearAgo($value['group_code']);
            $barcode_use = $group->getGroupStatus($value['group_code']);
            $value['status'] = $barcode_use==="1" ? 'Recived' : ($barcode_use==="0" ? 'Waiting' : '');
            $value['status_id'] = $barcode_use;

            $excel[] = array(
                $value['group_code'],
                sprintf('%06d', $value['barcode_start']),
                '="'.sprintf('%06d', $value['barcode_end']).'"',
                ($value['status_id']==0&&$value['remaining_qty']>0 ? $value['remaining_qty'] : ''),
                $value['barcode_start_year'],
                $value['barcode_end_year'],
                $value['default_start'],
                $value['default_end'],
                $value['default_range'],
                $value['status']
            );
        }
        
        $doc = DOCUMENT_ROOT . 'uploads/export/';
        $name = 'export_purchase_group'.$start_group.'-'.$end_group.'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }

    public function group() {
        $excel = array();

        $excel[] = array(
            'Group Prefix',
            'Start',
            'End',
            'QTY',
            'Status',
            'Purchase Date',
            'Create By',
        );

        $group = $this->model('group');
        $filter = array(
            'date_modify' => get('date'),
            'group_code' => get('group'),
            'barcode_use' => get('status')>=0 ? get('status') : null,
            'has_remainingqty' => true
        );
        $datas = $group->getGroups($filter);
        foreach ($datas as $val) {
            $excel[] = array(
                $val['group_code'],
                $val['start']-$val['remaining_qty'],
                $val['start']-1,
                $val['remaining_qty'],
                ($val['barcode_use']==1?'Received':'Waiting'),
                $val['date_added'],
                $val['username']
            );
        }


        $doc = DOCUMENT_ROOT . 'uploads/export/';
        $name = 'export_group_date'.$filter['date_modify'].'-group'.$filter['group_code'].'-barcode'.$filter['barcode_use'].'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }

    public function barcode() {

        $excel = array();

        $excel[] = array(
            'Prefix',
            'Barcode',
            'Used Date',
            'Create By'
        );

        $date = get('date');

        $barcode = $this->model('barcode');

        $data_select = array(
            'date' => $date
        );
        $results = $barcode->getBarcode($data_select);
        foreach ($results as $value) {
            $excel[] = array(
                $value['barcode_prefix'],
                $value['barcode_code'],
                $value['date_added'], // this date modify
                $value['username'],
            );
        }

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        $name = 'export_importbarcode_date'.$date.'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }
}