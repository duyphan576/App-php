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
        $key = [$a,$b];
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
    while($row = mysqli_fetch_assoc($query)){
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);
        (mysqli_num_rows($query2) > 0) ? $result = decryption($row2['msg'],ceazar($row2['ci_a']),ceazar($row2['ci_b'])) : $result ="No message available";
        (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
        if(isset($row2['outgoing_msg_id'])){
            ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
        }else{
            $you = "";
        }
        ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
        ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

        $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                    <div class="content">
                    <img src="php/images/'. $row['img'] .'" alt="">
                    <div class="details">
                        <span>'. $row['fname']. " " . $row['lname'] .'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
    }
?>