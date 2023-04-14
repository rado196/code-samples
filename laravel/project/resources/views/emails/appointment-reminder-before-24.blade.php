<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Appointment reminder</title>
</head>

<body>
    <p>
        Հարգելի օգտատեր, սույնով հիշեցնում ենք, որ վաղը` <strong>{{ $appointmentDate }}</strong> ժամը`
        <strong>{{ $startTime }}</strong> դուք ամրագրել եք ավտովարման դասընթաց
        «{{ config('custom.app_company_display_name') }}» ավտոդպրոցում՝
        Գլինկայի 3 հասցեում!
    </p>

    <p><strong>Հրահանգիչ`</strong> {{ $instructorFullName }}</p>

    <p>Չներկայանալու դեպքում դասը կհամարվի կատարված!</p>

    <p>Հարցերի դեպքում զանգահարել` <strong>098265050</strong></p>

    <br />
    Հարգանքներով, «{{ config('custom.app_company_display_name') }}» Ավտոդպրոց
</body>

</html>
