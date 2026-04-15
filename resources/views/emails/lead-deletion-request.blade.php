<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lead Deletion Request </title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5;">
    <h2>Hello 👋</h2>

    <p>
        We have received your deletion request!
    </p>

    <p>
        Below are the details of your deletion request!
    </p>

    <p>
        Click the link below to confirm your deletion request!
    </p>

    <p>
        <a href="{{ route('leads.deletion.confirm', ['token' => $token]) }}" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;">
            Confirm Deletion Request
        </a>
    </p>
</body>
</html>
