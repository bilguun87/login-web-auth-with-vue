<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<form action='/permission/store' method='post'>
		@csrf
		<input type="text" name="permission-name">
		<input type="submit" value="Save">
	</form>

	@if (isset($permissions))
	<table style="border: 1px solid black;">
		<tr>
			<th>Permission Name</th>
			<th>Guard</th>
		</tr>
		@foreach ($permissions as $perm)
		<tr>
			<td>{{ $perm->name }}</td>
			<td>{{ $perm->guard_name }}</td>
		</tr>
		@endforeach
	</table>
	@endif
</body>
</html>