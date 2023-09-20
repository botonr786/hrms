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
    <th style="text-align: left;">@if($com_logo!='') <img src="https://workpermitcloud.co.uk/hrms/public/{{ $com_logo }}" alt="" width="100"/> @endif</th>
		<th style=""><h2 style="font-size: 25px;    margin-bottom: 0;">{{ $com_name }}</h2>
	 <p style="margin:0;font-size:15px">{{ $address }}<br />{{$addresssub}}</p>

	  <p style="margin:0;font-size:20px">Leave Register Of {{ $year_value }}</p>
	</th>
  </tr>
 </thead>
 
 
</table>

<table border="1" style="width:100%;font-family:cambria;border-collapse:collapse;margin-top:30px">
<thead style="background: #1572e8;">
  <tr>
    <th rowspan="2" style="color:#fff;font-size:11px;">Sl No.</th>
	 <th rowspan="2" style="color:#fff;font-size:11px;">EMPLOYEE ID</th>
	 <th rowspan="2" style="color:#fff;font-size:11px;">EMPLOYEE NAME</th>
	 <th rowspan="2" style="color:#fff;font-size:11px;">DESIGNATION</th>
	 <th colspan="<?= count($leave_type);?>" style="color:#fff;font-size:11px;">LEAVE TYPE</th>
	 
  </tr>
  <tr>
      <?php foreach($leave_type as $leave_name){?>
					<th style="background: #1572e8;color: #fff;font-size:11px;"><?php echo $leave_name->leave_type_name; ?></th>
					<?php } ?>
  
  </tr>
 </thead>
<tbody>
    <?php $i=0; foreach($employeelist as $ls){ $i++;?>
  <tr>
    <td style="font-size:10px;"><?php echo $i; ?></td>
	<td style="font-size:10px;"><?php echo $ls->emp_code; ?></td>
	<td style="font-size:10px;"><?php echo $ls->emp_fname; ?> <?php echo $ls->emp_mname; ?> <?php echo $ls->emp_lname; ?></td>
	<td style="font-size:10px;"><?php echo $ls->designation; ?></td>

  </tr>
   <?php } ?>
  
</tbody>
</table>
</body>
</html>
