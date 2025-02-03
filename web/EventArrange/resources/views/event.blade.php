<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント詳細</title>
</head>
<body>
    <h1>{{ $event->name }}</h1>
    <p>{{ $event->detail }}</p>
    <h2>参加者と回答</h2>
    <table border="1">
        <tr>
            <th>名前</th>
            @foreach($event->availabilities as $availability)
                <th>{{ $availability->start_time }} - {{ $availability->end_time }}</th>
            @endforeach
        </tr>
        @foreach($event->users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                @foreach($event->availabilities as $availability)
                    @php
                        $response = $availability->responses->where('user_id', $user->id)->first();
                        $symbol = $response ? ['×', '△', '○'][$response->response] : '-';
                    @endphp
                    <td>{{ $symbol }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
