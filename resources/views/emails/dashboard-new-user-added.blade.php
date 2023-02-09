<!DOCTYPE html>
<html>
<head>
    <style>
        /*! Email Template */
        .text-center {
            text-align: center !important;
        }
        .email-logo {  height: 40px; }
        .email-logo {  max-width:100% !important; }
        .email-wraper { background: #f5f6fa; font-size: 14px; line-height: 22px; font-weight: 400; color: #8094ae; width: 100%; }

        .email-wraper a { color: #333; word-break: break-all; }

        .email-wraper .link-block { display: block; }

        .email-ul { margin: 5px 0; padding: 0; }

        .email-ul:not(:last-child) { margin-bottom: 10px; }

        .email-ul li { list-style: disc; list-style-position: inside; }

        .email-ul-col2 { display: flex; flex-wrap: wrap; }

        .email-ul-col2 li { width: 50%; padding-right: 10px; }

        .email-body { width: 96%; max-width: 620px; margin: 0 auto; background: #ffffff; padding:30px 20px; margin-bottom:40px; }

        .email-success { border-bottom: #1ee0ac; }

        .email-warning { border-bottom: #f4bd0e; }

        .email-btn { background: #333; border-radius: 4px; color: #ffffff !important; display: inline-block; font-size: 13px; font-weight: 600; line-height: 44px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 0 30px; }

        .email-btn-sm { line-height: 38px; }

        .email-header, .email-footer { width: 100%; max-width: 620px; margin: 0 auto; padding:20px; }

        .email-logo { height: 40px; }

        .email-title { font-size: 13px; color: #333; padding-top: 12px; }

        .email-heading { font-size: 18px; color: #333; font-weight: 600; margin: 0; line-height: 1.4; }

        .email-heading-sm { font-size: 24px; line-height: 1.4; margin-bottom: .75rem; }

        .email-heading-s1 { font-size: 20px; font-weight: 400; color: #526484; }

        .email-heading-s2 { font-size: 16px; color: #526484; font-weight: 600; margin: 0; text-transform: uppercase; margin-bottom: 10px; }

        .email-heading-s3 { font-size: 18px; color: #333; font-weight: 400; margin-bottom: 8px; }

        .email-heading-success { color: #1ee0ac; }

        .email-heading-warning { color: #f4bd0e; }

        .email-note { margin: 0; font-size: 13px; line-height: 22px; color: #8094ae; }

        .email-copyright-text { font-size: 13px; }

        .email-social li { display: inline-block; padding: 4px; }

        .email-social li a { display: inline-block; height: 30px; width: 30px; border-radius: 50%; background: #ffffff; }

        .email-social li a img { width: 30px; }

        @media (max-width: 480px) { .email-preview-page .card { border-radius: 0; margin-left: -20px; margin-right: -20px; }
            .email-ul-col2 li { width: 100%; } 
        }
    </style>
</head>
<body>
<table class="email-wraper">
    <tbody><tr>
        <td class="py-5">
            <table class="email-header">
                <tbody>
                    <tr>
                        <td class="text-center pb-4">
                            <a href="#"><img class="email-logo" src="https://app.signupknoxville.com/images/logo.png" alt="logo"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="email-body">
                <tbody>
                    <tr>
                        <td class="px-3 px-sm-5 pt-3 pt-sm-5 pb-3">
                            <h2 class="email-heading text-center">Complete your account</h2>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 px-sm-5 pb-2 text-center">
                            <p>Hi {{ $data['user']['first_name'] }},</p>
                            <p>Click on the link below to set your password.</p>

                            <a href="{{ $data['APP_URL'] }}/reset-password/{{ $data['user']['forgot_password_code'] }}" class="email-btn">Set Password</a>
                            <p class="mb-4">If you did not make this request, please contact us or ignore this message.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
           
        </td>
    </tr>
</tbody>
</table>
</body>
</html>