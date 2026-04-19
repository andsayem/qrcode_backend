<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>{{ $mailReceiverName }}</title>
</head>
<body class="">
<p>Dear <strong>{{ $mailReceiverName }}</strong>,</p>
<br/>
{!! $body !!}
<br/><br/>
<p style="margin-top:0px; margin-bottom: 0px;">Thank you,</p>
<p style="margin-top:0px; margin-bottom: 0px;">SSG</p>
<br>
<p style="font-size: 9px; text-align: justify; text-justify:inter-word; color: #CC0000;">
    This e-mail message including any attachment may contain privileged and confidential information. If you are the
    intended recipient, you are hereby notified that any disclosure, copying, distribution, or the taking of any action
    in reliance on the contents of this emailed information is strictly prohibited. If you are not the intended
    recipient, please (i) do not go through this e-mail, (ii) do not forward, print, copy or otherwise disseminate this
    e-mail, (iii) notify me of the error by a reply to this e-mail, and (iv) delete this e-mail from your computer.
</p>
</body>
</html>
