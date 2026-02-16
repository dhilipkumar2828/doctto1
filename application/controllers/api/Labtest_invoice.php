<?php

class Labtest_invoice extends MY_Controller {

    public function __construct() {
        parent::__construct();
//        $this->load->model("agent_store_documents_model");
    }

    function view($appointment_id) {
        
        
                             $this->db->select('doctor_id');
                             $this->db->where('id',$appointment_id);
    $this->data['doctor_id'] = $this->db->get('doctor_appointments')->row();    
    
                             
                             $this->db->where('id',$this->data['doctor_id']->doctor_id);
    $this->data['doc_det'] = $this->db->get('doctors')->row();  


                          $this->db->where('id',$this->data['doc_det']->designations);
    $this->data['desg'] = $this->db->get('designations')->row()->name; 

    
    // print_r($this->data['doc_det']);die;
    
                             $this->db->where('id',$appointment_id);
    $this->data['patient'] = $this->db->get('doctor_appointments')->row();
    

    
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','diagnosis');
    $this->data['diag'] = $this->db->get('patient_prescription')->row();
  
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','prescription');
    $this->data['pres'] = $this->db->get('patient_prescription')->row();
    
                            $this->db->select('id');
                            $this->db->where('appointment_id',$appointment_id);
    $this->data['epresp'] = $this->db->get('patient_prescription')->row(); 
    
  
                          
                                   $this->db->where('patient_prescription_id',$this->data['epresp']->id);
    $this->data['labtest'] = $this->db->get('lab_tests')->result();

        $base_path = str_replace("system", "vendor", BASEPATH);

        require_once $base_path . '/autoload.php';


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
        $mpdf->Output();
    }

}
