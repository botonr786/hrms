<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HRMS</title>
<style>
@media print {
   body {
      -webkit-print-color-adjust: exact;
   }
}
tr:nth-child(even) {
    background-color: #d0e5ff;
}
</style>
</head>

<body style="webkit-print-color-adjust: exact; ">

<table style="width:100%;font-family:cambria">
 <thead>
  <tr>
   <th style="text-align: left;width: 10%;">@if($com_logo!='') <img src="https://workpermitcloud.co.uk/hrms/public/{{ $com_logo }}" alt="" width="100"/> @endif</th>
		<th style="width:90%"><h2 style="font-size: 30px;    margin-bottom: 0;">{{ $com_name }}</h2>
	 <p style="margin:0;font-size:15px">{{ $address }}<br />{{$addresssub}}</p>

	  <p style="margin:0;font-size:20px">Leave Balance Report</p>
	</th>
  </tr>
 </thead>
 
 
</table>
  <?php $i=0; foreach($employee_rs as $ls){ $i++;?>
<table style="width:100%;margin-top: 50px;">
<tr>
<td>Employee Code: <?php echo $ls->emp_code; ?></td>

<td style="text-align:right">Employee Name: <?php echo $ls->emp_fname; ?> <?php echo $ls->emp_mname; ?> <?php echo $ls->emp_lname; ?>	</td>
</tr>
</table>
<table border="1" style="width:100%;font-family:cambria;border-collapse:collapse;margin-top:30px">
<thead style="background: #1572e8;">
  <tr>
    <th  style="color:#fff">Sl No.</th>
	 <th  style="color:#fff">Leave Type</th>
	 <th  style="color:#fff">Balance in Hand</th>
	 
  </tr>
 </thead>
<tbody>
     <?php 
     
      $leave_type=DB::table('leave_type')->orderBy('id','ASC')->where('emid','=',$emid)->get();
      $p=1;
     foreach($leave_type as $leave_name){
     
     
     ?>
					<tr>
    <td>{{$p}}</td>
	<td><?php echo $leave_name->leave_type_name; ?></td>
<td><?php 
        $leavetype=DB::Table('leave_allocation')
          ->where('leave_type_id', '=', $leave_name->id)
		     ->where('employee_code', '=', $ls->emp_code)
        ->whereYear('created_at', '=', date('Y'))
        
		->orderBy('id','desc')
        ->first();
if(!empty($leavetype)){
	echo $leavetype->leave_in_hand;
}else{
	echo '0';
}

		?></td>
	
  </tr>
					<?php $p++; } ?>
  
  
</tbody>
</table>
 <?php } ?>



</body>
</html>
