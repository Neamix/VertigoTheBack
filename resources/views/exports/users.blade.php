<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Active Hours</th>
        <th>Idle Hours</th>
        <th>Meeting Hours</th>
        <th>Total Hours</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->active_hours }}</td>
            <td>{{ $user->idle_hours }}</td>
            <td>{{ $user->meeting_hours }}</td>
            <td>{{ $user->total_hours }}</td>
        </tr>
    @endforeach
    </tbody>
</table>