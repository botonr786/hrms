<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="icon" href="{{ asset('img/favicon.png')}}" type="image/x-icon"/>
<title>Job Offer Letter</title>
</head>

<body>
<table style="width:100%;margin:0 auto;border:1px solid #000;font-family:calibri;padding: 0 0;line-height: 26px;font-size: 19px;">

<thead>
  <tr>
    <th style="text-align:left;width: 89px;padding-bottom: 65px;"><img src="https://workpermitcloud.co.uk/hrms/public/{{ $com_logo }}" alt="" style="width:100px"/></th>
	<th style="text-align: center;font-size: 20px;padding-bottom: 65px; padding-top:15px"><span style="font-size:30px">{{ $com_name }}</span><br />{{ $address }}<br />{{$addresssub}}</th>
  </tr>
</thead>

<tbody>
<tr>
  <td style="padding-bottom:65px;padding-left:15px" colspan="2">Sub: Job Offer Letter</td>
</tr>

  <tr>
    <td colspan="2" style="padding-left:15px">
	 {{ date('d/m/Y',strtotime($date)) }}
	</td>
  </tr>
  <tr>
    <td colspan="2" style="padding-left:15px">
	  <b>{{ $name }} </b>
	</td>
  </tr>
   <tr>
    <td colspan="2" style="padding-left:15px">
	 {{ $job_title }}
	</td>
  
   <tr>
    <td colspan="2" style="padding-left:15px">
	  Dear {{ $name }},
	  
	</td>
  </tr>
  <tr>
    <td colspan="2" style="padding-left:15px">
	 <p> We are pleased to offer the position of {{ $job_title }} at {{ $com_name }}. We feel confident that you will contribute your skills and experience towards the growth of our organization.</p>
	  
	  <p> As per the discussion your starting date will be  {{ date('d/m/Y',strtotime($st_date)) }}. Please find the employee handbook attached here with which contains medical and retired benefit offered by our organization.</p>
	  
	  <p>Please confirm your acceptance of this offer by signing and returning the copy of this letter</p>
	  <p>We look forward to welcoming you on board.</p>
	</td>
  </tr>
  <tr>
    <td colspan="2" style="padding-left:15px"><p>Sincerely,<br> for {{ $com_name }} <br /><br /><br /><br /> {{ $em_name }}<br />{{ $job_title }}</p></td>
</tr>
</tbody>
</table>
</body>
</html>
