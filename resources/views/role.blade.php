<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<div>
	<form action="/roles/store" method="post">
		@csrf
		<input type="text" name="rolename" id="">
		<input type="submit" value="Save">
	</form>
	</div>
	
	@if (isset($roles) && count($roles) > 0)
		@if(isset($permissions) && count($permissions) > 0)
		<div style="margin: 10px;">
			<span>Permission assign to role</span>
			<form action="/roles/assign" method="post">
				@csrf
				<select name="role" id="">
					<option value="Choose Role" disabled selected></option>
					@foreach ($roles as $role)
						<option value="{{$role->id}}">{{ $role->name }}</option>
					@endforeach
				</select>
				<br>
				<select name="permission" id="">
					<option value="Choose Role" disabled selected></option>
					@foreach ($permissions as $permission)
						<option value="{{$permission->id}}">{{ $permission->name }}</option>
					@endforeach
				</select>
				<input type="submit" value="Assign">
			</form>
		</div>
		@endif
	<div style="margin: 10px;">
		<span>Role list</span>
		<table>
			<tr>
				<th>Role Name</th>
				<th>Guard</th>
			</tr>
			@foreach ($roles as $role)
			<tr>
				<td>{{ $role->name }}</td>
				<td>{{ $role->guard_name }}</td>
			</tr>
			@endforeach
		</table>
	</div>
	
	<div style="margin: 10px;">
		<span style="margin-top: 10px;">Role Permissions Relation</span>
		<table>
			<tr>
				<th>Role Name</th>
				<th>Permission Names</th>
			</tr>
			@foreach ($roles as $role)
				<tr>
					<td>{{ $role->name }}</td>
					<td>
						@if (count($role->permissions()->pluck('name')) > 0)
							@foreach ($role->permissions()->pluck('name') as $permname)
								{{ $permname }}
							@endforeach
						@endif
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	@endif
</body>
</html>