<?php 
require_once DOCUMENT_ROOT.'/system/lib/PHPExcel/Classes/PHPExcel.php';
class ExportController extends Controller {

    public function association() {
        $association = $this->model('association');
        $config = $this->model('config');
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

        // Free Group
        $free_group = $this->jsonFreeGroup(false);
        $temp_freegroup = json_decode($free_group, true);
        $temp_freegroup = json_decode($temp_freegroup[0], true);
        $freegroup = array();
        foreach ($temp_freegroup as $v) {
            $freegroup[$v['group']] = $v['qty'];
        }

        // Config Relationship
        $config_relation = array();
        $temprelation = $config->getRelationship();
        foreach ($temprelation as $tr) {
            $config_relation[] = $tr['group'];
        }

        $oldSync = $association->getOldSync(); // Group barcode use with old association in config day
        $beforeSync = array(); 
        foreach ($oldSync as $v) {
            $beforeSync[] = $v['group_code'];
        }

        $i=0;

        $lists = $association->getProducts($date_wk);

        $notuse = array();
        foreach ($lists as $key => $value) {
            $last_week = $value['last_week'];
            $remaining_qty = (int)$value['remaining_qty'];
            // $remaining_qty = 0;

            $relation_group = $association->getRelationshipBySize($value['size'], $value['sum_prod']);
            $lists[$key]['relation_group'] = $relation_group;

            if ($value['sum_prod']>0) {
                if (!empty($relation_group['group']) && !empty($relation_group['qty'])) {
                    if (!in_array((int)$relation_group['group'],$notuse)) {
                        $notuse[] = (int)$relation_group['group'];
                    }
                }
                else if ($remaining_qty >= $value['sum_prod']) {
                    if (!in_array((int)$last_week,$notuse)) {
                        $notuse[] = (int)$last_week;
                    }
                }
            }
        }

        
        foreach ($lists as $key => $value) {
            $last_week = $value['last_week'];
            $remaining_qty = (int)$value['remaining_qty'];
            // $remaining_qty = 0;

            $relation_group = $value['relation_group'];

            $propose = '';
            $propose_remaining_qty = '';
            $message = '';

            if ($value['sum_prod']>0) {
                if (!empty($relation_group['group']) && !empty($relation_group['qty'])) {
                    $propose = $relation_group['group'];
                    $propose_remaining_qty = $relation_group['qty'];
                    $message = '<span class="text-primary">Relationship</span>';
                    unset($freegroup[$relation_group['group']]);
                } 
                else if ($remaining_qty >= $value['sum_prod']) {
                    $propose = $last_week;
                    $propose_remaining_qty = $remaining_qty;
                    $message = 'Last Weeek';
                    unset($freegroup[$last_week]);
                } 
                else {
                    $free = '';
                    $free_qty = '';

                    if (count($freegroup)>0) {
                        $keyfirst = array_keys($freegroup)[0];
                        if (!in_array($keyfirst,$beforeSync)) {
                            if ($freegroup[$keyfirst]>=$value['sum_prod'] && !in_array($keyfirst, $config_relation)) {
                                $free = $keyfirst;
                                $free_qty = $freegroup[$keyfirst];   
                                unset($freegroup[$keyfirst]); 
                            }
                        }
                    }
        
                    if (!empty($free)&&!empty($free_qty)) {
                        $propose = $free;
                        $propose_remaining_qty = $free_qty;
                        $message = !empty($free) ? '<span class="text-danger">Free Group</span>' : '';
                    }
                }
            }

            $text = '';
            $text = $message;

            $excel[] = array(
                'id_product' => $value['id_product'],
                'size' => $value['size'],
                'sum_prod' => $value['sum_prod'],
                'last_wk0' => $last_week,
                'remaining_qty' => number_format((int)round($remaining_qty,0),0),
                'propose' => $propose,
                'propose_remaining_qty' => number_format((int)round($propose_remaining_qty,0),0),
                'message' => $text,
                'save' => $value['save']
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

    public function pattern() {


        $start_group = get('start_group');
        $end_group = get('end_group');
        $purchase = $this->model('purchase');
        $group = $this->model('group');

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



        $json = $this->jsonGroupDefaultBarcode(false);
        $json = json_decode($json, true);
        // Get List
        $filter = array(
            'date_purchase' => date('Y-m-d'),
            'start_group' => $start_group,
            'end_group' => $end_group,
        );
        $i=1;
        $mapping = $purchase->getPurchases($filter);
        foreach ($mapping as $key => $value) {
            $value['barcode_start_year'] = $json[$value['group_code']]['start'];
            $value['barcode_end_year'] = $json[$value['group_code']]['end'];
            // $value['barcode_start_year'] = $purchase->getStartBarcodeOfYearAgo($value['group_code']);
            // $value['barcode_end_year'] = $purchase->getEndBarcodeOfYearAgo($value['group_code']);
            $barcode_use = $group->getGroupStatus($value['group_code']);
            $value['status'] = $barcode_use==="1" ? 'Recived' : ($barcode_use==="0" ? 'Waiting' : '');
            $value['status_id'] = $barcode_use;

            if (!empty($value['remaining_qty'])) {

                $temp = (int)$value['barcode_start'] - (int)$value['remaining_qty'];
                if ($temp< $value['default_start']) {

                    $excel[] = array(
                        '',
                        $i++,
                        '="'.sprintf('%08d', $value['default_end'] - ($value['remaining_qty']-($value['barcode_start']-$value['default_start']))+1).'"',
                        '="'.sprintf('%08d', $value['default_end']).'"',
                        '="'.number_format(($value['default_end']-($value['default_end'] - ($value['remaining_qty']-($value['barcode_start']-$value['default_start']))+1)+1),0).'"',
                       
                    );

                    $excel[] = array(
                        '',
                        $i++,
                        '="'.sprintf('%08d', $value['default_start']).'"',
                        '="'.sprintf('%08d', $value['barcode_start']-1).'"',
                        '="'.number_format((int)$value['barcode_start']-1-$value['default_start']+1,0).'"',
                       
                    );

                    
                } else {
                    $excel[] = array(
                        '',
                        $i++,
                        sprintf('%08d', $value['barcode_start']-$value['remaining_qty']),
                        '="'.sprintf('%08d', $value['barcode_start']-1).'"',
                        '="'.number_format((int)$value['remaining_qty'],0).'"',
                    );
                }

                
            }
        }

        $purchase = $this->model('purchase');
        $purchase->clearAjaxPurchase(); // clear data ajax

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }
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
            'Group prefix',
            'Start',
            'End',
            'Total'
        );

        $date = get('date');

        $results = $this->calcurateBarcode2($data['date']);
        foreach ($results as $value) {
            $ex = explode('-', $value['name']);
            $excel[] = array(
                '="'.$value['group'].'"',
                '="'.sprintf('%08d', trim($ex[0])).'"',
                '="'.sprintf('%08d', trim($ex[1])).'"',
                $value['count']
            );
        }

        // $barcode = $this->model('barcode');

        // $data_select = array(
        //     'date' => $date
        // );
        // $results = $barcode->getBarcode($data_select);
        // foreach ($results as $value) {
        //     $excel[] = array(
        //         $value['barcode_prefix'],
        //         $value['barcode_code'],
        //         $value['date_added'], // this date modify
        //         $value['username'],
        //     );
        // }

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }
        $name = 'export_importbarcode_date'.$date.'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }

    public function report() {
        $excel = array();
        $excel2 = array();

        $excel[] = array(
            'Group Prefix',
            'Start Barcode',
            'End Barcode',
            'Remaining QTY',
        );

        $excel2[] = array(
            'Group Prefix',
            'Start Barcode',
            'End Barcode',
            'Remaining QTY',
        );

        $temp = array();
        // $datas = $this->calcurateBarcode();
        $datas = $this->calcurateBarcode($_GET['group']);


        foreach ($datas as $val) {
            if (!isset($temp[$val['barcode_prefix']])) {
                $temp[$val['barcode_prefix']]['start'] = $val['start'];
                $temp[$val['barcode_prefix']]['qty'] = 0;
            }
            $temp[$val['barcode_prefix']]['end'] = $val['end'];
            $temp[$val['barcode_prefix']]['qty'] += (int)$val['qty'];

            $excel[] = array(
                $val['barcode_prefix'],
                $val['start'],
                $val['end'],
                $val['qty'],
            );
        }

        foreach ($temp as $key => $val) {
            $excel2[] = array(
                $key,
                $val['start'],
                $val['end'],
                $val['qty']
            );
        }

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }
        $name = 'export_report-remaining-qty_'.date('YmdHis').'.xlsx';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Admin")
                                     ->setLastModifiedBy("Admin")
                                     ->setTitle("Export Excel")
                                     ->setSubject("Export Excel")
                                     ->setDescription("Export Excel")
                                     ->setKeywords("export excel")
									 ->setCategory("export");
        

		$json = '["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","aa","ab","ac","ad","ae","af","ag","ah","ai","aj","ak","al","am","an","ao","ap","aq","ar","as","at","au","av","aw","ax","ay","az","ba","bb","bc","bd","be","bf","bg","bh","bi","bj","bk","bl","bm","bn","bo","bp","bq","br","bs","bt","bu","bv","bw","bx","by","bz","ca","cb","cc","cd","ce","cf","cg","ch","ci","cj","ck","cl","cm","cn","co","cp","cq","cr","cs","ct","cu","cv","cw","cx","cy","cz","da","db","dc","dd","de","df","dg","dh","di","dj","dk","dl","dm","dn","do","dp","dq","dr","ds","dt","du","dv","dw","dx","dy","dz","ea","eb","ec","ed","ee","ef","eg","eh","ei","ej","ek","el","em","en","eo","ep","eq","er","es","et","eu","ev","ew","ex","ey","ez","fa","fb","fc","fd","fe","ff","fg","fh","fi","fj","fk","fl","fm","fn","fo","fp","fq","fr","fs","ft","fu","fv","fw","fx","fy","fz","ga","gb","gc","gd","ge","gf","gg","gh","gi","gj","gk","gl","gm","gn","go","gp","gq","gr","gs","gt","gu","gv","gw","gx","gy","gz","ha","hb","hc","hd","he","hf","hg","hh","hi","hj","hk","hl","hm","hn","ho","hp","hq","hr","hs","ht","hu","hv","hw","hx","hy","hz","ia","ib","ic","id","ie","if","ig","ih","ii","ij","ik","il","im","in","io","ip","iq","ir","is","it","iu","iv","iw","ix","iy","iz","ja","jb","jc","jd","je","jf","jg","jh","ji","jj","jk","jl","jm","jn","jo","jp","jq","jr","js","jt","ju","jv","jw","jx","jy","jz","ka","kb","kc","kd","ke","kf","kg","kh","ki","kj","kk","kl","km","kn","ko","kp","kq","kr","ks","kt","ku","kv","kw","kx","ky","kz","la","lb","lc","ld","le","lf","lg","lh","li","lj","lk","ll","lm","ln","lo","lp","lq","lr","ls","lt","lu","lv","lw","lx","ly","lz","ma","mb","mc","md","me","mf","mg","mh","mi","mj","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nb","nc","nd","ne","nf","ng","nh","ni","nj","nk","nl","nm","nn","no","np","nq","nr","ns","nt","nu","nv","nw","nx","ny","nz","oa","ob","oc","od","oe","of","og","oh","oi","oj","ok","ol","om","on","oo","op","oq","or","os","ot","ou","ov","ow","ox","oy","oz","pa","pb","pc","pd","pe","pf","pg","ph","pi","pj","pk","pl","pm","pn","po","pp","pq","pr","ps","pt","pu","pv","pw","px","py","pz","qa","qb","qc","qd","qe","qf","qg","qh","qi","qj","qk","ql","qm","qn","qo","qp","qq","qr","qs","qt","qu","qv","qw","qx","qy","qz","ra","rb","rc","rd","re","rf","rg","rh","ri","rj","rk","rl","rm","rn","ro","rp","rq","rr","rs","rt","ru","rv","rw","rx","ry","rz","sa","sb","sc","sd","se","sf","sg","sh","si","sj","sk","sl","sm","sn","so","sp","sq","sr","ss","st","su","sv","sw","sx","sy","sz","ta","tb","tc","td","te","tf","tg","th","ti","tj","tk","tl","tm","tn","to","tp","tq","tr","ts","tt","tu","tv","tw","tx","ty","tz","ua","ub","uc","ud","ue","uf","ug","uh","ui","uj","uk","ul","um","un","uo","up","uq","ur","us","ut","uu","uv","uw","ux","uy","uz","va","vb","vc","vd","ve","vf","vg","vh","vi","vj","vk","vl","vm","vn","vo","vp","vq","vr","vs","vt","vu","vv","vw","vx","vy","vz","wa","wb","wc","wd","we","wf","wg","wh","wi","wj","wk","wl","wm","wn","wo","wp","wq","wr","ws","wt","wu","wv","ww","wx","wy","wz","xa","xb","xc","xd","xe","xf","xg","xh","xi","xj","xk","xl","xm","xn","xo","xp","xq","xr","xs","xt","xu","xv","xw","xx","xy","xz","ya","yb","yc","yd","ye","yf","yg","yh","yi","yj","yk","yl","ym","yn","yo","yp","yq","yr","ys","yt","yu","yv","yw","yx","yy","yz","za","zb","zc","zd","ze","zf","zg","zh","zi","zj","zk","zl","zm","zn","zo","zp","zq","zr","zs","zt","zu","zv","zw","zx","zy","zz"]';
		$char = json_decode($json, true);
        
        $objPHPExcel->setActiveSheetIndex(0);
		$row = 1;
		$index_char = 0;
		foreach ($excel as $key => $column) {
			foreach ($column as $k => $v) {
				// echo strtoupper($char[$index_char]).$row.' '.$v;
				// echo ',';
				$objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
				$index_char++;
			}
			// echo '<br>';
			$index_char = 0;
			$row++;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Report');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
		$row = 1;
		$index_char = 0;
		foreach ($excel2 as $key => $column) {
			foreach ($column as $k => $v) {
				// echo strtoupper($char[$index_char]).$row.' '.$v;
				// echo ',';
				$objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
				$index_char++;
			}
			// echo '<br>';
			$index_char = 0;
			$row++;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Group Report');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(1);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $filename = $name;
		$objWriter->save($doc.$filename);

        header('location:uploads/export/'.$filename);
        exit();
    }

    public function reportAll() {

        $excel = array();
        $excel2 = array();

        $excel[] = array(
            'Group Prefix',
            'Start Barcode',
            'End Barcode',
            'Remaining QTY',
        );

        $excel2[] = array(
            'Group Prefix',
            'Start Barcode',
            'End Barcode',
            'Remaining QTY',
        );

        $temp = array();
        

        $file_handle = fopen(DOCUMENT_ROOT . 'uploads/reportall.json', "r");
        while (!feof($file_handle)) {
            $line_of_text = fgets($file_handle);
            $json = json_decode($line_of_text, true);
            $json = json_decode($json,true);
            foreach ($json as $value) {
                foreach ($value['range'] as $k => $r) {
                    $range = explode('-', $r);

                    if (!isset($temp[$value['group']])) {
                        $temp[$value['group']]['start'] = trim($range[0]);
                        $temp[$value['group']]['qty'] = 0;
                    }
                    $temp[$value['group']]['end'] = trim($range[1]);
                    $temp[$value['group']]['qty'] += (int)str_replace(',','',$value['qty'][$k]);

                    $excel[] = array(
                        '="'.$value['group'].'"',
                        '="'.trim($range[0]).'"',
                        '="'.trim($range[1]).'"',
                        (int)str_replace(',','',$value['qty'][$k])
                    ); 
                }
            }
        }
        fclose($file_handle);
        
        foreach ($temp as $key => $val) {
            $excel2[] = array(
                $key,
                $val['start'],
                $val['end'],
                $val['qty']
            );
        }



        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Admin")
                                     ->setLastModifiedBy("Admin")
                                     ->setTitle("Export Excel")
                                     ->setSubject("Export Excel")
                                     ->setDescription("Export Excel")
                                     ->setKeywords("export excel")
									 ->setCategory("export");
        

		$json = '["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","aa","ab","ac","ad","ae","af","ag","ah","ai","aj","ak","al","am","an","ao","ap","aq","ar","as","at","au","av","aw","ax","ay","az","ba","bb","bc","bd","be","bf","bg","bh","bi","bj","bk","bl","bm","bn","bo","bp","bq","br","bs","bt","bu","bv","bw","bx","by","bz","ca","cb","cc","cd","ce","cf","cg","ch","ci","cj","ck","cl","cm","cn","co","cp","cq","cr","cs","ct","cu","cv","cw","cx","cy","cz","da","db","dc","dd","de","df","dg","dh","di","dj","dk","dl","dm","dn","do","dp","dq","dr","ds","dt","du","dv","dw","dx","dy","dz","ea","eb","ec","ed","ee","ef","eg","eh","ei","ej","ek","el","em","en","eo","ep","eq","er","es","et","eu","ev","ew","ex","ey","ez","fa","fb","fc","fd","fe","ff","fg","fh","fi","fj","fk","fl","fm","fn","fo","fp","fq","fr","fs","ft","fu","fv","fw","fx","fy","fz","ga","gb","gc","gd","ge","gf","gg","gh","gi","gj","gk","gl","gm","gn","go","gp","gq","gr","gs","gt","gu","gv","gw","gx","gy","gz","ha","hb","hc","hd","he","hf","hg","hh","hi","hj","hk","hl","hm","hn","ho","hp","hq","hr","hs","ht","hu","hv","hw","hx","hy","hz","ia","ib","ic","id","ie","if","ig","ih","ii","ij","ik","il","im","in","io","ip","iq","ir","is","it","iu","iv","iw","ix","iy","iz","ja","jb","jc","jd","je","jf","jg","jh","ji","jj","jk","jl","jm","jn","jo","jp","jq","jr","js","jt","ju","jv","jw","jx","jy","jz","ka","kb","kc","kd","ke","kf","kg","kh","ki","kj","kk","kl","km","kn","ko","kp","kq","kr","ks","kt","ku","kv","kw","kx","ky","kz","la","lb","lc","ld","le","lf","lg","lh","li","lj","lk","ll","lm","ln","lo","lp","lq","lr","ls","lt","lu","lv","lw","lx","ly","lz","ma","mb","mc","md","me","mf","mg","mh","mi","mj","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nb","nc","nd","ne","nf","ng","nh","ni","nj","nk","nl","nm","nn","no","np","nq","nr","ns","nt","nu","nv","nw","nx","ny","nz","oa","ob","oc","od","oe","of","og","oh","oi","oj","ok","ol","om","on","oo","op","oq","or","os","ot","ou","ov","ow","ox","oy","oz","pa","pb","pc","pd","pe","pf","pg","ph","pi","pj","pk","pl","pm","pn","po","pp","pq","pr","ps","pt","pu","pv","pw","px","py","pz","qa","qb","qc","qd","qe","qf","qg","qh","qi","qj","qk","ql","qm","qn","qo","qp","qq","qr","qs","qt","qu","qv","qw","qx","qy","qz","ra","rb","rc","rd","re","rf","rg","rh","ri","rj","rk","rl","rm","rn","ro","rp","rq","rr","rs","rt","ru","rv","rw","rx","ry","rz","sa","sb","sc","sd","se","sf","sg","sh","si","sj","sk","sl","sm","sn","so","sp","sq","sr","ss","st","su","sv","sw","sx","sy","sz","ta","tb","tc","td","te","tf","tg","th","ti","tj","tk","tl","tm","tn","to","tp","tq","tr","ts","tt","tu","tv","tw","tx","ty","tz","ua","ub","uc","ud","ue","uf","ug","uh","ui","uj","uk","ul","um","un","uo","up","uq","ur","us","ut","uu","uv","uw","ux","uy","uz","va","vb","vc","vd","ve","vf","vg","vh","vi","vj","vk","vl","vm","vn","vo","vp","vq","vr","vs","vt","vu","vv","vw","vx","vy","vz","wa","wb","wc","wd","we","wf","wg","wh","wi","wj","wk","wl","wm","wn","wo","wp","wq","wr","ws","wt","wu","wv","ww","wx","wy","wz","xa","xb","xc","xd","xe","xf","xg","xh","xi","xj","xk","xl","xm","xn","xo","xp","xq","xr","xs","xt","xu","xv","xw","xx","xy","xz","ya","yb","yc","yd","ye","yf","yg","yh","yi","yj","yk","yl","ym","yn","yo","yp","yq","yr","ys","yt","yu","yv","yw","yx","yy","yz","za","zb","zc","zd","ze","zf","zg","zh","zi","zj","zk","zl","zm","zn","zo","zp","zq","zr","zs","zt","zu","zv","zw","zx","zy","zz"]';
		$char = json_decode($json, true);
        
        $objPHPExcel->setActiveSheetIndex(0);
		$row = 1;
		$index_char = 0;
		foreach ($excel as $key => $column) {
			foreach ($column as $k => $v) {
				$objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
				$index_char++;
			}
			$index_char = 0;
			$row++;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Report');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
		$row = 1;
		$index_char = 0;
		foreach ($excel2 as $key => $column) {
			foreach ($column as $k => $v) {
				$objPHPExcel->getActiveSheet()->setCellValue(strtoupper($char[$index_char]).$row, $v);	
				$index_char++;
			}
			$index_char = 0;
			$row++;
		}
		$objPHPExcel->getARRctiveSheet()->setTitle('Group Report');
        $objPHPExcel->getSecurity()->setLockWindows(false);
        $objPHPExcel->getSecurity()->setLockStructure(false);
        $objPHPExcel->setActiveSheetIndex(1);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $name = 'export_report_allgroup_'.$date.'_'.date('YmdHis').'.xlsx';
        $filename = $name;
		$objWriter->save($doc.$filename);

        header('location:uploads/export/'.$filename);
        
        // $file = whiteExcel($excel, $doc, $name);
        // header('location:uploads/export/'.$file);
        exit();


    }

    public function setting_barcode() {
        $excel = array();

        $excel[] = array(
            'Group',
            'Start',
            'End',
            'Total'
        );

        $config = $this->model('config');
        $results = $config->getBarcodes();
        foreach ($results as $value) {
            $excel[] = array(
                '="'.sprintf('%03d',$value['group']).'"',
                '="'.sprintf('%08d',$value['start']).'"',
                '="'.sprintf('%08d',$value['end']).'"',
                $value['total']
            );
        }

        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }
        $name = 'export_config_barcode_'.$date.'_'.date('YmdHis').'.xlsx';

        
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }
    public function setting_relation() {
        $excel = array();

        $excel[] = array(
            'Group',
            'Size',
            'Remark'
        );

        $config = $this->model('config');
        $results = $config->getRelationship();
        foreach ($results as $value) {
            $excel[] = array(
                '="'.sprintf('%03d',$value['group']).'"',
                $value['size'],
                $value['comment']
            );
        }


        $doc = DOCUMENT_ROOT . 'uploads/export/';
        if (!file_exists($doc)) {
            $oldmask = umask(0);
            mkdir($doc, 0777);
            umask($oldmask);
        }
        $name = 'export_config_relation_'.$date.'_'.date('YmdHis').'.xlsx';
        $file = whiteExcel($excel, $doc, $name);
        header('location:uploads/export/'.$file);
        exit();
    }

    private function calcurateBarcode($group) {
        $data = array();
        $barcode = $this->model('barcode');
        $date = '';
        $data = $barcode->getRangeBarcode($group, '0', $date, 0);
        return $data;
    }
    private function calcurateBarcode2($date_wk='') {
        $input=array();
        if (!empty($date_wk)) { $input['date_wk'] = $date_wk; }
        $barcode = $this->model('barcode');

        $list1 = array();
        $list2 = array();
        $input['barcode_use'] = 1;
        $listbarcode = $barcode->getListBarcode($input); // ? ที่จองในระบบทั้งหมด
        foreach ($listbarcode as $key => $value) {
            $list1[] = (int)$value['barcode_code'];
        }
        $input['barcode_status'] = 1;
        $listbarcode = $barcode->getListBarcode($input); // ? ที่ใช้ไปแล้ว
        foreach ($listbarcode as $key => $value) {
            $list2[] = (int)$value['barcode_code'];
        }

        // ? get default alert
        $config = $this->model('config');
        $default_number_maximum_alert = $config->getConfig('config_maximum_alert'); // ? ค่าที่ตั้งไว้ว่าเกินเท่าไหร่ให้ alert
        return $this->calcurateDiffernce($list1, $list2, $default_number_maximum_alert);
    }
    private function calcurateBarcode3() {
        $input=array();
        $barcode = $this->model('barcode');

        $list1 = array();
        $list2 = array();

        $listbarcode = $barcode->getListBarcode(); // ? ที่จองในระบบ
        foreach ($listbarcode as $key => $value) {
            $list1[] = (int)$value['barcode_code'];
        }


        // $listimport = $barcode->getListImportBarcode($input); // ? ที่ Import เข้ามา
        $filter = array('barcode_status'=>1);
        $listbarcode2 = $barcode->getListBarcode($filter); // ? ที่ใช้ไปแล้ว 
        foreach ($listbarcode2 as $key => $value) {
            $list2[] = (int)$value['barcode_code'];
        }

        // ? get default alert
        $config = $this->model('config');
        $default_number_maximum_alert = $config->getConfig('config_maximum_alert'); // ? ค่าที่ตั้งไว้ว่าเกินเท่าไหร่ให้ alert
        return $this->calcurateDiffernce($list1, $list2, $default_number_maximum_alert);
    }
    private function calcurateDiffernce($list1, $list2, $default_number_maximum_alert) {
        sort($list1);
        sort($list2);
        $arr_diff = array_diff($list1, $list2); // ? ได้อาเรย์ ส่วนต่างที่ไม่เหมือนกัน
        $list_notfound = array_values($arr_diff); // ? reset key array

        $count = 0;
        $first = '';
        $end = '';
        $save = array();
        $group = '';
        foreach ($list_notfound as $key => $value) {
            if (isset($list_notfound[$key+1]) && $list_notfound[$key+1] == $value+1) { // ? ในกรณีที่ คียอันถัดไป เท่า ค่า+1 แสดงว่า ส่วนต่างที่ไม่มีนี้กำลังเรียง
                if (empty($first)) {
                    $save = array();
                    $first = $value;
                    if (strlen($value)==8) {
                        $group = substr($value, 0, 3);
                    } else if (strlen($value)==7) {
                        $group = sprintf('%03d', substr($value, 0, 2));
                    } else if (strlen($value)==6) {
                        $group = sprintf('%03d', substr($value, 0, 1));
                    }
                }
                $count++; // ? เริ่มนับจำนวนส่วนต่าง
            } else {
                if (empty($end)) {
                    $end = $value;
                    $save[] = $value;
                }
                $diff[] = array(
                    'name' => "$first - $end",
                    'group' => $group,
                    // 'barcodes' => $save, 
                    'count' => $count + 1 //  ? จำนวนระยะห่างที่หายไป +1 นับตัวแรกด้วย
                );
                $first = '';
                $end = '';
                $count = 0;
            }
            if ($count>0) {
                $save[] = $value;
            }
        }

        $text = array();
        foreach ($diff as $key => $value) {
            if ($value['count'] >= $default_number_maximum_alert) {
                $text[] = $value;
            }
        }

        return $text;
    }


    public function jsonGroupDefaultBarcode($header=true) {
        $json = array();
        if (!file_exists(DOCUMENT_ROOT . 'uploads/default_purchase.json')) {
            $this->generateJsonDefaultBarcode();
        }
        $file_handle = fopen(DOCUMENT_ROOT . 'uploads/default_purchase.json', "r");
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
    public function generateJsonDefaultBarcode() {
        $data = array();
        $purchase = $this->model('purchase');
        $config = $this->model('config');
        $groups = $config->getBarcodes();
        foreach ($groups as $value) {
            $data[$value['group']] = array(
                'start' => '',
                'end' => '',
            );
            $data[$value['group']]['start'] = $purchase->getStartBarcodeOfYearAgo($value['group']);
            $data[$value['group']]['end'] = $purchase->getEndBarcodeOfYearAgo($value['group']);
        }
        $fp = fopen(DOCUMENT_ROOT . 'uploads/default_purchase.json', 'w');
        fwrite($fp, json_encode($data));
        fclose($fp);
        return $data;
    }
}