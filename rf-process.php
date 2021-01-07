<?php
error_reporting(0);
session_start();
$staff_department =$_SESSION['staff_department'];
date_default_timezone_set('Asia/Manila');
include('config.php');
$now = new DateTime();
$now = $now->format('Y-m-d h:i:s');
$type = $_POST['type'];
#$id = (isset($id))?$_POST['id'];
$result = mysqli_query($con, "SET NAMES utf8")or die(mysqli_error($con));
if($type=='prev') {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $license = $_POST['license'];
    
    $select = mysqli_query($con, "SELECT * FROM `tb_md_rf` WHERE pf_from_date='$from' AND pf_to_date='$to' AND d_license='$license'") or die(mysqli_error($con));
    #$select = mysqli_query($con, "SELECT * FROM `tb_md_rf` WHERE d_license='$license'") or die(mysqli_error($con));
    $result = array();
    $td = '';
    $total = 0;
    $sumnetov = 0;
    $sumwtax = 0;
    $sumnetotal = 0;
    while ($row =mysqli_fetch_assoc($select)) {
        $id = $row['id'];
        $patient_name = str_replace("ñ", "Ñ", $row['patient_name']);
        $pf_amount = str_replace(",", "",$row['pf_amount']);
        $pf_trans_date = $row['pf_trans_date'];
        $pf_from_date = $row['pf_from_date'];
        $pf_to_date = $row['pf_to_date'];
        $pf_guarantor = $row['pf_guarantor'];
        $d_license = $row['d_license'];
        #$encoder = $row['encoder'];
        $status = $row['status'];
        $total+= floatval($pf_amount);
        $newpf_amount = floatval($pf_amount);
        $doc = $doc = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_license='$d_license'") or die(mysqli_error($con));
            while ($row =mysqli_fetch_assoc($doc)) {
                #$d_userid = $row['d_userid'];
                $d_email = $row['d_email'];
                $fullname = str_replace("ñ", "Ñ", $row['d_fullname']);
                $d_vat = $row['d_vat'];
                $d_pf = $row['d_pf'];
                $d_wtax = $row['d_wtax'];
                $d_tax = $row['d_tax'];
                
               # $fullname = ucwords($d_lname.', '.$d_fname.' '.$d_mname);
                $result[] = '<p>'.$d_email.'</p>';
                    if($d_wtax=='0'){
                        $d_wtax =round('0.1',2);
                    }else{
                        $d_wtax =round($d_wtax*'.100',2);
                    }
                    if($d_tax=='0'){
                        $d_tax =round('0.1',2);
                    }else{
                        $d_tax = round($d_tax*'.100', 2);
                    } 

                    if($d_vat == 'Y') {
                        $netov = round($newpf_amount / 1.12, 2);
                        $fomatednetov = number_format($netov, 2, '.', ',');
                        $wtax = round($netov * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    } else {

                        $netov = '-';
                        $fomatednetov = $netov;
                        $wtax = round($newpf_amount * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    }
            }
            if($staff_department =='Hospital Information System') {
                $th = '<th>Action</th>';
                $trash = '<td style="border:1px solid #000;"><button class="btn btn-light pt-0 pb-0 pl-1 pr-1 btn-block text-danger text-left delete" data-id="'.$id.'" data-pcfrom="'.date('m/j/Y', strtotime($from)).'" data-pcto="'.date('m/j/Y', strtotime($to)).'" data-license="'.$d_license.'" data-name="'.trim($patient_name).'"><i class="far fa-trash-alt"></i> DELETE</button></td>';
            } else {
                $th ='';
                $trash = '';
            }  
        $td.= '<tr><td style="border:1px solid #000;">'.$patient_name.'</td><td style="border:1px solid #000;">'.number_format($newpf_amount, 2, '.', ',').'</td><td style="border:1px solid #000;">'.$fomatednetov.'</td><td  style="border:1px solid #000;">'.number_format($wtax, 2, '.', ',').'</td><td style="border:1px solid #000;">'.number_format($netotal, 2, '.', ',').'</td>'.$trash.'</tr>';
        $sumnetov+= $netov;
        $sumwtax+= $wtax;
        $sumnetotal+= $netotal;
    }
   
    $td .='';
    $msg ="<img src='assets/images/lh.png' style='width:100%;margin-bottom:3%;' id='logo-header'>
    <table class='w-100 table-bordered-bd-dark table-head-bg-info' id='tableprint' style='border:1px solid #000;'>
    <tr class='bg-primary text-white text-center'><th colspan='6'>MIHMCI PF - <span class='mb-1' id='lic' data-lic='".$d_license."'>Dr. ".$fullname." </span></th></tr>
    <tr><th colspan='6' style='text-align:center;border:1px solid #000;'>Period Covered <span id='pcfrom' data-pcfrom='".date('m/j/Y', strtotime($from))."'>".date('F d , Y', strtotime($from))."</span> to <span id='pcto' data-pcto='".date('m/j/Y', strtotime($to))."'>".date('F d , Y', strtotime($to))."</span></th></tr>
    <tr>
    <th style='border:1px solid #000;'>Patient Name</th>
    <th style='border:1px solid #000;'>Amount</th>
    <th style='border:1px solid #000;'>Net of VAT</th>
    <th style='border:1px solid #000;'>W/Tax</th>
    <th style='border:1px solid #000;'>Net Pay</th>
    ".$th."
    </tr>".$td."
    <tr><td colspan='' style='border:1px solid #000;'><b>Total</b></td>
    <td style='border:1px solid #000;'>".number_format($total, 2, '.', ',')."</td><td style='border:1px solid #000;'>".number_format($sumnetov, 2, '.', ',')."</td><td style='border:1px solid #000;'>".number_format($sumwtax, 2, '.', ',')."</td><td colspan='2' style='border:1px solid #000;'>".number_format($sumnetotal, 2, '.', ',')."</td></tr>
    </table>
    <div class='prep' style='position:fixed:bottom:0;color:#000;'><p>Prepared By:</p><p style='border-top:1px solid #000;margin-bottom:0;width:25%;text-align:center;'>Irene Geamala Fille</p><p style='margin-top:0;width:25%;text-align:center;'><small>Accounting Assistant</small></p></div>
    <div class='app' style='width:100%;color:#000;'><p>Approved By:</p><p style='border-top:1px solid #000;margin-bottom:0;width:25%;text-align:center;'>Marivic Padpad Guevara</p><p style='margin-top:0;width:25%;text-align:center;'><small>Accounting Head</small></p></div>
    ";
    echo $msg;
    die();

}elseif($type=='add'){

    $license = $_POST['license'];
    $patientname = $_POST['patientname'];
    $transdate = date('m/j/Y', strtotime($_POST['transdate']));
    $amount = $_POST['amount'];
    $pcfrom = $_POST['pcfrom'];
    $pcto = $_POST['pcto'];
    $insert = mysqli_query($con, "INSERT INTO `tb_md_rf`(`patient_name`, `pf_amount`, `pf_trans_date`, `pf_from_date`, `pf_to_date`, `d_license`) VALUES ('$patientname', '$amount', '$transdate', '$pcfrom', '$pcto', '$license')") or die(mysqli_error($con));
    if($insert) {
        echo '1';
        die();
    } else {
       
    }

}elseif($type=='edit'){
    $id = $_POST['id'];
    $select = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_id='$id'") or die(mysqli_error($con));
        while($row=mysqli_fetch_assoc($select)) {
            $id = $row['d_id'];
            $d_fullname = $row['d_fullname'];
            $d_license = $row['d_license'];
            $d_email = $row['d_email'];
            $d_vat = $row['d_vat'];
            $d_wtax = $row['d_wtax'];
            $d_tax = $row['d_tax'];
            $percent = ($d_vat=='Y')?$d_wtax:$d_tax;
            $d_vatx=($d_vat=='Y')?'VAT':'NON-VAT';
            echo '<form method="POST" id="form"><div class="row no-gutters"><div class="col-6"><div class="form-group"><p class="text-dark mb-0">Doctor</p><input class="form-control" type="text" name="doctorname" value="'.$d_fullname.'"></div></div><div class="col-6"><div class="form-group"><p class="text-dark mb-0">Email</p><input class="form-control" type="text" name="email" value="'.$d_email.'"></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">License</p><input class="form-control" type="text" name="license" value="'.$d_license.'"></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Type</p><select class="form-control" name="type"><option value="'.$d_vat.'">'.$d_vatx.'</option><option value="Y">VAT</option><option value="0">NON-VAT</option></select></div></div><div class="col"><div class="form-group"><p class="text-dark mb-0">Percent</p><select class="form-control" name="percent"><option value="'.$percent.'">'.$percent.'%</option><option value="5">5%</option><option value="10">10%</option></select></div></div><div class="col-2"><div class="form-group"><p class="text-dark mb-0" style="opacity:0;">.</p><button class="btn btn-primary btn-block save" data-id="'.$id.'">Save</button></div></div></div></div></div></form>';
            die();
        }
}elseif($type=='update'){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $license = $_POST['license'];
    $tax = $_POST['tax'];
    $percent = $_POST['percent'];
    if($tax=='Y') {
        $stmt = ", d_wtax='$percent', d_tax='0'";
    }else{
        $stmt = ", d_wtax='0', d_tax='$percent'";  
    }
        $update = mysqli_query($con, "UPDATE `tb_pf_dr` SET `d_fullname`='$name',`d_license`='$license',`d_email`='$email',`d_vat`='$tax' $stmt WHERE d_id='$id'") or die(mysqli_error($con));
        if($update){
            echo '1';
        }else{

        }
           
}elseif($type=='delete'){

    $id = $_POST['id'];
    $insert = mysqli_query($con, "DELETE FROM `tb_md_rf` WHERE id='$id'") or die(mysqli_error($con));

}elseif($type=='sendgroup') { 

    $from = $_POST['from'];
    $to = $_POST['to'];
    $license = $_POST['license'];
    
    $select = mysqli_query($con, "SELECT * FROM `tb_md_rf` WHERE pf_from_date='$from' AND pf_to_date='$to' AND d_license='$license'") or die(mysqli_error($con));
    $result = array();
    $td = '';
    $total = 0;
    $sumnetov = 0;
    $sumwtax = 0;
    $sumnetotal = 0;
    while ($row =mysqli_fetch_assoc($select)) {
        /*$id = $row['id'];
        $patient_name = str_replace("ñ", "Ñ", $row['patient_name']);
        $pf_amount = $row['pf_amount'];
        $pf_trans_date = $row['pf_trans_date'];
        $pf_from_date = $row['pf_from_date'];
        $pf_to_date = $row['pf_to_date'];
        $pf_quarantor = $row['pf_quarantor'];
        $d_license = $row['d_license'];
        $encoder = $row['encoder'];
        $status = $row['status'];
        $total+= intval($pf_amount);
        $newpf_amount = round(intval($pf_amount), 2);*/
        $id = $row['id'];
        $patient_name = str_replace("ñ", "Ñ", $row['patient_name']);
        $pf_amount = str_replace(",", "",$row['pf_amount']);
        $pf_trans_date = $row['pf_trans_date'];
        $pf_from_date = $row['pf_from_date'];
        $pf_to_date = $row['pf_to_date'];
        #$pf_guarantor = $row['pf_guarantor'];
        $d_license = $row['d_license'];
        #$encoder = $row['encoder'];
        $status = $row['status'];
        $total+= floatval($pf_amount);
        $newpf_amount = floatval($pf_amount);
    
        $doc = mysqli_query($con, "SELECT * FROM tb_pf_dr WHERE d_license='$d_license'") or die(mysqli_error($con));
            while ($row =mysqli_fetch_assoc($doc)) {
                $d_userid = $row['d_userid'];
                $d_email = $row['d_email'];
                $fullname = str_replace("ñ", "Ñ", $row['d_fullname']);
                $d_vat = $row['d_vat'];
                $d_pf = $row['d_pf'];
                $d_wtax = $row['d_wtax'];
                $d_tax = $row['d_tax'];
                
                #$fullname = ucwords($d_lname.', '.$d_fname);
                $result[] = '<p>'.$d_email.'</p>';
                    /*if($d_wtax=='0'){
                        $d_wtax =round('0.1',2);
                    }else{
                        $d_wtax =round($d_wtax*'.100',2);
                    }
                    if($d_tax=='0'){
                        $d_tax =round('0.1',2);
                    }else{
                        $d_tax = round($d_tax*'.100', 2);
                    }

                    if($d_vat == 'Y') {

                        $netov = round($newpf_amount / 1.12, 2);
                        $fomatednetov = number_format($netov, 2, '.', ',');
                        $wtax = round($netov * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    } else {

                        $netov = '-';
                        $fomatednetov = $netov;
                        $wtax = round($newpf_amount * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    }*/
                    if($d_wtax=='0'){
                        $d_wtax =round('0.1',2);
                    }else{
                        $d_wtax =round($d_wtax*'.100',2);
                    }
                    if($d_tax=='0'){
                        $d_tax =round('0.1',2);
                    }else{
                        $d_tax = round($d_tax*'.100', 2);
                    } 

                    if($d_vat == 'Y') {
                        $netov = round($newpf_amount / 1.12, 2);
                        $fomatednetov = number_format($netov, 2, '.', ',');
                        $wtax = round($netov * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    } else {

                        $netov = '-';
                        $fomatednetov = $netov;
                        $wtax = round($newpf_amount * $d_tax * $d_wtax, 2);
                        $netotal = $newpf_amount - $wtax;
                        
                    }
            }
    $td.= '<tr><td style="border: 1px solid #06418e;color:#000;font-size:24px;">'.$patient_name.'</td><td style="border: 1px solid #06418e;color:#000;font-size:24px;">'.number_format($newpf_amount, 2, '.', ',').'</td><td style="border: 1px solid #06418e;color:#000;font-size:24px;">'.$fomatednetov.'</td><td style="border: 1px solid #06418e;color:#000;font-size:24px;">'.number_format($wtax, 2, '.', ',').'</td><td style="border: 1px solid #06418e;color:#000;font-size:24px;">'.number_format($netotal, 2, '.', ',').'</td></tr>';
    $sumnetov+= $netov;
    $sumwtax+= $wtax;
    $sumnetotal+= $netotal;
           
    }//first while loop
    $td .='';
    //emailscript
    $img="<img src='https://www.metroiloilohospital.com/maillong.png' width='100%'>";
    $msg="<!DOCTYPE html>
    <html lang='en'>
    <head>
      <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>MIHMCI RF Period Covered ".date('F Y', strtotime($from))."</title>
        <style>
        @media screen and (max-width: 480px) {
            #etable {
                width:280px !important;
            }
            #etable caption {
                font-size:16px;
            }
        }
        </style>  
    </head>  
    <body>
      ".$img."
    <table style='border: 1px solid #06418e;width:100%;border-collapse: collapse;' id='etable'>
    <caption style='border: 1px solid #06418e;background:#06418e;color:#000;font-size:36px;'>MIHMCI RF - <span style='color:#000;font-size:36px;'>Dr. ".$fullname."</span></caption>
    <tr><th colspan='5' style='border: px solid #06418e;color:#000;font-size:24px;text-align:center;'>Period Covered ".date('F d , Y', strtotime($from))." to ".date('F d , Y', strtotime($to))."</th></tr>
    <tr>
    <th style='border: 1px solid #06418e;color:#000;font-size:24px;text-align:left;'>Patient Name</th>
    <th style='border: 1px solid #06418e;color:#000;font-size:24px;text-align:left;'>Amount</th>
    <th style='border: 1px solid #06418e;color:#000;font-size:24px;text-align:left;'>Net of VAT</th>
    <th style='border: 1px solid #06418e;color:#000;font-size:24px;text-align:left;'>W/Tax</th>
    <th style='border: 1px solid #06418e;color:#000;font-size:24px;text-align:left;'>Net Pay</th>
    </tr>".$td."
    <tr>
    <td style='border: 1px solid #06418e;color:#000;font-size:24px;'><b>Total</b></td>
    <td style='border: 1px solid #06418e;color:#000;font-size:24px;'>".number_format($total, 2, '.', ',')."</td>
    <td style='border: 1px solid #06418e;color:#000;font-size:24px;'>".number_format($sumnetov, 2, '.', ',')."</td>
    <td style='border: 1px solid #06418e;color:#000;font-size:24px;'>".number_format($sumwtax, 2, '.', ',')."</td>
    <td style='border: 1px solid #06418e;color:#000;font-size:24px;'>".number_format($sumnetotal, 2, '.', ',')."</td>
    </tr>
    </table>
    </body>
    </html>";
    $to_email      = "mjpalencia27@gmail.com";
    #$to_email      = $d_email;
    $subject = "MIHMCI RF Period Covered ".date('F Y', strtotime($from));
    $message = $msg;
    $headers = "From: admin\nMIME-Version: 1.0\nContent-Type: text/html; charset=utf-8\n";

    if(mail($to_email, $subject, $message, $headers)) {
        $update = mysqli_query($con, "UPDATE `tb_md_rf` SET `status`='sent' WHERE pf_from_date='$from' AND pf_to_date='$to' AND d_license='$d_license'") or die(mysqli_error($con));
            echo $from.' | '.$to.' | '.$d_license;
        //start archive
    $msg="
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>MIHMCI PF Mailer Archive - Readers Fee Period Covered ".date('F Y', strtotime($from))."</title>
    </head>  
    <body>
        <table style='width:100%;border-collapse: collapse;' id='etable'>
        <tr>
            <th style='border: 1px solid #000;color:#000;font-size:16px;text-align:left;'>Doctor</th>
            <th style='border: 1px solid #000;color:#000;font-size:16px;text-align:left;'>License</th>
            <th style='border: 1px solid #000;color:#000;font-size:16px;text-align:left;'>Subject</th>
            <th style='border: 1px solid #000;color:#000;font-size:16px;text-align:left;'>Date Sent</th>
            <tr>
            <td style='border: 1px solid #000;color:#000;font-size:14px;text-align:left;'>".$fullname."</td>
            <td style='border: 1px solid #000;color:#000;font-size:14px;text-align:left;'>".$d_license."</td>
            <td style='border: 1px solid #000;color:#000;font-size:14px;text-align:left;'>MIHMCI RF Period Covered ".date('F Y', strtotime($from))."</td>
            <td style='border: 1px solid #000;color:#000;font-size:14px;text-align:left;'>".$now."</td>
            </tr>
        </table>
    </body>
    </html>";
        $to_email      = "mihmci.his@gmail.com";
        $subject = "MIHMCI RF Mailer Archive - Readers Fee Period Covered ".date('F Y', strtotime($from));
        $message = $msg;
        $headers = "From: admin\nMIME-Version: 1.0\nContent-Type: text/html; charset=utf-8\n";
//end archive        
        if(mail($to_email, $subject, $message, $headers)) { 
            //email logs
            $ts = date('Y-m-d H:i:s');
            $logmsg = $subject.' | '.$fullname;

            $sqlmailer = mysqli_query($con, "SELECT * FROM tb_mailer WHERE rt_status='SENDER'") or die(mysqli_error($con));

            while($row=mysqli_fetch_array($sqlmailer)) {
                $senderemail=$row['rt_sender'];
            }

            $elogs = mysqli_query($con, "INSERT INTO `tb_elogs`(`rt_id`, `rt_logstatus`, `rt_logemail`, `rt_logmsg`, `rt_logtype`, `rt_mailer`, `rt_sender`, `rt_timestamp`) VALUES (NULL, 'SENT', '$d_email', '$subject', 'PF MAILER', '$senderemail', '', '$ts')") or die(mysqli_error($con));
            //end logs
            echo $ts;
            die();
        } else {
            //archive error
        }
    }else{
        //email error
    }  
} else {
    //do nothing
}

?>