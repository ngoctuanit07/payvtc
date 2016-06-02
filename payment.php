<?php 
    include 'libcard.php';
    $key='920130506!@#123';
    $url = 'http://sandbox2.vtcebank.vn/WSCard2010/card.asmx?wsdl';
    //$card = $_GET['card'];
    //$seri = $_GET['seri'];
    //$cardnumber = $_GET['cardnumber'];
    $libpayvtc=new libpay();
    $xml=$libpayvtc->cardexcute('920130506',$key,$url,'UseCard','80','040071002279954','802443105201','VMS|1733888|VTCTEST');
    print_r($xml);
//    echo "<textarea style='width:1024px;height:200px;'>".$xml."</textarea>";      
