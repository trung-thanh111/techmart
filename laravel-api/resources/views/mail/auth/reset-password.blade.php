<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }

        .email-container {
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .content {
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .content h2 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #444;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            margin-bottom: 15px;
            color: #555;
        }

        .otp-box {
            display: inline-block;
            background: #f1f5f9;
            border: 2px dashed #00b88c;
            border-radius: 8px;
            padding: 15px 25px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #007c8b;
            margin: 20px auto;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .footer a {
            color: #00b88c;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .otp-box {
                font-size: 22px;
                letter-spacing: 6px;
                padding: 12px 20px;
            }
        }
    </style>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td>
                <div class="email-container">
                    <div class="content">
                        <h2>Đặt lại mật khẩu</h2>

                        <p>Xin chào <strong>{{ $name ?? 'Người dùng' }}</strong>,</p>
                        <p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản <strong>{{ $email ?? '' }}</strong>.</p>
                        <p>Để tiếp tục, vui lòng sử dụng mã xác thực bên dưới (hết hạn sau <strong>10 phút</strong>):
                        </p>

                        <div class="text-center">
                            <div class="otp-box">
                                {{ $otp ?? '123456' }}
                            </div>
                        </div>

                        <p class="text-center" style="font-size: 14px; color: #999;">
                            Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.
                        </p>
                    </div>

                    <div class="footer">
                        <p style="margin-top: 5px; font-size: 12px;">
                            © 2025 Techmart. All rights reserved.
                        </p>
                        <p style="font-size: 12px;">
                            <a href="[LINK_PRIVACY_POLICY]">Chính sách bảo mật</a> |
                            <a href="[LINK_TERMS]">Điều khoản sử dụng</a>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
