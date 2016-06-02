<?php 

    class libpay
    {
       function Encrypt($input, $key_seed){
            $input = trim($input);
            $block = mcrypt_get_block_size('tripledes', 'ecb');
            $len = strlen($input);
            $padding = $block - ($len % $block);
            $input .= str_repeat(chr($padding),$padding);
            // generate a 24 byte key from the md5 of the seed
            $key = substr(md5($key_seed),0,24);
            $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            // encrypt
            $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key,
            $input, MCRYPT_MODE_ECB, $iv);
            // clean up output and return base64 encoded
            return base64_encode($encrypted_data);
       } //end function Encrypt()   
       
       function Decrypt($input, $key_seed)
       {
            $input = base64_decode($input);
            $key = substr(md5($key_seed),0,24);
            $text=mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input,
            MCRYPT_MODE_ECB,'12345678');
            $block = mcrypt_get_block_size('tripledes', 'ecb');
            $packing = ord($text{strlen($text) - 1});
            if($packing and ($packing < $block)){
            for($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--){
            if(ord($text{$P}) != $packing){
            $packing = 0;
            } } }
            $text = substr($text,0,strlen($text) - $packing);
            return $text;
        }
        function getError($status) {
            switch ($status) {
                case -1:
                    return "Thẻ đã sử dụng.";
                case -2:
                    return "Thẻ đã bị khóa.";
                case -3:
                    return "Thẻ đã hết hạn sử dụng.";
                case -4: 
                    return "Thẻ chưa kích hoạt.";
                case -5: 
                    return "TransID không hợp lệ.";
                case -6:
                    return "Mã số và số seri không khớp.";
                case -8:
                    return "Cảnh báo số lần giao dịch lỗi của một tài khoản.";
                case -9:
                    return "Thẻ thử quá số lần cho phép.";
                case -10:
                    return "CardID không hợp lệ.";
                case -11: 
                    return "CardCode không hợp lệ.";
                case -12:
                    return "Thẻ không tồn tại.";
                case -13:
                    return "Thẻ sai cấu trúc Descriptions.";
                case -14:
                    return "Mã dịch vụ không tồn tại.";
                case -15:
                    return "Thiếu thông tin khách hàng.";
                case -16:
                    return "Mã giao dịch không hợp lệ.";
                case -90:
                    return "Sai tên hàm.";
                case -98 || -99:
                    return "Giao dịch thất bại do lỗi hệ thống.";
                case -999:
                    return "Hệ thống Telco tạm ngừng.";
                case -100:
                    return "Giao dịch nghi vấn.";
                default:
                    return "";
            }          
        }
        function cardexcute($PartnerID,$key,$URL,$fun,$port,$cardid,$cardcode,$des){
            //$urlpost="http://sandbox2.vtcebank.vn/WSCard2010/card.asmx?wsdl";
                $cardfun="<?xml version=\"1.0\" encoding=\"utf-16\"?>\n" .
                "<CardRequest>\n" .
                "<Function>".$fun."</Function>\n" .
                "<CardID>".$cardid."</CardID>\n" .
                "<CardCode>".$cardcode."</CardCode>\n" .
                "<Description>".$des."</Description>\n" .
                "</CardRequest>";
               $libPayF=new libpay();
               //$RequestData = $cardfun;
               $RequestData=$libPayF->Encrypt($cardfun,$key);
               $urlpar=parse_url($URL);
               
               $xml_data="<?xml version=\"1.0\" encoding=\"utf-8\"?>"
                ."<soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">"
                . "<soap:Body>"
                . "<Request xmlns=\"VTCOnline.Card.WebAPI\">"
                . "<PartnerID>".$PartnerID."</PartnerID>"
                . "<RequestData>".$RequestData."</RequestData>"
                . "</Request>"
                . "</soap:Body>"
                . "</soap:Envelope>";
             
                $headers = array(
                "POST ".$urlpar['path']." HTTP/1.1",
                "Host: ".$urlpar['host']."",
                "Content-Type: text/xml; charset=utf-8",
                "SOAPAction: VTCOnline.Card.WebAPI/Request",
                "Content-Length: ".strlen($xml_data)
                );
                $ch = curl_init(); // initialize curl handle 
                curl_setopt($ch, CURLOPT_VERBOSE, 1); // set url to post to 
                curl_setopt($ch, CURLOPT_PORT, $port);
                curl_setopt($ch, CURLOPT_URL, $URL); // set url to post to 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable 
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
                curl_setopt($ch, CURLOPT_HEADER, 1); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 40); // times out after 4s 
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data); // add POST fields 
                curl_setopt($ch, CURLOPT_POST, 1); 
                $result = curl_exec($ch); // run the whole process 
                curl_close($ch); // close cURL resource, and free up system resources
                $startIndex = strpos($result, "<RequestResult>") + strlen("<RequestResult>");
                $length = strpos($result, "</RequestResult>") - $startIndex;
                $strXML = substr($result, $startIndex , $length);
                // Giải mã result
                $temp = $libPayF->Decrypt($strXML,$key);             
                $xml = simplexml_load_string(preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $temp));
                return $temp;                                         
        }
        
       
    }
?>
