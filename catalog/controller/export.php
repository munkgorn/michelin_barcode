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

        $freegroup = $this->jsonFreeGroup(false);
        $jsonfreegroup = json_decode($freegroup, true);
        $freegroup = json_decode($jsonfreegroup[0], true);

        $i=0;

        $date_lastweek = $association->getDateLastWeek();
        $products = $association->getProducts($date_wk);
        foreach ($products as $product) {
            $last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($product['size'], $date_lastweek) : '';
            $remaining_qty = 0;
            if (!empty($last_week)) {
                $groupReceived = $association->getGroupReceived($last_week);
                $barcodeUse = $association->getBarcodeUse($last_week);
                if ($groupReceived!=false) {
                    if ($barcodeUse==0) {
                        $remaining_qty = $groupReceived;
                    } else {
                        $remaining_qty = $groupReceived - $barcodeUse;
                    }
                }
            }
            // $remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;

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
            } else { // Use free group in json file
                $propose = (int)$freegroup[$i];
                $message = 'Free group';
                $i++;
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

    public function jsonFreeGroup($header=true) {
        $json = array();
        if (!file_exists(DOCUMENT_ROOT . 'uploads/freegroup.json')) {
            $this->generateJsonFreeGroup();
        }
        $file_handle = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', "r");
        while(!feof($file_handle)){
            $line_of_text = fgets($file_handle);
            $json[] = $line_of_text;
        }
        fclose($file_handle);
        if ($header) {
            $this->json($json);
        } else {
            return json_encode($json);
        }
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

    public function groupPattern() {

        $excel = array();

        $excel[] = array(
            'BARCODE FOR PCLT',
        );
        $excel[] = array(
            'PART NO.: ______________________________',
            '',
            '',
            '',
            '',
            '',
            '______________________________'
        );
        $excel[] = array(
            'BUYER: ______________________________',
            '',
            '',
            '',
            'ID. ______________________________',
            '',
            'Tel. ______________________________'
        );
        $excel[] = array(
            'Order date: ______________________________',
            '',
            '',
            '',
            'Needed Date: ______________________________'
        );

        $excel[] = array(
            '[______]',
            'Barcode Non-VMI: DURATACK_PG PRINTED LABEL WIDETH = 7 mm. LENGTH = 32.60 mm.'
        );

        $excel[] = array(
            '[______]',
            'BARCODE VMI MAXX - DURATACK-PG LABEL MIC008'
        );

        $excel[] = array();

        $excel[] = array(
            '',
            'No.',
            // 'Group Prefix',
            'Start',
            'End',
            'Qty',
            ''
            // 'Status',
            // 'Purchase Date',
            // 'Create By',
        );

        $group = $this->model('group');
        $filter = array(
            'date_modify' => get('date'),
            'group_code' => get('group'),
            'barcode_use' => get('status')>=0 ? get('status') : null,
            'has_remainingqty' => true
        );
        $datas = $group->getGroups($filter);
        $i=1;
        foreach ($datas as $val) {
            $excel[] = array(
                '',
                $i++,
                // $val['group_code'],
                $val['start']-$val['remaining_qty'],
                $val['start']-1,
                $val['remaining_qty'],
                // ($val['barcode_use']==1?'Received':'Waiting'),
                // $val['date_added'],
                // $val['username']
            );
        }


        $doc = DOCUMENT_ROOT . 'uploads/export/';
        $name = 'export_group_date'.$filter['date_modify'].'-group'.$filter['group_code'].'-barcode'.$filter['barcode_use'].'_'.date('YmdHis').'.xlsx';

        // $file = whiteExcel($excel, $doc, $name);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Admin")
                                     ->setLastModifiedBy("Admin")
                                     ->setTitle("Export Excel")
                                     ->setSubject("Export Excel")
                                     ->setDescription("Export Excel")
                                     ->setKeywords("export excel")
									 ->setCategory("export");
									 $objPHPExcel->setActiveSheetIndex(0);

		$json = '["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","aa","ab","ac","ad","ae","af","ag","ah","ai","aj","ak","al","am","an","ao","ap","aq","ar","as","at","au","av","aw","ax","ay","az","ba","bb","bc","bd","be","bf","bg","bh","bi","bj","bk","bl","bm","bn","bo","bp","bq","br","bs","bt","bu","bv","bw","bx","by","bz","ca","cb","cc","cd","ce","cf","cg","ch","ci","cj","ck","cl","cm","cn","co","cp","cq","cr","cs","ct","cu","cv","cw","cx","cy","cz","da","db","dc","dd","de","df","dg","dh","di","dj","dk","dl","dm","dn","do","dp","dq","dr","ds","dt","du","dv","dw","dx","dy","dz","ea","eb","ec","ed","ee","ef","eg","eh","ei","ej","ek","el","em","en","eo","ep","eq","er","es","et","eu","ev","ew","ex","ey","ez","fa","fb","fc","fd","fe","ff","fg","fh","fi","fj","fk","fl","fm","fn","fo","fp","fq","fr","fs","ft","fu","fv","fw","fx","fy","fz","ga","gb","gc","gd","ge","gf","gg","gh","gi","gj","gk","gl","gm","gn","go","gp","gq","gr","gs","gt","gu","gv","gw","gx","gy","gz","ha","hb","hc","hd","he","hf","hg","hh","hi","hj","hk","hl","hm","hn","ho","hp","hq","hr","hs","ht","hu","hv","hw","hx","hy","hz","ia","ib","ic","id","ie","if","ig","ih","ii","ij","ik","il","im","in","io","ip","iq","ir","is","it","iu","iv","iw","ix","iy","iz","ja","jb","jc","jd","je","jf","jg","jh","ji","jj","jk","jl","jm","jn","jo","jp","jq","jr","js","jt","ju","jv","jw","jx","jy","jz","ka","kb","kc","kd","ke","kf","kg","kh","ki","kj","kk","kl","km","kn","ko","kp","kq","kr","ks","kt","ku","kv","kw","kx","ky","kz","la","lb","lc","ld","le","lf","lg","lh","li","lj","lk","ll","lm","ln","lo","lp","lq","lr","ls","lt","lu","lv","lw","lx","ly","lz","ma","mb","mc","md","me","mf","mg","mh","mi","mj","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nb","nc","nd","ne","nf","ng","nh","ni","nj","nk","nl","nm","nn","no","np","nq","nr","ns","nt","nu","nv","nw","nx","ny","nz","oa","ob","oc","od","oe","of","og","oh","oi","oj","ok","ol","om","on","oo","op","oq","or","os","ot","ou","ov","ow","ox","oy","oz","pa","pb","pc","pd","pe","pf","pg","ph","pi","pj","pk","pl","pm","pn","po","pp","pq","pr","ps","pt","pu","pv","pw","px","py","pz","qa","qb","qc","qd","qe","qf","qg","qh","qi","qj","qk","ql","qm","qn","qo","qp","qq","qr","qs","qt","qu","qv","qw","qx","qy","qz","ra","rb","rc","rd","re","rf","rg","rh","ri","rj","rk","rl","rm","rn","ro","rp","rq","rr","rs","rt","ru","rv","rw","rx","ry","rz","sa","sb","sc","sd","se","sf","sg","sh","si","sj","sk","sl","sm","sn","so","sp","sq","sr","ss","st","su","sv","sw","sx","sy","sz","ta","tb","tc","td","te","tf","tg","th","ti","tj","tk","tl","tm","tn","to","tp","tq","tr","ts","tt","tu","tv","tw","tx","ty","tz","ua","ub","uc","ud","ue","uf","ug","uh","ui","uj","uk","ul","um","un","uo","up","uq","ur","us","ut","uu","uv","uw","ux","uy","uz","va","vb","vc","vd","ve","vf","vg","vh","vi","vj","vk","vl","vm","vn","vo","vp","vq","vr","vs","vt","vu","vv","vw","vx","vy","vz","wa","wb","wc","wd","we","wf","wg","wh","wi","wj","wk","wl","wm","wn","wo","wp","wq","wr","ws","wt","wu","wv","ww","wx","wy","wz","xa","xb","xc","xd","xe","xf","xg","xh","xi","xj","xk","xl","xm","xn","xo","xp","xq","xr","xs","xt","xu","xv","xw","xx","xy","xz","ya","yb","yc","yd","ye","yf","yg","yh","yi","yj","yk","yl","ym","yn","yo","yp","yq","yr","ys","yt","yu","yv","yw","yx","yy","yz","za","zb","zc","zd","ze","zf","zg","zh","zi","zj","zk","zl","zm","zn","zo","zp","zq","zr","zs","zt","zu","zv","zw","zx","zy","zz"]';
        $char = json_decode($json, true);
        
        $styleTextCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '333333')
                )
            )
        );
		
		$row = 1;
		$index_char = 0;
		foreach ($excel as $key => $column) {
			foreach ($column as $k => $v) {
                $objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
                if ($row>=7) {
                    
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleTextCenter);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleBorder);
                    // $objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D1'.$row);
                }
				$index_char++;
			}
			$index_char = 0;
			$row++;
        }

        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleTextCenter);
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($styleTextCenter);
        $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($styleTextCenter);
        


		$objPHPExcel->getActiveSheet()->setTitle('Export Excel');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $filename = $name;
		$objWriter->save($doc.$filename);
        // return $filename;
        
        header('location:uploads/export/'.$filename);
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