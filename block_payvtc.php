<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class block_payvtc extends block_base {
    public function init() {
        $this->title = get_string('payvtc', 'block_payvtc');
    }

    public function get_content(){
	    if ($this->content !== null) {
	    	return $this->content;
	    }
	 
	    $this->content         =  new stdClass;
	    $this->content->text   = '<form class="thanh-toan-the-dt" action="payment.php" method="post">
                                        <lable>Loại thẻ</lable><input type="text" id="card" name="card">
	    				<lable>Số Seri</lable><input type="text" id="seri" name="seri">
	    				<lable>Số Thẻ</lable><input type="text" id="cardnumber" name="cardnumber">
	    				<input type="button" class="button" id="btnnaptien" value="NAP TIEN">
                                        <lable id="msg"></lable>
                                      </form>                                      
                                        ';
	    $this->content->footer = '';
	 
	    return $this->content;
    }
    
}
