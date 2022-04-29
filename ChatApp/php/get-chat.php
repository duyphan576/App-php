<?php 
   function ceasar($ch){
    $list = "0123456789";
    // echo strpos($list,(string)ord($ch));
    return $list[ (strpos($list,(string)$ch)-5)%10];
   }
   function ceazar($ch){
    return ($ch-4+26)%26;
   }
   function decryption($CTxt,$a,$b)
    {
        $Msg = "";
        $WIDTH = 26;
        $key = [ceazar($a),ceazar($b)];
        $a_inv = 0;
        $num_inv=0;
        $flag = 0;
        $ASC_A = ord('A');
        $DSC_A = ord('a');
        $Num=ord('0');   
        for ($i = 0; $i < $WIDTH; $i++)
        {
            $flag = ($key[0] * $i) % $WIDTH;
            if ($flag == 1)
            {
                $a_inv = $i;
            }
        }
        for ($i = 0; $i < strlen($CTxt); $i++)
        {   
            if (is_numeric($CTxt[$i]))
            {   
                $Msg .=ceasar($CTxt[$i]);
            }  
            if (ctype_alpha($CTxt[$i])){
                if (ctype_upper($CTxt[$i]))
                {
                    $offset = ord($CTxt[$i]) - $ASC_A;
                    $g=$a_inv * (($offset - $key[1])) % $WIDTH;
                    if($g>=0) $Msg .=chr(((($g)) +$ASC_A));
                    else{
                    $g=$g+26; 
                    $Msg .=chr(((($g)) +$ASC_A));
                    }
                }
                else {
                    $offset = ord($CTxt[$i]) - $DSC_A;
                    $g=$a_inv * (($offset - $key[1])) % $WIDTH;
                    if($g>=0) $Msg .=chr(((($g)) +$DSC_A));
                    else{
                        $g=$g+26; 
                        $Msg .=chr(((($g)) +$DSC_A));
                    }
                }
            }          
            if(!is_numeric($CTxt[$i])&& !ctype_alpha($CTxt[$i])) {
               $Msg .=$CTxt[$i];
            }
        }
        return $Msg;
    }
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $a=$row['ci_a'];
                    $b=$row['ci_b'];
                    // $ci=[$a,$b];
                    $text=mysqli_real_escape_string($conn,$row['msg']);
                    $text=decryption($text,$a,$b);
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'.$text.'</p>
                                </div>
                                </div>';
                }else{
                    $a=$row['ci_a'];
                    $b=$row['ci_b'];
                    $text=mysqli_real_escape_string($conn,$row['msg']);
                    $text=decryption($text,$a,$b);
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'. $text .'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../index.php");
    }

?>