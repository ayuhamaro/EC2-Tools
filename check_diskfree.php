<?php

    class checkDiskFree{

        private $admin_mail = 'admin@yourdomain.com';

        private function __send_mail($msg){
            $subject = sprintf("EC2磁碟容量警報(%s)", date("Y-m-d H:i:s")); //信件標題
            $headers = "From: service@yourdomain.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";

            if(mail($this->admin_mail, $subject, $msg, $headers)){
                echo $this->admin_mail." 發送成功\n";
            }else{
                echo $this->admin_mail." 發送失敗\n";
            }
        }

        public function check(){
            $df_result = shell_exec("df / | awk '{ print $2 \" \" $3}'");
            preg_match_all('/\d{2,}/', $df_result, $match);

            if(count($match[0]) == 2 && is_numeric($match[0][0]) && is_numeric($match[0][1])){
                $total = (int)$match[0][0];
                $used =  (int)$match[0][1];
                $scale = ceil(($used/$total) * 100);

                if($scale > 80){
                    $this->__send_mail("<p>磁碟已使用$scale%<p>");
                }else{
                    echo "容量足夠\n";
                }
            }else{
		$this->__send_mail("<p>解析錯誤<p>");
                echo "解析錯誤\n";
            }

        }

    }

    $checkDiskFree = new checkDiskFree;
    $checkDiskFree->check();
