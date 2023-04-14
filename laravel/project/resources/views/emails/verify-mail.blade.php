<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Email</title>
</head>

<body>
    Բարև {{ $name }}
    Դուք գրանցել եք հաշիվ {{ config('custom.app_name') }} - ում, <br /> նախքան ձեր հաշիվն օգտագործելու
    հնարավորությունը,
    դուք պետք է
    հաստատեք, որ սա ձեր էլ․ հասցեն է՝ սեղմելով այստեղ՝ <a href="{{ \URL::to('/verification/' . $token) }}">հղում</a>
    <br />
    Հարգանքներով, «{{ config('custom.app_company_display_name') }}» Ավտոդպրոց

</body>

</html>
