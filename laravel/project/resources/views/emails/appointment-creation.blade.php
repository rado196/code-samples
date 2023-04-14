<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Appointment creation</title>
</head>

<body>
    <p><strong>Ուսանող</strong> - {{ $student }}</p>
    <p><strong>Հրահանգիչ</strong> - {{ $instructor }}</p>
    <p><strong>Ամրագրման ամսաթիվ</strong> - {{ $appointmentDate }}</p>
    <p><strong>Ամրագրման ժամ</strong> - {{ '(' . $startTime . ' - ' . $endTime . ')' }}</p>

    <br />
    Հարգանքներով, «{{ config('custom.app_company_display_name') }}» Ավտոդպրոց
</body>

</html>
