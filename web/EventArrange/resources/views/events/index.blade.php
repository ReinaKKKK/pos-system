<!-- resources/views/events/index.blade.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント一覧</title>
</head>
<body>
    <h1>イベント一覧</h1>
    <ul>
        @foreach ($events as $event)
            <li>
                <a href="{{ url('/events/' . $event->id) }}">{{ $event->name }}</a> - {{ $event->detail }}
            </li>
        @endforeach
    </ul>
</body>
</html>
