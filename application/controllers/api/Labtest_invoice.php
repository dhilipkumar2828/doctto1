<?php

class Labtest_invoice extends MY_Controller {

    public function __construct() {
        parent::__construct();
//        $this->load->model("agent_store_documents_model");
    }

    function view($appointment_id) {
        
                             $this->db->select('doctor_id');
                             $this->db->where('id',$appointment_id);
               $doctor_row = $this->db->get('doctor_appointments')->row();
        if (!$doctor_row) {
            echo "Appointment not found.";
            return;
        }
        $this->data['doctor_id'] = $doctor_row;

        $this->db->where('id', $this->data['doctor_id']->doctor_id);
        $doc_det = $this->db->get('doctors')->row();
        if (!$doc_det) {
            echo "Doctor not found.";
            return;
        }
        $this->data['doc_det'] = $doc_det;

        $this->db->where('id', $this->data['doc_det']->designations);
        $desg_row = $this->db->get('designations')->row();
        $this->data['desg'] = $desg_row ? $desg_row->name : '';

        // Fetch specialisation
        $this->data['spcl'] = '';
        if (!empty($this->data['doc_det']->specialisation)) {
            $this->db->where('id', $this->data['doc_det']->specialisation);
            $spcl_row = $this->db->get('specialisation')->row();
            $this->data['spcl'] = $spcl_row ? $spcl_row->name : '';
        }

    
    // print_r($this->data['doc_det']);die;
    
                             $this->db->where('id',$appointment_id);
    $this->data['patient'] = $this->db->get('doctor_appointments')->row();
    

                                   $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','diagnosis');
    $diag = $this->db->get('patient_prescription')->row();
    $this->data['diag'] = $diag ? $diag : (object)[
        'chief_complaints' => '',
        'diagnosis' => '',
        'investigation' => '',
        'advice' => '',
        'followup' => ''
    ];
  
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','prescription');
    $this->data['pres'] = $this->db->get('patient_prescription')->row();
    
                            $this->db->select('id');
                            $this->db->where('appointment_id',$appointment_id);
    $epresp = $this->db->get('patient_prescription')->row(); 
    $this->data['epresp'] = $epresp;
    
    $this->data['labtest'] = array();
    if ($epresp) {
        $this->db->where('patient_prescription_id', $epresp->id);
        $this->data['labtest'] = $this->db->get('lab_tests')->result();
    }

        $base_path = str_replace("system", "vendor", BASEPATH);

        require_once $base_path . '/autoload.php';

        error_reporting(0);
        ini_set('display_errors', 0);


        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 50,
            'margin_bottom' => 10
        ]);

        $this->data["mpdf"] = $mpdf;

        $mpdf->SetWatermarkImage(
                base_url() , 0.05, ''
        );
        $mpdf->showWatermarkImage = true;
        $mpdf->use_kwt = true;
        $mpdf->autoPageBreak = true;

        //$html = file_get_contents($_GET['url']);
        //$html = $this->load->view("pos_agreement/index", $this->data, true);
        $html = $this->load->view("labtest_invoice", $this->data, true);
        $mpdf->WriteHTML($html);
        
        if (ob_get_length()) ob_clean();
        $mpdf->Output();
    }

}
