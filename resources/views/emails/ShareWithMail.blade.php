<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Image Cloud</title>
</head>
<body>
    <table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                <tbody>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi ,</p>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">{{ $details['name'] }} Share some Images with you</p>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Click link bellow or use Link to varify your Email.</p>
                        </td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{ url('user/UserLogin') }}" style="text-decoration:none;">
                                <strong style="color:#ffff;text-decoration:none;display:block;width: 13em;text-align:center;background:#47a2ea;padding:0.5em;font-size:20px;margin-left: 4em; ">Login Now</strong>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="line-height:10px" colspan="1">&nbsp;
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Did't have Account Create now using Link <a href="{{ url('user/register') }}">Create Now</a></p>
                        </td>
                    </tr>
                </tbody>
            </table>
</body>
</html>
