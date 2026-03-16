<?php

class Pdf_generation extends MY_Controller {

    public function __construct() {
        parent::__construct();
//        $this->load->model("agent_store_documents_model");
    }


   

    function mpdf_view() {

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
        $html = $this->load->view("invoice", $this->data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

}
