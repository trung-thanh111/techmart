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
            padding: 10px 20px;
            color: #333;
            line-height: 1.6;
        }

        .content h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #999999
        }

        .content p {
            font-size: 16px;
            margin-bottom: 15px;
            color: #555;
        }

        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .verify-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #00b88c 0%, #007c8b 100%);
            color: #fff !important;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            opacity: 0.9;
        }

        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 30px 0;
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-success {
            background: #e6f9f2;
            color: #0f766e;
            border: 1px solid #99f6e4;
        }

        .badge-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
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
            .email-container {
                width: 100% !important;
                border-radius: 0;
            }

            .verify-button {
                padding: 12px 30px;
                font-size: 14px;
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
                        @if (!isset($success) && !isset($error))
                            <h2>Xác thực Email</h2>
                            <p>Xin chào <strong>{{ $name ?? '[TÊN_NGƯỜI_DÙNG]' }}</strong>,</p>
                            <p>Cảm ơn bạn đã đăng ký tài khoản trên nền tảng của chúng tôi. Chúng tôi rất vui mừng được
                                chào đón bạn!</p>
                            <p>Để hoàn tất quá trình đăng ký và kích hoạt tài khoản của bạn, vui lòng nhấp vào nút bên
                                dưới để xác nhận địa chỉ email:</p>
                            <div class="button-container">
                                <a href="{{ $verificationUrl ?? '[LINK_XÁC_NHẬN]' }}" class="verify-button">
                                    XÁC NHẬN ĐĂNG KÝ
                                </a>
                            </div>
                            <div class="divider"></div>
                            <p class="text-center" style="font-size: 14px; color: #999999;">
                                *** <strong>Lưu ý:</strong> Link xác nhận này sẽ hết hạn sau <strong>1 giờ</strong>. Nếu
                                bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.
                            </p>
                        @endif

                        @if (isset($success) && $success)
                            <div class="text-center">
                                <span class="badge badge-success">Xác thực email thành công!</span>
                            </div>
                            <div class="button-container">
                                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/auth/login"
                                    class="verify-button">
                                    ĐĂNG NHẬP NGAY
                                </a>
                            </div>
                        @elseif (isset($error))
                            <div class="text-center">
                                <span class="badge badge-error">{{ $error }}</span>
                            </div>
                            <div class="button-container">
                                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/auth/register"
                                    class="verify-button" style="background: #b91c1c;">
                                    ĐĂNG KÝ LẠI
                                </a>
                            </div>
                        @endif
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
