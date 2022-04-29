<?php 
    function ceazar($ch){
        return ($ch+4)%26;
       }
    function shift($key, $ch){
        $WIDTH = 26;
        $ASC_A = ord('A');
        $DSC_A = ord('a');
        $list = "0123456789";
        $Num=ord(0);
        if (ctype_alpha($ch)){
            if (ctype_upper($ch)){
                $offset = ord($ch) - $ASC_A;
                return chr((($key[0] * $offset + $key[1]) % $WIDTH) + $ASC_A);
            } else {
                $offset = ord($ch) - $DSC_A;
                return chr((($key[0] * $offset + $key[1]) % $WIDTH) + $DSC_A);
            }
        } 
        if (ctype_alnum($ch)){
          return  $list[ (strpos($list,ord($ch))+5)%10];
        } 
        else {
            return $ch;
        }
    }
    function encrypt($key, $words){
        $tmp = "";
        for ($i=0; $i < strlen($words); $i++){
            $tmp .= shift($key, $words[$i]);
        }

        return $tmp;
    }
    
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        // $key = [5, 8];
        $a=mt_rand(0,25);
        $a1=0;
        while($a%2==0||$a==13){
            $a=mt_rand(0, 25);
        }
        $a1=ceazar($a);
        $b=mt_rand(0,25);
        $b1=ceazar($b);
        if($a%2!=0)
        $ci=[$a,$b];
        $message =encrypt($ci,$message);
        if(!empty($message)){
            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, ci_a, ci_b)
                                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}',{$a1},{$b1})") or die();
        }
    }else{
        header("location: ../login.php");
    }    
?>