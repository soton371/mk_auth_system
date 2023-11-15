<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="background-color: #f5f5f5;">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; margin-top: 20px; border-radius: 10px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <h1>Email Verification</h1>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <p>Hello User,</p>
                            <p>Thank you for registering with MK mining. To complete your registration, please use the following verification code:</p>
                            <h2 style="background-color: #ffd600;
                            width: 64px;
                            padding: 5px;
                            border-radius: 5px;">{{ $details['code'] }}</h2>
                            <p>Please enter this code on our website to verify your email address.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <p>If you did not register with MK mining, please ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <p>Best Regards,</p>
                            <p>The MK mining Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>