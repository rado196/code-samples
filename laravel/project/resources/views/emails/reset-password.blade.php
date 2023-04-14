<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password reset</title>
</head>

<body>
    Ձեր գաղտնաբառը փոխելու հարցում է եղել: <br />

    Եթե դուք չեք կատարել այս հարցումը, ապա խնդրում ենք անտեսել այս նամակը: <br />

    Հակառակ դեպքում, ձեր գաղտնաբառը փոխելու համար խնդրում ենք սեղմել այստեղ՝
    <a href="{{ config('custom.web_app_url') . '/account/reset-password/' . $token }}">հղում</a>
    <br />
    Հարգանքներով, «{{ config('custom.app_company_display_name') }}» Ավտոդպրոց
</body>

</html>
