<?php
    function my_pagination($showUrl, $perPage = 6, $totalRows, $isJsonPagination = false) {
    if (strpos($_SERVER['REQUEST_URI'], '?')) {
        $sufix = '&' . http_build_query($_GET, '', "&");
    } else
        $sufix = '?' . http_build_query($_GET, '', "&");
    $sufix = explode('&', $sufix);
    $c = count($sufix);
    $v = array();
    for ($i = 0; $i < $c; $i++) {
        $ss = substr($sufix[$i], 0, 4);
        if ($ss != 'page' && $sufix[$i] != '') {
            $v[] = $sufix[$i];
        }
    }
    $cd = implode('&', $v);
    count($_GET) == 0 ? $showUrl .= $cd : $showUrl .= "?" . $cd;
    $ci = &get_instance();
    $ci->load->library("pagination");
    $cfg = array(
        'base_url' => base_url() . $showUrl,
        'total_rows' => $totalRows,
        'per_page' => $perPage,
        'use_page_numbers' => TRUE,
        'enable_query_strings' => TRUE,
        'page_query_string' => TRUE,
        'query_string_segment' => 'page',
            //'suffix' => '?'.http_build_query($_GET, '', "&")
    );

    $cfg['full_tag_open'] = '  <div class="pagination-bx clearfix"> <ul class="pagination">';
    $cfg['full_tag_close'] = '</ul></div>';
    // $cfg['prev_link'] = '';
    $cfg['prev_tag_open'] = ' <li class="previous">';
    $cfg['prev_tag_close'] = '</li>';
    // $cfg['next_link'] = '';
    $cfg['next_tag_open'] = '<li class="next">';
    $cfg['next_tag_close'] = '</li>';
    if ($isJsonPagination) {
        $cfg['cur_tag_open'] = '<li class="active"><a>';
    } else {
        $cfg['cur_tag_open'] = '<li class="active"><a href="' . base_url() . $showUrl . '#" >';
    }
    $cfg['cur_tag_close'] = '</a></li>';
    $cfg['num_tag_open'] = '<li>';
    $cfg['num_tag_close'] = '</li>';
    $cfg['first_tag_open'] = '<li>';
    $cfg['first_tag_close'] = '</li>';
    $cfg['last_tag_open'] = '<li>';
    $cfg['last_tag_close'] = '</li>';
    $cfg['first_link'] = '<li></i>';
    $cfg['last_link'] = '<li></i>';
    $ci->pagination->initialize($cfg);
    $page = 0;
    if (isset($_GET['page'])) {
        $page = $_GET['page'] > 0 ? $_GET['page'] : 0;
    }
    //$data['report']=$this->Salemodel->get_user_transaction($cfg["per_page"],$page);
    $data['page'] = $page;
    $data["pagination"] = $ci->pagination->create_links();
    $data["pagination_helper"] = $ci->pagination;
    return $data;
}




function my_pagination_cmoon($showUrl, $perPage = 6, $totalRows, $isJsonPagination = false) {
    if (strpos($_SERVER['REQUEST_URI'], '?')) {
        $sufix = '&' . http_build_query($_GET, '', "&");
    } else
        $sufix = '?' . http_build_query($_GET, '', "&");
    $sufix = explode('&', $sufix);
    $c = count($sufix);
    $v = array();
    for ($i = 0; $i < $c; $i++) {
        $ss = substr($sufix[$i], 0, 4);
        if ($ss != 'page' && $sufix[$i] != '') {
            $v[] = $sufix[$i];
        }
    }
    $cd = implode('&', $v);
    count($_GET) == 0 ? $showUrl .= $cd : $showUrl .= "?" . $cd;
    $ci = &get_instance();
    $ci->load->library("pagination");
    $cfg = array(
        'base_url' => base_url() . $showUrl,
        'total_rows' => $totalRows,
        'per_page' => $perPage,
        'use_page_numbers' => TRUE,
        'enable_query_strings' => TRUE,
        'page_query_string' => TRUE,
        'query_string_segment' => 'page',
            //'suffix' => '?'.http_build_query($_GET, '', "&")
    );

    $cfg['full_tag_open'] = '<div class="pull-right" ><ul class="pagination m-b-0">';
    $cfg['full_tag_close'] = '</ul></div>';
    // $cfg['prev_link'] = '';
    $cfg['prev_tag_open'] = '<li>';
    $cfg['prev_tag_close'] = '</li>';
    // $cfg['next_link'] = '';
    $cfg['next_tag_open'] = '<li>';
    $cfg['next_tag_close'] = '</li>';
    if ($isJsonPagination) {
        $cfg['cur_tag_open'] = '<li class="active"><a>';
    } else {
        $cfg['cur_tag_open'] = '<li class="active"><a href="' . base_url() . $showUrl . '#">';
    }
    $cfg['cur_tag_close'] = '</a></li>';
    $cfg['num_tag_open'] = '<li>';
    $cfg['num_tag_close'] = '</li>';
    $cfg['first_tag_open'] = '<li>';
    $cfg['first_tag_close'] = '</li>';
    $cfg['last_tag_open'] = '<li>';
    $cfg['last_tag_close'] = '</li>';
    // $cfg['first_link'] = '<li></i>';
    // $cfg['last_link'] = '<li></i>';
    $ci->pagination->initialize($cfg);
    $page = 0;
    if (isset($_GET['page'])) {
        $page = $_GET['page'] > 0 ? $_GET['page'] : 0;
    }
    //$data['report']=$this->Salemodel->get_user_transaction($cfg["per_page"],$page);
    $data['page'] = $page;
    $data["pagination"] = $ci->pagination->create_links();
    $data["pagination_helper"] = $ci->pagination;
    return $data;
}

?>

