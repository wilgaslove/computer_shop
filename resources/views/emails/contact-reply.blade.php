<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Re: {{ $contactMessage->subject }}</title>
</head>
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;padding:32px;">

        <p style="margin:0 0 16px;">Bonjour {{ $contactMessage->name }},</p>

        <div style="white-space:pre-line;line-height:1.6;">{{ $replyBody }}</div>

        <p style="margin:24px 0 0;">Cordialement,<br>L'équipe {{ config('app.name') }}</p>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:32px 0 16px;">

        <p style="margin:0 0 8px;font-size:13px;color:#6b7280;">Votre message initial :</p>
        <div style="white-space:pre-line;font-size:13px;color:#6b7280;border-left:3px solid #e5e7eb;padding-left:12px;">{{ $contactMessage->message }}</div>

        @if ($contactMessage->order)
            <p style="margin:16px 0 0;font-size:13px;color:#6b7280;">Commande concernée : {{ $contactMessage->order->reference }}</p>
        @endif
    </div>
</body>
</html>
