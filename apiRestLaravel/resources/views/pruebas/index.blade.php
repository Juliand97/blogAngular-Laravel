<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<p>{{$title}}</p>
	<ul>
		@foreach($language AS $tec)
			<li>{{$tec}}</li>
		@endforeach	
	</ul>

</body>
</html>