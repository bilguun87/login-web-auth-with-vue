<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<div style="margin: 10px;">
		<form action="/users/store" method="post">
			@csrf
			<input type="text" name="name" id="">
			<input type="submit" value="Create">
		</form>
	</div>

	@if (isset($users) && count($users) > 0)

	<div style="margin: 10px;">
		<form action="/users/assign" method="post">
			@csrf
			<select name="user" id="">
				<option value="" selected>Choose User</option>
				@foreach ($users as $user)
				<option value="{{ $user->id }}">{{ $user->name }}</option>
				@endforeach
			</select>
			<select name="role" id="">
				<option value="">Choose Role</option>
				@if (isset($roles) && count($roles) > 0)
					@foreach($roles as $role)
					<option value="{{ $role->id }}">{{ $role->name }}</option>
					@endforeach
				@endif
			</select>
			<input type="submit" value="Assign">
		</form>
	</div>
	<div style="margin: 10px;">
		<form action="/users/removerole" method="post">
			@csrf
			<select name="user" id="">
				<option value="">Choose User</option>
				@foreach ($users as $user)
				<option value="{{ $user->id }}">{{ $user->name }}</option>
				@endforeach
			</select>
			<input type="text" name="role" id="" placeholder="Write role name">
			<input type="submit" value="Remove Role">
		</form>
	</div>
	<div style="margin: 10px;">
		<span>User Role Relation</span>
		<table>
			<tr>
				<th>Name</th>
				<th>User</th>
				<th>Role</th>
			</tr>
			@foreach ($users as $user)
			<tr>
				<td>{{ $user->name }}</td>
				<td>{{ $user->domain }}</td>
				<td>
					@if (count($user->getRoleNames()) > 0)
						@foreach ($user->getRoleNames() as $role)
						{{ $role }}
						@endforeach
					@endif
				</td>
			</tr>
			@endforeach
		</table>
	</div>

	<div style="margin: 10px;">
		<span>User permission relation</span>
		<table>
			<tr>
				<th>Name</th>
				<th>User</th>
				<th>Permission</th>
			</tr>
			@foreach ($users as $user)
			<tr>
				<td>{{ $user->name }}</td>
				<td>{{ $user->domain }}</td>
				<td>
					@if (count($user->getRoleNames()) > 0)
						@foreach ($user->getPermissionsViaRoles() as $permission)
						{{ $permission->name }}
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